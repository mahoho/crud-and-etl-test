<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelRequest;
use App\Http\Resources\HotelsResource;
use App\Models\Hotel;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller {
    /**
     * Display a listing of the hotels.
     *
     */
    public function index() {
        $hotels = Hotel::with('city')->get();

        return HotelsResource::collection($hotels);
    }

    /**
     * Store a newly created hotel in storage.
     *
     * @param HotelRequest $request
     * @return JsonResponse
     */
    public function store(HotelRequest $request): JsonResponse {
        $hotel = Hotel::create($request->validated());

        return response()->json([
            'message' => 'Hotel created successfully',
            'hotel'   => $hotel,
        ], 201);
    }

    /**
     * Display the specified hotel.
     *
     * @param Hotel $hotel
     * @return JsonResponse
     */
    public function show(Hotel $hotel): JsonResponse {
        $hotel->load('city');

        return response()->json($hotel);
    }

    /**
     * Update the specified hotel in storage.
     *
     * @param HotelRequest $request
     * @param Hotel $hotel
     * @return JsonResponse
     */
    public function update(HotelRequest $request, Hotel $hotel): JsonResponse {
        $hotel->update($request->validated());

        return response()->json([
            'message' => 'Hotel updated successfully',
            'hotel'   => $hotel,
        ]);
    }

    /**
     * Remove the specified hotel from storage (soft delete).
     *
     * @param Hotel $hotel
     * @return JsonResponse
     */
    public function destroy(Hotel $hotel): JsonResponse {
        $hotel->delete();

        return response()->json([
            'message' => 'Hotel deleted successfully',
        ], 204);
    }

    /**
     * Restore the specified soft-deleted hotel.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function restore($id): JsonResponse {
        $hotel = Hotel::withTrashed()->findOrFail($id);
        $hotel->restore();

        return response()->json([
            'message' => 'Hotel restored successfully',
            'hotel'   => $hotel,
        ]);
    }
}
