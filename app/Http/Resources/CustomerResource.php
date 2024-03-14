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
            'gender' => $this->gender,
            'birthdate' => $this->birthdate,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'can' => [
                'delete' => $request->user()?->can('delete', $this->resource),
            ],
        ];
    }
}