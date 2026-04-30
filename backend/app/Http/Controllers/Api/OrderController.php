<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService){}
    public function store(StoreOrderRequest $request): JsonResponse {
        $user = $request->user('sanctum');
        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $result = $this->orderService->createOrder(
            $request->validated(),
            $user
        );

        if(!$result['success']){
            return response()->json([
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }

    public function index(): JsonResponse {
        $user = request()->user('sanctum');

        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $result = $this->orderService->getUserOrders($user);

        return response()->json([
            'message' => $result['message'],
            'data' => OrderResource::collection($result['data']),
        ], $result['status']);
    }

    public function show(int $id): JsonResponse {
        $user = request()->user('sanctum');

        if(!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $result = $this->orderService->getOrderById($id, $user);

        if(!$result['success']){
            return response()->json([
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => new OrderResource($result['data']),
        ], $result['status']);
    }

    public function pay(int $id): JsonResponse {
        $user = request()->user('sanctum');

        if(!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $result = $this->orderService->payOrder($id, $user);

        if(!$result['success']){
            return response()->json([
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }
    
    public function cancel(int $id): JsonResponse {
        $user = request()->user('sanctum');

        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $result = $this->orderService->cancelOrder($id, $user);

        if(!$result['success']){
            return response()->json([
                'message' => $result['message'],
            ], $result['status']);
        }

  
        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
        
    }
}
