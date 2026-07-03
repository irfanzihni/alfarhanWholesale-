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
     * Expects ?order_id=123 (or route-model binding).
     */
    public function checkout(Request $request): RedirectResponse
    {
        $order = Order::query()
            ->where('user_id', Auth::id())
            ->findOrFail($request->integer('order_id'));

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

        // ToyyibPay: status_id 1 = success, 2 = pending, 3 = failed
        // Billplz: paid = true/false
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
                ->with('info', 'Payment is still pending. We will update your order once it is confirmed.');
        }

        return redirect()
            ->route('customer.orders')
            ->with('error', 'Payment was not completed. You can try again from your orders page.');
    }

    /**
     * Server-to-server webhook/callback from the gateway (no CSRF).
     */
    public function webhook(Request $request): Response
    {
        Log::info('Payment webhook received', $request->all());

        return match (config('payment.gateway')) {
            'billplz' => $this->handleBillplzWebhook($request),
            default => $this->handleToyyibPayWebhook($request),
        };
    }

    // -------------------------------------------------------------------------
    // ToyyibPay
    // -------------------------------------------------------------------------

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
                'billPaymentChannel' => 2, // 0=FPX, 1=Credit Card, 2=Both FPX & eWallet
            ]);

        if (! $response->successful()) {
            Log::error('ToyyibPay createBill failed', ['body' => $response->body()]);
            return back()->with('error', 'Unable to connect to payment gateway. Please try again.');
        }

        $payload = $response->json();
        $billCode = is_array($payload) ? ($payload[0]['BillCode'] ?? $payload['BillCode'] ?? null) : null;

        if (! $billCode) {
            Log::error('ToyyibPay missing BillCode', ['body' => $response->json()]);
            return back()->with('error', 'Payment gateway returned an invalid response.');
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

        if ($status !== '1') {
            return response('Ignored', 200);
        }

        $order = Order::find($orderId)
            ?? Order::where('payment_bill_code', $billCode)->first();

        if (! $order) {
            Log::warning('ToyyibPay webhook: order not found', $request->all());
            return response('Order not found', 404);
        }

        if ($amountInSen !== $this->toSen($order->final_amount)) {
            Log::warning('ToyyibPay webhook: amount mismatch', [
                'order_id' => $order->id,
                'expected' => $this->toSen($order->final_amount),
                'received' => $amountInSen,
            ]);
            return response('Amount mismatch', 422);
        }

        $this->markOrderAsPaid($order, $request->input('refno'));

        return response('OK', 200);
    }

    // -------------------------------------------------------------------------
    // Billplz (alternative gateway — switch via PAYMENT_GATEWAY=billplz)
    // -------------------------------------------------------------------------

    private function redirectToBillplz(Order $order): RedirectResponse
    {
        $config = config('payment.billplz');
        $amountInSen = $this->toSen($order->final_amount);

        $response = Http::withBasicAuth($config['api_key'], '')
            ->asForm()
            ->post("{$config['base_url']}/bills", [
                'collection_id' => $config['collection_id'],
                'email' => $order->customer_email ?? 'noemail@example.com',
                'mobile' => $order->customer_phone,
                'name' => $order->customer_name,
                'amount' => $amountInSen,
                'description' => 'Order #' . $order->id,
                'reference_1_label' => 'Order ID',
                'reference_1' => (string) $order->id,
                'callback_url' => route('webhook.payment'),
                'redirect_url' => route('checkout.payment.status', ['order_id' => $order->id]),
            ]);

        if (! $response->successful()) {
            Log::error('Billplz create bill failed', ['body' => $response->body()]);
            return back()->with('error', 'Unable to connect to payment gateway. Please try again.');
        }

        $bill = $response->json();
        $order->update(['payment_bill_code' => $bill['id']]);

        return redirect()->away($bill['url']);
    }

    private function handleBillplzWebhook(Request $request): Response
    {
        if (! $this->verifyBillplzSignature($request->all())) {
            Log::warning('Billplz webhook: invalid signature', $request->all());
            return response('Invalid signature', 403);
        }

        $billId = (string) $request->input('id');
        $paid = $request->input('paid') === 'true';
        $orderId = (int) $request->input('reference_1');
        $amountInSen = (int) $request->input('amount');

        if (! $paid) {
            return response('Ignored', 200);
        }

        $order = Order::find($orderId)
            ?? Order::where('payment_bill_code', $billId)->first();

        if (! $order) {
            return response('Order not found', 404);
        }

        if ($amountInSen !== $this->toSen($order->final_amount)) {
            return response('Amount mismatch', 422);
        }

        $this->markOrderAsPaid($order, $billId);

        return response('OK', 200);
    }

    private function verifyBillplzSignature(array $payload): bool
    {
        $key = config('payment.billplz.x_signature_key');

        if (! $key) {
            return false;
        }

        $signature = $payload['x_signature'] ?? '';
        unset($payload['x_signature']);

        ksort($payload);
        $signString = implode('|', array_map(
            fn ($k, $v) => $k . $v,
            array_keys($payload),
            array_values($payload)
        ));

        return hash_equals($signature, hash_hmac('sha256', $signString, $key));
    }

    // -------------------------------------------------------------------------
    // Shared helpers
    // -------------------------------------------------------------------------

    /**
     * Convert RM (decimal) to sen (integer) as required by Malaysian gateways.
     * RM 12.50 → 1250
     */
    private function toSen(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function markOrderAsPaid(Order $order, ?string $paymentRef = null): void
    {
        if ($order->status === 'paid') {
            return;
        }

        $order->update([
            'status' => 'paid',
            'payment_ref' => $paymentRef,
            'paid_at' => now(),
        ]);
    }
}
