<?php

namespace App\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface {
    public function create(array $data): Order;
    public function getByUserId($userId);
    public function findById(int $id);
}