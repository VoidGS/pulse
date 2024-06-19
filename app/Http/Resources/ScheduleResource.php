<?php

namespace App\Http\Resources;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'customer' => $this->whenLoaded('customer', fn () => CustomerResource::make($this->customer)),
            'service' => $this->whenLoaded('service', fn () => ServiceResource::make(Service::with(['user'])->find($this->service->id))),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'active' => $this->active,
            'event_id' => $this->event_id,
            'recurrence_id' => $this->recurrence_id,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'can' => [
                'delete' => $request->user()?->can('delete', $this->resource),
            ],
        ];
    }
}