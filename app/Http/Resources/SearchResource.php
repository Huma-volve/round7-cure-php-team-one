<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ,
            'search_query' => $this->search_query,
            "search_type" => $this->search_type,
            "latitude"    => $this->latitude,
            "longitude"   => $this->longitude,
            "location_name" => $this->location_name,
            "searched_at" => $this->searched_at,
            "is_saved"  => $this->is_saved,
            'user' => $this->when($this->relationLoaded('user'), function () {
                 return [
                    'id' => $this->user->id ?? null,
                    'name' => $this->user->name ?? null,
                    'email' => $this->user->email ?? null,
                    'mobile' => $this->user->mobile ?? null,
                   
                ];
            }),
        ];
    }
}
