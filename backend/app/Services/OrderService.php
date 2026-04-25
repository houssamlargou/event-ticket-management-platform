<?php

namespace App\Services;

use App\Repositories\OrderRepositoryInterface;
use App\Repositories\TicketRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderService {
    public function __construct(protected OrderRepositoryInterface $orderRepository, protected TicketRepositoryInterface $ticketRepository){}
    public function createOrder(array $data, $user):array {

    return DB::transaction(function () use ($data, $user) {
        $ticket = $this->ticketRepository
                    ->query()
                    ->where('id', $data['ticket_id'])
                    ->lockForUpdate()
                    ->first();

        if(!$ticket){
            return [
                'success' => false,
                'status' => 404,
                'message' => 'Ticket not found.'
            ];
        }
    

        if($data['quantity'] > $ticket->quantity){
            return [
                'success' => false,
                'status' => 422,
                'message' => 'Not enough tickets available.',
            ];
        }

        $existingOrder = $this->orderRepository->findByUserAndTicket($user->id, $ticket->id);

        if($existingOrder) {
            $newQuantity = $existingOrder->quantity + $data['quantity'];
            $newTotalPrice = $ticket->price * $newQuantity;
            $updatedOrder = $this->orderRepository->update($existingOrder, [
                'quantity' => $newQuantity,
                'total_price' => $newTotalPrice,
            ]);
            $ticket->update([
                'quantity' => $ticket->quantity - $data['quantity'],
            ]);

            return [
                'success' => true,
                'status' => 200,
                'message' => 'Order updated successfully.',
                'data' => $updatedOrder
            ];
        }

        

        $totalPrice = $ticket->price * $data['quantity'];

        $order = $this->orderRepository->create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'quantity' => $data['quantity'],
            'total_price' => $totalPrice,
            'payment_status' => 'pending',
        ]);

        $ticket->update([
            'quantity' => $ticket->quantity - $data['quantity'],
        ]);

        return [
            'success' => true,
            'status' => 201,
            'message' => 'Order created successfully.',
            'data' => $order
        ];
    });
}

    public function getUserOrders($user): array {
        $orders = $this->orderRepository->getByUserId($user->id);

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Order retrieved successfully.',
            'data' => $orders,
        ];
    }

    public function getOrderById(int $id, $user): array {
        $order = $this->orderRepository->findById($id);

        if(!$order){
            return [
                'success' => false,
                'status' => 404,
                'message' => 'Order not found.',
            ];
        }

        if($order->user_id !== $user->id){
            return [
                'success' => false,
                'status' => 403,
                'message' => 'Forbidden. You can only view your own order.',
            ];
        }

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Order retrieved successfully.',
            'data' => $order,
        ];
    }

    public function payOrder(int $id, $user):array {
        $order = $this->orderRepository->findById($id);

        if(!$order){
            return [
                'success' => false,
                'status' => 404,
                'message' => 'Order not found.',
            ];
        }

        if($order->user_id !== $user->id) {
            return [
                'success' => false,
                'status' => 403,
                'message' => 'Forbidden. You can only pay own order.',
            ];
        }

        if($order->payment_status === 'paid') {
            return [
                'success' => false,
                'status' => 400,
                'message' => 'Order is already paid.'
            ];
        }

        $updatedOrder = $this->orderRepository->markAsPaid($order);

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Payment successful.',
            'data' => $updatedOrder,
        ];
    }

    public function cancelOrder(int $id, $user):array {
        $order = $this->orderRepository->findById($id);

        if(!$order){
            return [
                'success' => false,
                'status' => 404,
                'message' => 'Order not found.',
            ];
        }

        if($order->user_id !== $user->id){
            return [
                'success' => false,
                'status' => 403,
                'message' => 'Forbidden. You can only cancel your own order.',
            ];
        }

        if($order->payment_status !== 'pending') {
            return [
                'success' => false,
                'status' => 400,
                'message' => 'Only pending orders can be cancelled.',
            ];
        }

        $ticket = $order->ticket;

        $ticket->update([
            'quantity' => $ticket->quantity + $order->quantity,
        ]);

        $cancelledOrder = $this->orderRepository->cancel($order);

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Order cancelled successfully.',
            'data' => $cancelledOrder,
        ];
    }
}