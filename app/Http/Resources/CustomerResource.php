<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cpf' => $this->cpf,
            'phone' => $this->phone,
            'email' => $this->email,
            'birthdate' => $this->birthdate,
            'guardians' => $this->whenLoaded('guardians', fn () => GuardianResource::collection($this->guardians)),
            'discounts' => $this->whenLoaded('discounts', fn () => GuardianResource::collection($this->discounts)),
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'can' => [
                'delete' => $request->user()?->can('delete', $this->resource),
            ],
        ];
    }
}