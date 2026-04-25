<?php 

namespace App\Repositories;

use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface {
    public function create(array $data):Order {
        return Order::create($data);
    }

    public function getByUserId($userId){
        return Order::with(['ticket.event'])
                ->where('user_id', $userId)
                ->latest()
                ->get();
    }

    public function findById(int $id){
        return Order::with('ticket.event')
                ->find($id);
    }

    public function findByUserAndTicket(int $userId, int $ticketId) {
    return Order::where('user_id', $userId)
            ->where('ticket_id', $ticketId)
            ->first();
    }

    public function update($order, array $data){
        $order ->update($data);
        return $order->fresh(['ticket.event']);
    }

    public function markAsPaid($order){
        $order->update([
            'payment_status' => 'paid',
        ]);

        return $order->fresh(['ticket.event']);
    }
}