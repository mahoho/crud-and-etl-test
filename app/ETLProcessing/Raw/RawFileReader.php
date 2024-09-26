<?php

namespace App\ETLProcessing\Raw;

use Illuminate\Support\Collection;

interface RawFileReader {
    public function process(string $filename): Collection;
}
