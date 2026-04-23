<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository implements EventRepositoryInterface {
    public function create(array $data): Event {
        return Event::create($data);
    }
};