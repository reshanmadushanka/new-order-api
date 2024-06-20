<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase; // Reset database after each test
    /**
     * Test creating a new order.
     *
     * @return void
     */
    public function test_create_order()
    {
        Queue::fake(); // Mock the queue to fake job dispatching

        // Create a user and authenticate
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $orderData = [
            'customer_name' => 'John Doe',
            'order_value' => 100.00,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->postJson('/api/orders', $orderData);

        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'customer_name',
                         'order_value',
                         'created_at',
                         'updated_at',
                         'process_id',
                         'status',
                     ]
                 ]);

        // Assert that the job to send order details to API was dispatched
        Queue::assertPushed(\App\Jobs\SendOrderDetailsToApi::class, function ($job) use ($response) {
            $orderDetails = $response->json('data');
            return $job->orderDetails['Order_ID'] === $orderDetails['id'];
        });
    }
    
}
