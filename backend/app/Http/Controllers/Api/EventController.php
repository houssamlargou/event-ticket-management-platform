<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
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
        $events = $this->eventService->getVisibleEvents(request()->user('sanctum'));

        return response()->json([
            'message' => 'Events retrieved successfully.',
            'data' => $events,
        ]);
    }
    public function show(int $id): JsonResponse {
        $event = $this->eventService->getEventById($id);
        if(!$event){
            return response()->json([
                'message' => 'Event not found.',
            ], 404);
        }
        return response()->json([
            'message' => 'Event retrieved successfully.',
            'data' => $event,
        ]);
    }
    public function update(UpdateEventRequest $request, int $id): JsonResponse {
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }
        $result = $this->eventService->updateEvent(
            $id,
            $request->validated(),
            $user
        );
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

    public function destroy(int $id): JsonResponse {
        $user = auth()->user();
        if(!$user){
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }
        $result = $this->eventService->deleteEvent($id, $user);
        if(!$result['success']){
            return response()->json([
                'message' => $result['message'],
            ], $result['status']);
        }
        return response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }

    public function moderate(int $id): JsonResponse {
        $status = request()->input('status');
        $result = $this->eventService->moderateEvent($id, $status);
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
