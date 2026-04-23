<?php

namespace App\Services;

use App\Models\Event;
use App\Repositories\EventRepositoryInterface;

class EventService {
        public function __construct(protected EventRepositoryInterface $eventRepository){}
        public function createEvent(array $data, $user): Event {
            return $this->eventRepository->create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'event_date' => $data['event_date'],
                'location' => $data['location']
            ]);
        }
}