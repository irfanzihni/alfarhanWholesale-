<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EasyParcelService
{
    protected $apiKey;
    protected $sandbox;
    protected $apiUrl;
    protected $originPostcode;
    protected $originState;
    protected $originName;
    protected $originPhone;
    protected $originAddress;
    protected $originCity;

    /**
     * EasyParcel state code mapping (full name -> abbreviated code per Appendix III).
     */
    protected $stateCodes = [
        'johor'           => 'jhr',
        'kedah'           => 'kdh',
        'kelantan'        => 'ktn',
        'melaka'          => 'mlk',
        'negeri sembilan' => 'nsn',
        'pahang'          => 'phg',
        'perak'           => 'prk',
        'perlis'          => 'pls',
        'pulau pinang'    => 'png',
        'penang'          => 'png',
        'selangor'        => 'sgr',
        'terengganu'      => 'trg',
        'kuala lumpur'    => 'kul',
        'putrajaya'       => 'pjy',
        'putra jaya'      => 'pjy',
        'sarawak'         => 'srw',
        'sabah'           => 'sbh',
        'labuan'          => 'lbn',
        // Allow passing the code directly
        'jhr' => 'jhr',
        'kdh' => 'kdh',
        'ktn' => 'ktn',
        'mlk' => 'mlk',
        'nsn' => 'nsn',
        'phg' => 'phg',
        'prk' => 'prk',
        'pls' => 'pls',
        'png' => 'png',
        'sgr' => 'sgr',
        'trg' => 'trg',
        'kul' => 'kul',
        'pjy' => 'pjy',
        'srw' => 'srw',
        'sbh' => 'sbh',
        'lbn' => 'lbn',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.easyparcel.api_key');
        $this->sandbox = config('services.easyparcel.sandbox', false);

        // Live URL only — never use demo for real credentials
        $this->apiUrl = $this->sandbox
            ? 'http://demo.connect.easyparcel.my/?ac='
            : 'https://connect.easyparcel.my/?ac=';

        $this->originPostcode = config('services.easyparcel.origin_postcode', '47100');
        $this->originCity     = config('services.easyparcel.origin_city', 'Puchong');
        $this->originState    = config('services.easyparcel.origin_state', 'Selangor');
        $this->originName     = config('services.easyparcel.origin_name', 'Alfarhan Trading');
        $this->originPhone    = config('services.easyparcel.origin_phone', '0123456789');
        $this->originAddress  = config('services.easyparcel.origin_address', 'No 1, Jalan Puchong, Industri Puchong');
    }

    /**
     * Resolve a full state name (or existing code) to EasyParcel's abbreviated code.
     */
    protected function resolveStateCode(string $state): string
    {
        $key = strtolower(trim($state));
        return $this->stateCodes[$key] ?? $key;
    }

    /**
     * Get real-time shipping rates from EasyParcel live API.
     * Falls back to local rate table only if API is truly unreachable.
     */
    public function getRates(string $destPostcode, float $totalWeight = 0.50, string $destState = ''): array
    {
        $totalWeight = max(0.10, $totalWeight);

        if (empty($this->apiKey) || $this->apiKey === 'your-easyparcel-api-key-here') {
            Log::warning('EasyParcel: API key not configured. Falling back to local rates.');
            return $this->getFallbackRates($destPostcode, $totalWeight, $destState);
        }

        $pickStateCode = $this->resolveStateCode($this->originState);
        $sendStateCode = $this->resolveStateCode($destState);

        $url = $this->apiUrl . 'EPRateCheckingBulk';

        $payload = [
            'api'            => $this->apiKey,
            'exclude_fields' => ['rates.*.pickup_point', 'rates.*.dropoff_point'],
            'bulk'           => [
                [
                    'pick_code'    => $this->originPostcode,
                    'pick_state'   => $pickStateCode,
                    'pick_country' => 'MY',
                    'send_code'    => $destPostcode,
                    'send_state'   => $sendStateCode,
                    'send_country' => 'MY',
                    'weight'       => $totalWeight,
                    'width'        => 0,
                    'length'       => 0,
                    'height'       => 0,
                ],
            ],
        ];

        try {
            $response = Http::timeout(15)
                ->asForm()
                ->post($url, $payload);

            Log::info('EasyParcel Rate Check Request', [
                'url'     => $url,
                'payload' => $payload,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('EasyParcel Rate Check Response', ['data' => $data]);

                $apiStatus = strtolower($data['api_status'] ?? '');

                if ($apiStatus === 'success' && isset($data['result'][0]['rates'])) {
                    $rates = [];
                    foreach ($data['result'][0]['rates'] as $rate) {
                        // Use shipment_price for display (actual courier charge, not EasyParcel markup)
                        $rates[] = [
                            'service_id'   => $rate['service_id'],
                            'service_name' => $rate['service_name'],
                            'courier_name' => $rate['courier_name'],
                            'price'        => (float) ($rate['price'] ?? 0),
                            'delivery'     => $rate['delivery'] ?? '-',
                            'logo'         => $rate['courier_logo'] ?? null,
                        ];
                    }

                    if (empty($rates)) {
                        Log::warning('EasyParcel: API returned success but no rates for postcode ' . $destPostcode);
                        return $this->getFallbackRates($destPostcode, $totalWeight, $destState);
                    }

                    return $rates;
                }

                // API returned failure — log and fallback
                $remark = $data['error_remark'] ?? ($data['result'][0]['remarks'] ?? 'Unknown API error');
                Log::warning('EasyParcel Rate Check API error: ' . $remark, ['data' => $data]);

            } else {
                Log::error('EasyParcel Rate Check HTTP error: ' . $response->status(), [
                    'body' => $response->body(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('EasyParcel Rate Check exception: ' . $e->getMessage());
        }

        // Fallback if anything goes wrong
        return $this->getFallbackRates($destPostcode, $totalWeight, $destState);
    }

    /**
     * Book a shipment order on EasyParcel (live).
     */
    public function createShipment($order): array
    {
        $weight = 0;
        foreach ($order->items as $item) {
            $weight += ($item->product->weight ?? 0.50) * $item->quantity;
        }
        $weight = max(0.10, $weight);

        if (empty($this->apiKey) || $this->apiKey === 'your-easyparcel-api-key-here') {
            Log::info('EasyParcel: API key not configured. Generating mock shipment.');
            return $this->mockShipment($order);
        }

        if ($this->sandbox) {
            Log::info('EasyParcel: Sandbox mode active. Generating mock shipment.');
            return $this->mockShipment($order);
        }

        $url = $this->apiUrl . 'EPSubmitOrderBulkV3';

        $payload = [
            'api'  => $this->apiKey,
            'bulk' => [
                [
                    'reference' => 'ORDER-' . $order->id,
                    'weight'    => $weight,
                    'width'     => 0,
                    'length'    => 0,
                    'height'    => 0,
                    'content'   => 'Alfarhan Wholesale Order #' . $order->id,
                    'value'     => $order->final_amount,

                    // Sender
                    'pick_name'    => $this->originName,
                    'pick_contact' => $this->originPhone,
                    'pick_addr1'   => $this->originAddress,
                    'pick_city'    => $this->originCity,
                    'pick_state'   => $this->resolveStateCode($this->originState),
                    'pick_code'    => $this->originPostcode,
                    'pick_country' => 'MY',

                    // Receiver
                    'send_name'    => $order->customer_name,
                    'send_contact' => $order->customer_phone,
                    'send_email'   => $order->customer_email ?: 'customer@example.com',
                    'send_addr1'   => $order->delivery_address,
                    'send_city'    => $order->shipping_city   ?? 'City',
                    'send_state'   => $this->resolveStateCode($order->shipping_state ?? ''),
                    'send_code'    => $order->shipping_postcode ?? '50000',
                    'send_country' => 'MY',

                    'collect_date' => now()->addDay()->format('Y-m-d'),
                    'courier'      => [$order->shipping_courier],
                ],
            ],
        ];

        try {
            $response = Http::timeout(20)->asForm()->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (
                    isset($data['api_status']) &&
                    strtolower($data['api_status']) === 'success' &&
                    isset($data['result'][0]['status']) &&
                    strtolower($data['result'][0]['status']) === 'success'
                ) {
                    $res = $data['result'][0];
                    return [
                        'status'              => 'Success',
                        'easyparcel_order_id' => $res['parcel_number'] ?? ($res['order_number'] ?? 'EP-' . rand(1000, 9999)),
                        'tracking_code'       => $res['consignment_note'] ?? null,
                        'price'               => (float) ($res['price'] ?? 0.00),
                    ];
                }

                $remark = $data['error_remark'] ?? ($data['result'][0]['remarks'] ?? 'Unknown API error');
                return ['status' => 'Failed', 'message' => 'EasyParcel API Error: ' . $remark];
            }

            return ['status' => 'Failed', 'message' => 'API connection failed (HTTP ' . $response->status() . ')'];

        } catch (\Exception $e) {
            Log::error('EasyParcel createShipment exception: ' . $e->getMessage());
            return ['status' => 'Failed', 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Generate a mock shipment response (used in sandbox or when API key missing).
     */
    protected function mockShipment($order): array
    {
        return [
            'status'              => 'Success',
            'easyparcel_order_id' => 'EP-MOCK-' . strtoupper(uniqid()),
            'tracking_code'       => 'MY-MOCK-' . $order->id . '-' . rand(100000, 999999),
            'price'               => $order->shipping_cost ?? 8.00,
        ];
    }

    /**
     * Fallback local rate table — used ONLY when API is unavailable.
     * Based on typical Malaysian courier rates; not from EasyParcel live data.
     */
    protected function getFallbackRates(string $destPostcode, float $weight, string $destState = ''): array
    {
        $postcodeNum   = (int) $destPostcode;
        $isEastMY      = false;

        if ($postcodeNum >= 87000 && $postcodeNum <= 99999) {
            $isEastMY = true;
        } elseif (!empty($destState)) {
            $lower = strtolower($destState);
            foreach (['sabah', 'sbh', 'sarawak', 'srw', 'labuan', 'lbn'] as $em) {
                if (str_contains($lower, $em)) {
                    $isEastMY = true;
                    break;
                }
            }
        }

        $carriers = $isEastMY
            ? [
                ['name' => 'J&T Express',    'base' => 14.50, 'inc' => 4.50, 'days' => '2-4 hari bekerja'],
                ['name' => 'Ninja Van',       'base' => 13.00, 'inc' => 4.80, 'days' => '3-6 hari bekerja'],
                ['name' => 'Shopee Express',  'base' => 12.50, 'inc' => 4.20, 'days' => '2-5 hari bekerja'],
            ]
            : [
                ['name' => 'J&T Express',    'base' => 7.50, 'inc' => 1.80, 'days' => '1-2 hari bekerja'],
                ['name' => 'Ninja Van',      'base' => 7.00, 'inc' => 2.20, 'days' => '2-4 hari bekerja'],
                ['name' => 'Shopee Express', 'base' => 7.20, 'inc' => 1.60, 'days' => '2-3 hari bekerja'],
            ];

        $extra = max(0, ceil($weight - 1.0));
        $rates = [];
        $suffix = $isEastMY ? 'EM' : 'WM';

        foreach ($carriers as $c) {
            $rates[] = [
                'service_id'   => 'FALLBACK-' . $suffix . '-' . strtoupper(str_replace([' ', '&'], '', $c['name'])),
                'service_name' => 'Standard Delivery',
                'courier_name' => $c['name'],
                'price'        => round($c['base'] + ($c['inc'] * $extra), 2),
                'delivery'     => $c['days'],
                'logo'         => null,
            ];
        }

        return $rates;
    }
}
