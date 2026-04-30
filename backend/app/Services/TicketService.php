<?php 

namespace App\Services;

use App\Repositories\TicketRepositoryInterface;
use App\Repositories\EventRepositoryInterface;

class TicketService {
    public function __construct(protected TicketRepositoryInterface $ticketRepository, protected EventRepositoryInterface $eventRepository){}
    public function createTicket(int $eventId, array $data, $user){
        $event = $this->eventRepository->findById($eventId);
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
                'message' => 'Forbidden You can only add tickets to your own event.',
            ];
        }

        $ticket = $this->ticketRepository->create([
            'event_id' => $eventId,
            'name' => $data['name'],
            'price' => $data['price'],
            'quantity' => $data['quantity'],
        ]);

        return [
            'success' => true,
            'status' => 201,
            'message' => 'Ticket created successfully.',
            'data' => $ticket
        ];
    }

    public function getTicketsByEvent(int $eventId) {
        $event = $this->eventRepository->findById($eventId);
        if(!$event){
            return [
                'success' => false,
                'status' => 404,
                'message' => 'Event not found.',
            ];
        }

        $tickets = $this->ticketRepository->getByEventId($eventId);

        return [
            'success' => true,
            'status' => 200,
            'message' => 'Tickets retrieved successfully.',
            'data' => $tickets,
        ];
    }
}