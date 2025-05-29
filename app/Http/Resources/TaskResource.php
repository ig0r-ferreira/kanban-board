<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'key' => $this->key,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => new StatusResource($this->whenLoaded('status')),
            'reporter' => new UserResource($this->whenLoaded('reporter')),
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'due_date' => $this->due_date,
            'resolution_date' => $this->resolution_date,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
