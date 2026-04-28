<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\EventResource;

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

        $data = $request->validated();

        if($request->hasFile('image')) {
            $path = $request->file('image')->store('events','public');
            $data['image'] = $path;
        }

        $event = $this->eventService->createEvent(
            $data,
            $user
        );

        return response()->json([
            'message' => 'Event created successfully.',
            'data' => new EventResource($event),
        ], 201);
    }
    
    public function index() {
        $user = request()->user('sanctum');

        $filter = [
            'status' => request()->query('status'),
            ];

        $events = $this->eventService->getVisibleEvents($user, $filter);

        return EventResource::collection($events);
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
            'data' => new EventResource($event),
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
