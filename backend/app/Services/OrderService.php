<?php

namespace App\Services;

use App\Repositories\OrderRepositoryInterface;
use App\Repositories\TicketRepositoryInterface;

class OrderService {
    public function __construct(protected OrderRepositoryInterface $orderRepository, protected TicketRepositoryInterface $ticketRepository){}
    public function createOrder(array $data, $user):array {
        $ticket = $this->ticketRepository->findById($data['ticket_id']);
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

        $totalPrice = $ticket->price * $data['quantity'];

        $order = $this->orderRepository->create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'quantity' => $data['quantity'],
            'total_price' => $totalPrice,
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
    }
}