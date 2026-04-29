<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'location' => $this->location,
            'status' => $this->status,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'is_favorited' => $request->user('sanctum')
            ? $request->user('sanctum')->favorites()->where('event_id', $this->id)->exists()
            : false,
        ];
    }
}
