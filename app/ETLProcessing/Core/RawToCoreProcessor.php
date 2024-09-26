<?php

namespace App\ETLProcessing\Core;

use App\Models\ETL\RawModel;

interface RawToCoreProcessor {
    public function process(RawModel $rawModel);
}
