<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'teams' => $this->when($this->relationLoaded('teams') && $this->relationLoaded('ownedTeams'), fn () => $this->allTeams()->toArray()),
            'role' => $this->whenLoaded('roles', $this->getRoleNames()[0] ?? ''),
            'profile_photo_url' => $this->profile_photo_url,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}