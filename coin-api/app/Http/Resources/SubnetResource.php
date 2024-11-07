<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubnetResource extends JsonResource
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
            'name' => $this->name,
            'icon' => $this->icon,
            'description' => $this->description,
            'provider_embed_url' => $this->provider_embed_url,
            'miner_embed_url' => $this->miner_embed_url,
            'weight' => $this->whenPivotLoaded('assignment_subnet', function () {
                return (float) $this->pivot->weight;
            }),
            'needed' => $this->whenPivotLoaded('assignment_subnet', function () {
                return (float) $this->pivot->needed;
            }),
            'grades' => GradeResource::collection($this->whenLoaded('grades')),
        ];
    }
}
