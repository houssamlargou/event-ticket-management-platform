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
}