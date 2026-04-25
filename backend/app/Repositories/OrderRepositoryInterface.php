<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface {
    public function create(array $data): Order;
    public function getByUserId($userId);
    public function findById(int $id);
    public function findByUserAndTicket(int $userId, int $ticketId);
    public function update($order, array $data);
    public function markAsPaid($order);
    public function cancel($order);
}