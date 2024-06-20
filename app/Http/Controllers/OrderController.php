<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCreate;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{

    public function __construct(protected OrderApiService $orderApiService)
    {
    }
    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(OrderCreate $request)
    {
        try {
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'status' => config('order_statuses.statuses.processing'),
                'order_value' => $request->order_value,
            ]);
            $process_id = rand(1, 10);

            $orderDetails = [
                'Order_ID' => $order->id,
                'Customer_Name' => $order->customer_name,
                'Order_Value' => $order->order_value,
                'Order_Date' => $order->created_at->format('Y-m-d H:i:s'),
                'Order_Status' => $order->status,
                'Process_ID' => $process_id,
            ];

            // Send the order details to the third-party API
            $apiResponse = $this->orderApiService->sendOrderDetails($orderDetails);

            return (new OrderResource($order))->additional([
                'process_id' => $process_id,
                'status' => 'success',
                'api_response' => $apiResponse
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error storing Order: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to store Order'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
