<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService){}
    
    public function store(StoreTicketRequest $request, int $eventId): JsonResponse {
        $user = auth()->user();

        if(!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $result = $this->ticketService->createTicket($eventId, $request->validated(), $user);
        
        if(!$result['success']){
            return response()->json([
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['status']);
    }
}
