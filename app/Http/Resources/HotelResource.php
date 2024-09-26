<?php

namespace App\Http\Resources;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Hotel */
class HotelResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'address'    => $this->address,
            'stars'      => $this->stars,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'city_id' => $this->city_id,
        ];
    }
}
