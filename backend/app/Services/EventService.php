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
                'location' => $data['location'],
                'status' => 'pending',
            ]);
        }
        public function getAllEvents(){
            return $this->eventRepository->getAll();
        }
        public function getEventById(int $id){
            return $this->eventRepository->findById($id);
        }
        public function updateEvent(int $id, array $data, $user){
            $event = $this->eventRepository->findById($id);
            if(!$event){
                return [
                    'success' => false,
                    'status' => 404,
                    'message' => 'Event not found.',
                ];
            }
            if($event->user_id !== $user->id){
                return [
                    'success' => false,
                    'status' => 403,
                    'message' => 'Forbidden. You can only update your own event.',
                ];
            }
            $updatedEvent = $this->eventRepository->update($event, $data);
            return [
                'success' => true,
                'status' => 200,
                'message' => 'Event updated successfully.',
                'data' => $updatedEvent,
            ];
        }

        public function deleteEvent(int $id, $user) {
            $event = $this->eventRepository->findById($id);
            if(!$event) {
                return [
                    'success' => false,
                    'status' => 404,
                    'message' => 'Event not found.',
                ];
            }
            if($event->user_id !== $user->id) {
                return [
                    'success' => false,
                    'status' => 403,
                    'message' => 'Forbidden. You can only delete your own event.',
                ];
            }
            $this->eventRepository->delete($event);
            return [
                'success' => true,
                'status' => 200,
                'message' => 'Event deleted successfully.',
            ];
        }

        public function moderateEvent(int $id, string $status){
            $event = $this->eventRepository->findById($id);
            if(!$event){
                return [
                    'success' => false,
                    'status' => 404,
                    'message' => 'Event not found.',
                ];
            }
            if(!in_array($status, ['approved', 'rejected'])) {
                return [
                    'success' => false,
                    'status' => 422,
                    'message' => 'Invalid status.',
                ];
            }
            $updateEvent = $this->eventRepository->updateStatus($event, $status);
            return [
                'success' => true,
                'status' => 200,
                'message' => "Event {$status} successfully.",
                'data' => $updateEvent,
            ];
        }

        public function getVisibleEvents($user = null) {
            return $this->eventRepository->getVisibleEvents($user);
        }
}