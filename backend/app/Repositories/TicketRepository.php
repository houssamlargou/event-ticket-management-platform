<?php

namespace App\Repositories;

use App\Models\Ticket;

class TicketRepository implements TicketRepositoryInterface {
    public function create(array $data): Ticket {
        return Ticket::create($data);
    }
}