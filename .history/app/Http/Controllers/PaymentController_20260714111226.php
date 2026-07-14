<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Initiate FPX / eWallet payment for an existing order.
     */
    public function checkout($order_id): RedirectResponse
    {
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->findOrFail($order_id);

        if ($order->status === 'paid') {
            return redirect()
                ->route('checkout.success', $order->id)
                ->with('success', 'This order is already paid.');
        }

        if (! in_array($order->status, ['pending'], true)) {
            return redirect()
                ->route('customer.orders')
                ->with('error', 'This order cannot be paid at this time.');
        }

        return match (config('payment.gateway')) {
            'billplz' => $this->redirectToBillplz($order),
            default => $this->redirectToToyyibPay($order),
        };
    }

    /**
     * Browser return URL after the customer finishes on the gateway page.
     */
    public function status(Request $request): RedirectResponse
    {
        $orderId = $request->integer('order_id');

        $order = Order::query()
            ->where('user_id', Auth::id())
            ->findOrFail($orderId);

        $isPaid = match (config('payment.gateway')) {
            'billplz' => $request->boolean('paid'),
            default => (string) $request->input('status_id') === '1',
        };

        if ($isPaid || $order->status === 'paid') {
            return redirect()
                ->route('checkout.success', $order->id)
                ->with('success', 'Payment received. Thank you!');
        }

        if ((string) $request->input('status_id') === '2') {
            return redirect()
                ->route('customer.orders')
                ->with('info', 'Payment is still pending.');
        }

        return redirect()
            ->route('customer.orders')
            ->with('error', 'Payment was not completed.');
    }

    /**
     * Server-to-server webhook/callback.
     */
    public function webhook(Request $request): Response
    {
        Log::info('Payment webhook received', $request->all());

        return match (config('payment.gateway')) {
            'billplz' => $this->handleBillplzWebhook($request),
            default => $this->handleToyyibPayWebhook($request),
        };
    }

    // --- ToyyibPay Logic ---

    private function redirectToToyyibPay(Order $order): RedirectResponse
    {
        $config = config('payment.toyyibpay');
        $amountInSen = $this->toSen($order->final_amount);

        $response = Http::asForm()
            ->post("{$config['base_url']}/index.php/api/createBill", [
                'userSecretKey' => $config['secret_key'],
                'categoryCode' => $config['category_code'],
                'billName' => 'Order #' . $order->id,
                'billDescription' => 'AlfarhanWholesale order payment',
                'billPriceSetting' => 1,
                'billPayorInfo' => 1,
                'billAmount' => $amountInSen,
                'billReturnUrl' => route('checkout.payment.status', ['order_id' => $order->id]),
                'billCallbackUrl' => route('webhook.payment'),
                'billExternalReferenceNo' => (string) $order->id,
                'billTo' => $order->customer_name,
                'billEmail' => $order->customer_email ?? 'noemail@example.com',
                'billPhone' => $order->customer_phone ?? '0123456789',
                'billSplitPayment' => 0,
                'billPaymentChannel' => 2,
            ]);

        if (! $response->successful()) {
            Log::error('ToyyibPay createBill failed', ['body' => $response->body()]);
            return back()->with('error', 'Unable to connect to payment gateway.');
        }

        $payload = $response->json();
        $billCode = is_array($payload) ? ($payload[0]['BillCode'] ?? $payload['BillCode'] ?? null) : null;

        if (! $billCode) {
            return back()->with('error', 'Invalid response from payment gateway.');
        }

        $order->update(['payment_bill_code' => $billCode]);

        return redirect()->away("{$config['base_url']}/{$billCode}");
    }

    private function handleToyyibPayWebhook(Request $request): Response
    {
        $status = (string) $request->input('status');
        $billCode = (string) $request->input('billcode');
        $orderId = (int) $request->input('order_id');
        $amountInSen = (int) $request->input('amount');

        if ($status !== '1') return response('Ignored', 200);

        $order = Order::find($orderId) ?? Order::where('payment_bill_code', $billCode)->first();

        if (! $order) return response('Order not found', 404);

        if ($amountInSen !== $this->toSen($order->final_amount)) return response('Amount mismatch', 422);

        $this->markOrderAsPaid($order, $request->input('refno'));

        return response('OK', 200);
    }

    // --- Helpers ---

    private function toSen(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function markOrderAsPaid(Order $order, ?string $paymentRef = null): void
    {
        if ($order->status === 'paid') return;
        $order->update([
            'status' => 'paid',
            'payment_ref' => $paymentRef,
            'paid_at' => now(),
        ]);
    }
    
    // --- Billplz Logic (basic implementations) ---

    private function redirectToBillplz(Order $order): RedirectResponse
    {
        // Minimal implementation: if Billplz is configured, attempt to redirect to its frontend URL.
        $config = config('payment.billplz', []);

        $host = $config['base_url'] ?? null;
        $collectionId = $config['collection_id'] ?? null;

        if (! $host || ! $collectionId) {
            return back()->with('error', 'Billplz is not configured.');
        }

        // Construct a simple payment page URL. Projects integrating Billplz should replace
        // this with the proper API call to create a bill and obtain a redirect URL.
        $url = rtrim($host, '/') . '/bills?collection_id=' . urlencode($collectionId) . '&amount=' . $this->toSen($order->final_amount);

        return redirect()->away($url);
    }

    private function handleBillplzWebhook(Request $request): Response
    {
        Log::info('Billplz webhook received', $request->all());

        // Basic webhook handling: attempt to locate order by known fields.
        $reference = (string) $request->input('reference');
        $paid = $request->boolean('paid');

        $order = null;
        if ($reference !== '') {
            $order = Order::where('payment_ref', $reference)->first();
        }

        if (! $order) {
            // Try by id if present
            $orderId = $request->integer('order_id');
            $order = $orderId ? Order::find($orderId) : null;
        }

        if (! $order) {
            return response('Order not found', 404);
        }

        if ($paid) {
            $this->markOrderAsPaid($order, $reference ?: null);
            return response('OK', 200);
        }

        return response('Ignored', 200);
    }
}