<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderApiService
{
    protected $apiEndpoint = 'https://wibip.free.beeceptor.com/order';
    
    /**
     * sendOrderDetails
     *
     * @param  mixed $orderDetails
     * @return void
     */
    public function sendOrderDetails($orderDetails)
    {
        try {
            $response = Http::post($this->apiEndpoint, $orderDetails);
            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'response' => $response->json()
                ];
            } else {
                Log::error('Failed to send order details: ' . $response->body());
                return [
                    'status' => 'error',
                    'response' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error when sending order details: ' . $e->getMessage());
            return [
                'status' => 'error',
                'response' => $e->getMessage()
            ];
        }
    }
}
