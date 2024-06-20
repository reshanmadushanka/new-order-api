<?php

namespace App\Jobs;

use App\Services\OrderApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderDetailsToApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderDetails;
    /**
     * __construct
     *
     * @param  mixed $orderDetails
     * @return void
     */
    public function __construct($orderDetails)
    {
        $this->orderDetails = $orderDetails;
    }


    /**
     * Execute the job.
     */
    public function handle(OrderApiService $orderApiService): void
    {
        $response = $orderApiService->sendOrderDetails($this->orderDetails);

        if ($response['status'] !== 'success') {
            Log::error('Failed to send order details: ' . json_encode($response));
        }
    }
}
