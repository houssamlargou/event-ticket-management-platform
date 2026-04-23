<?php 
namespace App\Repositories;

use App\Models\Event;

interface EventRepositoryInterface {
    public function create(array $data): Event;
    public function getAll();
    public function findById(int $id);
}