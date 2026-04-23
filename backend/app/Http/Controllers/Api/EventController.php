<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(protected EventService $eventService){}
    public function store(StoreEventRequest $request): JsonResponse {
        $user = auth()->user();

        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $event = $this->eventService->createEvent(
            $request->validated(),
            $user
        );

        return response()->json([
            'message' => 'Event created successfully.',
            'data' => $event,
        ], 201);
    }
    public function index(): JsonResponse {
        $events = $this->eventService->getAllEvents();

        return response()->json([
            'message' => 'Events retrieved successfully.',
            'data' => $events,
        ]);
    }
}
