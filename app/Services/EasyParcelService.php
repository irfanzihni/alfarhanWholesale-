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
    protected $originCity;
    protected $originState;
    protected $originName;
    protected $originPhone;
    protected $originAddress;

    public function __construct()
    {
        $this->apiKey = config('services.easyparcel.api_key');
        $this->sandbox = config('services.easyparcel.sandbox', true);
        
        $this->apiUrl = $this->sandbox
            ? 'http://demo.connect.easyparcel.my/?ac='
            : 'https://connect.easyparcel.my/?ac=';

        $this->originPostcode = config('services.easyparcel.origin_postcode', '47100');
        $this->originCity = config('services.easyparcel.origin_city', 'Puchong');
        $this->originState = config('services.easyparcel.origin_state', 'Selangor');
        $this->originName = config('services.easyparcel.origin_name', 'Alfarhan Trading');
        $this->originPhone = config('services.easyparcel.origin_phone', '0123456789');
        $this->originAddress = config('services.easyparcel.origin_address', 'No 1, Jalan Puchong, Industri Puchong');
    }

    /**
     * Get real-time shipping rates from EasyParcel.
     * Fallbacks to standard rates if API is unavailable or unconfigured.
     */
    public function getRates($destPostcode, $totalWeight = 0.50, $destState = '')
    {
        $totalWeight = max(0.10, (float)$totalWeight);

        // If API key is not set, run fallback generator
        if (empty($this->apiKey)) {
            Log::info('EasyParcel API key not configured. Using fallback local shipping rates.');
            return $this->getFallbackRates($destPostcode, $totalWeight, $destState);
        }

        try {
            $action = 'EPRateCheckingBulk';
            $url = $this->apiUrl . $action;

            $payload = [
                'api' => $this->apiKey,
                'bulk' => [
                    [
                        'pick_code' => $this->originPostcode,
                        'pick_country' => 'MY',
                        'send_code' => $destPostcode,
                        'send_country' => 'MY',
                        'weight' => $totalWeight,
                        'width' => 10,
                        'length' => 10,
                        'height' => 10
                    ]
                ]
            ];

            $response = Http::asForm()->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['api_status']) && strtolower($data['api_status']) === 'success' && isset($data['result'][0]['rates'])) {
                    $rates = [];
                    foreach ($data['result'][0]['rates'] as $rate) {
                        $rates[] = [
                            'service_id' => $rate['service_id'],
                            'service_name' => $rate['service_name'],
                            'courier_name' => $rate['courier_name'],
                            'price' => (float)$rate['price'],
                            'delivery' => $rate['delivery'],
                            'logo' => $rate['courier_logo'] ?? null,
                        ];
                    }
                    return $rates;
                } else {
                    $remark = $data['error_remark'] ?? ($data['result'][0]['remarks'] ?? 'Unknown API error');
                    Log::warning('EasyParcel Rate Checking API returned error: ' . $remark);
                }
            } else {
                Log::error('EasyParcel Rate Checking API request failed: Status ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('EasyParcel Rate Checking exception: ' . $e->getMessage());
        }

        // Return fallback if API call fails
        return $this->getFallbackRates($destPostcode, $totalWeight, $destState);
    }

    /**
     * Book shipment order on EasyParcel.
     */
    public function createShipment($order)
    {
        $weight = 0;
        foreach ($order->items as $item) {
            $weight += ($item->product->weight ?? 0.50) * $item->quantity;
        }
        $weight = max(0.10, $weight);

        // If API key is not configured, generate a mock success
        if (empty($this->apiKey) || $this->sandbox) {
            Log::info('EasyParcel API key not configured or sandbox active. Generating mock shipment booking.');
            return [
                'status' => 'Success',
                'easyparcel_order_id' => 'EP-MOCK-' . strtoupper(uniqid()),
                'tracking_code' => 'MY-MOCK-' . $order->id . '-' . rand(100000, 999999),
                'price' => $order->shipping_cost ?? 8.00,
            ];
        }

        try {
            $action = 'EPSubmitOrderBulkV3';
            $url = $this->apiUrl . $action;

            $payload = [
                'api' => $this->apiKey,
                'bulk' => [
                    [
                        'reference' => 'ORDER-' . $order->id,
                        'weight' => $weight,
                        'width' => 10,
                        'length' => 10,
                        'height' => 10,
                        'content' => 'Alfarhan Wholesale Order #' . $order->id,
                        'value' => $order->final_amount,
                        
                        // Sender Details
                        'pick_name' => $this->originName,
                        'pick_contact' => $this->originPhone,
                        'pick_addr1' => $this->originAddress,
                        'pick_city' => $this->originCity,
                        'pick_state' => $this->originState,
                        'pick_code' => $this->originPostcode,
                        'pick_country' => 'MY',
                        
                        // Receiver Details
                        'send_name' => $order->customer_name,
                        'send_contact' => $order->customer_phone,
                        'send_email' => $order->customer_email ?: 'customer@example.com',
                        'send_addr1' => $order->delivery_address,
                        'send_city' => $order->shipping_city ?: 'City',
                        'send_state' => $order->shipping_state ?: 'State',
                        'send_code' => $order->shipping_postcode ?: '50000',
                        'send_country' => 'MY',
                        'collect_date' => now()->addDay()->format('Y-m-d'),
                        'courier' => [$order->shipping_courier],
                    ]
                ]
            ];

            $response = Http::asForm()->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['api_status']) && strtolower($data['api_status']) === 'success' && isset($data['result'][0]['status']) && strtolower($data['result'][0]['status']) === 'success') {
                    $res = $data['result'][0];
                    return [
                        'status' => 'Success',
                        'easyparcel_order_id' => $res['parcel_number'] ?? ($res['order_number'] ?? 'EP-' . rand(1000, 9999)),
                        'tracking_code' => $res['consignment_note'] ?? null,
                        'price' => (float)($res['price'] ?? 0.00),
                    ];
                } else {
                    $remark = $data['error_remark'] ?? ($data['result'][0]['remarks'] ?? 'Unknown API error');
                    return [
                        'status' => 'Failed',
                        'message' => 'EasyParcel API Error: ' . $remark
                    ];
                }
            } else {
                return [
                    'status' => 'Failed',
                    'message' => 'API connection failed (Status ' . $response->status() . ')'
                ];
            }
        } catch (\Exception $e) {
            Log::error('EasyParcel Booking exception: ' . $e->getMessage());
            return [
                'status' => 'Failed',
                'message' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Local rate calculation engine.
     * Estimates shipping fees for major Malaysian carriers.
     */
    protected function getFallbackRates($destPostcode, $weight, $destState = '')
    {
        // East Malaysia states: Sabah, Sarawak, Labuan. Or postcodes starting with 87-99.
        $postcodeNum = (int)$destPostcode;
        $isEastMalaysia = false;

        if ($postcodeNum >= 87000 && $postcodeNum <= 99999) {
            $isEastMalaysia = true;
        } elseif (!empty($destState)) {
            $eastStates = ['Sabah', 'Sarawak', 'Labuan'];
            foreach ($eastStates as $s) {
                if (stripos($destState, $s) !== false) {
                    $isEastMalaysia = true;
                    break;
                }
            }
        }

        // Base & incremental rates
        if ($isEastMalaysia) {
            // East Malaysia pricing
            $carriers = [
                ['name' => 'Poslaju', 'base' => 15.00, 'inc' => 5.00, 'days' => '3-5 hari bekerja'],
                ['name' => 'J&T Express', 'base' => 14.50, 'inc' => 4.50, 'days' => '2-4 hari bekerja'],
                ['name' => 'Ninja Van', 'base' => 13.00, 'inc' => 4.80, 'days' => '3-6 hari bekerja'],
            ];
        } else {
            // West Malaysia pricing
            $carriers = [
                ['name' => 'J&T Express', 'base' => 7.50, 'inc' => 1.80, 'days' => '1-2 hari bekerja'],
                ['name' => 'Poslaju', 'base' => 8.00, 'inc' => 2.00, 'days' => '2-3 hari bekerja'],
                ['name' => 'Ninja Van', 'base' => 7.00, 'inc' => 2.20, 'days' => '2-4 hari bekerja'],
                ['name' => 'DHL eCommerce', 'base' => 8.50, 'inc' => 1.50, 'days' => '2-3 hari bekerja'],
            ];
        }

        $rates = [];
        $weightFactor = max(0, ceil($weight - 1.0)); // standard base weight covers 1kg

        foreach ($carriers as $index => $c) {
            $price = $c['base'] + ($c['inc'] * $weightFactor);
            $rates[] = [
                'service_id' => 'FALLBACK-SV-' . ($isEastMalaysia ? 'EM' : 'WM') . '-' . str_replace(' ', '', strtoupper($c['name'])),
                'service_name' => 'Standard Delivery',
                'courier_name' => $c['name'],
                'price' => $price,
                'delivery' => $c['days'],
                'logo' => null,
            ];
        }

        return $rates;
    }
}
