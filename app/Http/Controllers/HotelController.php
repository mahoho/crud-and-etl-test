<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelRequest;
use App\Http\Resources\HotelsResource;
use App\Models\Hotel;

class HotelController extends Controller {
    public function list() {
        $hotels = Hotel::with('city')->get();

        return HotelsResource::collection($hotels);
    }

    public function show($id) {
        return Hotel::with('city')->findOrFail($id);
    }

    public function save(HotelRequest $request) {
        $id = $request->input('id');
        $hotel = Hotel::findOrNew($id);
        $hotel->fill($request->validated());
        $hotel->save();

        $hotel->load('city');

        return $hotel;
    }

    public function delete($id) {
        Hotel::where(['id' => $id])->delete();

        return ['success' => true];
    }
}
