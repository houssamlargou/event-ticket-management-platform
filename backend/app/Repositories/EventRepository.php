<?php

namespace App\Repositories;

use App\Models\Event;

class EventRepository implements EventRepositoryInterface {
    public function create(array $data): Event {
        return Event::create($data);
    }

    public function getAll(){
        return Event::with('user')->latest()->get();
    }

    public function findById(int $id){
        return Event::with('user')->find($id);
    }

    public function update(Event $event, array $data): Event {
        $event->update($data);
        return $event->fresh(['user']);
    }

    public function delete(Event $event): void {
        $event->delete();
    }

    public function updateStatus(Event $event, string $status): Event {
        $event->update([
            'status' => $status,
        ]);
        return $event->fresh(['user']);
    }
};