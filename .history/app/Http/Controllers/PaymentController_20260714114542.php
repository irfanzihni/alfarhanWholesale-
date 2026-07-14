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
            default   => $this->redirectToToyyibPay($order),
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
            default   => (string) $request->input('status_id') === '1',
        };

        if ($isPaid || $order->status === 'paid') {
            // Mark as paid if webhook hasn't already done so
            if ($order->status !== 'paid') {
                $this->markOrderAsPaid($order, $request->input('transaction_id') ?? $request->input('billcode'));
            }

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
     * Server-to-server webhook/callback from ToyyibPay.
     */
    public function webhook(Request $request): Response
    {
        Log::info('Payment webhook received', $request->all());

        return match (config('payment.gateway')) {
            'billplz' => $this->handleBillplzWebhook($request),
            default   => $this->handleToyyibPayWebhook($request),
        };
    }

    // =========================================================================
    // ToyyibPay Logic
    // =========================================================================

    private function redirectToToyyibPay(Order $order): RedirectResponse
    {
        $config      = config('payment.toyyibpay');
        $amountInSen = $this->toSen($order->final_amount);

        Log::info('ToyyibPay createBill attempt', [
            'order_id'   => $order->id,
            'amount_sen' => $amountInSen,
            'base_url'   => $config['base_url'],
            'sandbox'    => $config['sandbox'],
        ]);

        $response = Http::timeout(30)
            ->asForm()
            ->post("{$config['base_url']}/index.php/api/createBill", [
                'userSecretKey'          => $config['secret_key'],
                'categoryCode'           => $config['category_code'],
                'billName'               => 'Order #' . $order->id,
                'billDescription'        => 'Alfarhan Wholesale order payment',
                'billPriceSetting'       => 1,   // fixed price
                'billPayorInfo'          => 1,   // collect payer info
                'billAmount'             => $amountInSen,
                'billReturnUrl'          => route('checkout.payment.status', ['order_id' => $order->id]),
                'billCallbackUrl'        => route('webhook.payment'),
                'billExternalReferenceNo'=> (string) $order->id,
                'billTo'                 => $order->customer_name ?? 'Customer',
                'billEmail'              => $order->customer_email ?? 'noemail@example.com',
                'billPhone'              => $order->customer_phone ?? '0123456789',
                'billSplitPayment'       => 0,
                'billSplitPaymentArgs'   => '',
                'billPaymentChannel'     => 0,   // 0 = FPX + eWallet, 1 = FPX only, 2 = eWallet only
                'billDisplayMerchant'    => 1,
                'billContentEmail'       => 'Thank you for your order at Alfarhan Wholesale!',
            ]);

        Log::info('ToyyibPay createBill response', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        if (! $response->successful()) {
            Log::error('ToyyibPay createBill HTTP error', ['body' => $response->body()]);
            return back()->with('error', 'Unable to connect to payment gateway. Please try again.');
        }

        $payload  = $response->json();

        // ToyyibPay returns an array: [{"BillCode":"xxxx"}]
        $billCode = null;
        if (is_array($payload)) {
            $billCode = $payload[0]['BillCode'] ?? $payload['BillCode'] ?? null;
        }

        if (! $billCode) {
            Log::error('ToyyibPay createBill returned no BillCode', ['payload' => $payload]);
            return back()->with('error', 'Invalid response from payment gateway. Please try again.');
        }

        // Save the bill code, this requires the migration to have run
        $order->update(['payment_bill_code' => $billCode]);

        $paymentUrl = "{$config['base_url']}/{$billCode}";

        Log::info('Redirecting to ToyyibPay', ['url' => $paymentUrl]);

        return redirect()->away($paymentUrl);
    }

    private function handleToyyibPayWebhook(Request $request): Response
    {
        // ToyyibPay POST fields: status, billcode, order_id, amount, transaction_id, msg
        $status      = (string) $request->input('status');
        $billCode    = (string) $request->input('billcode');
        $orderId     = (int)    $request->input('order_id');
        $amountInSen = (int)    $request->input('amount');
        $transactionId = (string) $request->input('transaction_id', '');

        Log::info('ToyyibPay webhook parsed', compact('status', 'billCode', 'orderId', 'amountInSen'));

        // status '1' = success, '2' = pending, '3' = failed
        if ($status !== '1') {
            return response('Ignored: status ' . $status, 200);
        }

        // Find order by ID first, fallback to bill code
        $order = $orderId
            ? Order::find($orderId)
            : Order::where('payment_bill_code', $billCode)->first();

        if (! $order) {
            $order = Order::where('payment_bill_code', $billCode)->first();
        }

        if (! $order) {
            Log::warning('ToyyibPay webhook: order not found', compact('orderId', 'billCode'));
            return response('Order not found', 404);
        }

        // Amount verification (in sen)
        if ($amountInSen !== $this->toSen($order->final_amount)) {
            Log::warning('ToyyibPay webhook: amount mismatch', [
                'expected' => $this->toSen($order->final_amount),
                'received' => $amountInSen,
            ]);
            return response('Amount mismatch', 422);
        }

        $this->markOrderAsPaid($order, $transactionId ?: $billCode);

        return response('OK', 200);
    }

    // =========================================================================
    // Billplz Logic
    // =========================================================================

    private function redirectToBillplz(Order $order): RedirectResponse
    {
        $config = config('payment.billplz', []);

        $host         = $config['base_url'] ?? null;
        $collectionId = $config['collection_id'] ?? null;
        $apiKey       = $config['api_key'] ?? null;

        if (! $host || ! $collectionId || ! $apiKey) {
            return back()->with('error', 'Billplz is not fully configured.');
        }

        $url = rtrim($host, '/') . '/bills?collection_id=' . urlencode($collectionId)
             . '&amount=' . $this->toSen($order->final_amount);

        return redirect()->away($url);
    }

    private function handleBillplzWebhook(Request $request): Response
    {
        Log::info('Billplz webhook received', $request->all());

        $reference = (string) $request->input('reference');
        $paid      = $request->boolean('paid');

        $order = $reference
            ? Order::where('payment_ref', $reference)->first()
            : null;

        if (! $order) {
            $orderId = $request->integer('order_id');
            $order   = $orderId ? Order::find($orderId) : null;
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

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Convert RM to sen (e.g. 12.50 → 1250).
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
            'status'      => 'paid',
            'payment_ref' => $paymentRef,
            'paid_at'     => now(),
        ]);

        Log::info('Order marked as paid', [
            'order_id'    => $order->id,
            'payment_ref' => $paymentRef,
        ]);
    }
}
