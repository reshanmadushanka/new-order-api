<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCreate;
use App\Http\Resources\OrderResource;
use App\Jobs\SendOrderDetailsToApi;
use App\Models\Order;
use App\Services\OrderApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *     title="Order API",
 *     version="1.0.0",
 *     description="API documentation for Order API",
 *     @OA\Contact(
 *         email="reshanmadushanka@gmail.com"
 *     )
 * )
 *
 * @OA\PathItem(path="/api")
 */
class OrderController extends Controller
{

    public function __construct(protected OrderApiService $orderApiService)
    {
    }
    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Order"},
     *     summary="Create a new order",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="customer_name", type="string"),
     *             @OA\Property(property="order_value", type="number"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="integer"),
     *             @OA\Property(property="process_id", type="integer"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function store(OrderCreate $request)
    {
        try {
            $order = Order::create([
                'customer_name' => $request->customer_name,
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

            // Dispatch the job to send the order details to the third-party API
            SendOrderDetailsToApi::dispatch($orderDetails)->onQueue('default');

            return (new OrderResource($order))->additional([
                'process_id' => $process_id,
                'status' => 'success',
            ])->response()->setStatusCode(Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error storing Order: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to store Order'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
