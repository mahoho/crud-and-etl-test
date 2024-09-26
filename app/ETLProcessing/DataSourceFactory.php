<?php

namespace App\ETLProcessing;

use App\ETLProcessing\Core\HotelsRawToCoreProcessor;
use App\ETLProcessing\Core\RawToCoreProcessor;
use App\ETLProcessing\Raw\RawFileReader;
use App\Models\ETL\RawHotels;

class DataSourceFactory {
    public static function getRawModelClass(string $source): string {
        if($source === 'hotels') {
            return RawHotels::class;
        }

        throw new \InvalidArgumentException("Unknown source: $source");
    }

    public static function getCoreProcessor(string $source): string {
        if($source === 'hotels') {
            return HotelsRawToCoreProcessor::class;
        }

        throw new \InvalidArgumentException("Unknown source: $source");
    }
}
