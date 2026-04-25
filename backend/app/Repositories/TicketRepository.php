<?php

namespace App\Repositories;

use App\Models\Ticket;

class TicketRepository implements TicketRepositoryInterface {
    public function create(array $data): Ticket {
        return Ticket::create($data);
    }

    public function getByEventId(int $eventId) {
        return Ticket::where('event_id', $eventId)
                ->latest()
                ->get();
    }

    public function findById(int $id) {
        return Ticket::find($id);
    }
}