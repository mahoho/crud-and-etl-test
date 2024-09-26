<?php

namespace App\ETLProcessing\Core;

use App\Models\City;
use App\Models\ETL\RawHotels;
use App\Models\ETL\RawModel;
use App\Models\Hotel;

class HotelsRawToCoreProcessor implements RawToCoreProcessor {

    /**
     * Process hotels raw file entry to core
     *
     * @param RawHotels $rawModel
     * @return void
     */
    public function process(RawModel $rawModel) {
        /** @var RawHotels $rawModel */

        $city = City::firstOrNew(['name' => $rawModel->city]);
        $city->save();

        $hotel = Hotel::firstOrNew([
            'city_id' => $city->id,
            'name' => $rawModel->hotel_name
        ]);

        $hotel->address = $rawModel->address;
        $hotel->image = $rawModel->image ?: null;
        $hotel->description = $rawModel->description ?: null;
        $hotel->stars = filter_var($rawModel->stars, FILTER_VALIDATE_INT);
        $hotel->save();
    }
}
