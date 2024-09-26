<?php

namespace App\Http\Resources;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Hotel */
class HotelsResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'stars' => $this->stars,
            'image' => $this->image,
            'city'  => new CitiesResource($this->whenLoaded('city')),
        ];
    }
}
