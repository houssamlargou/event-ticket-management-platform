<?php

namespace App\Repositories;

use App\Models\Ticket;

interface TicketRepositoryInterface {
    public function create(array $data): Ticket;
    public function getByEventId(int $eventId); 
    public function findById(int $id);
}