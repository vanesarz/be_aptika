<?php

namespace App\Http\Resources\TaskManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'is_read' => (bool) $this->is_read,
            'read_at' => $this->read_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'board' => $this->whenLoaded('board') ? [
                'id' => $this->board->id,
                'name' => $this->board->name,
            ] : null,
            'task' => $this->whenLoaded('task') ? [
                'id' => $this->task->id,
                'title' => $this->task->title,
                'status' => $this->task->status,
            ] : null,
            'created_by' => $this->whenLoaded('createdBy') ? [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ] : null,
        ];
    }
}
