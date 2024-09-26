<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelRequest;
use App\Http\Resources\HotelResource;
use App\Models\Hotel;

class HotelController extends Controller {
    public function index() {
        return HotelResource::collection(Hotel::all());
    }

    public function store(HotelRequest $request) {
        return new HotelResource(Hotel::create($request->validated()));
    }

    public function show(Hotel $hotel) {
        return new HotelResource($hotel);
    }

    public function update(HotelRequest $request, Hotel $hotel) {
        $hotel->update($request->validated());

        return new HotelResource($hotel);
    }

    public function destroy(Hotel $hotel) {
        $hotel->delete();

        return response()->json();
    }
}
