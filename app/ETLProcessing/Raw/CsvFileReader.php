<?php

namespace App\ETLProcessing\Raw;

use Illuminate\Support\Collection;
use ParseCsv\Csv;

class CsvFileReader extends TextFileReader {
    /**
     * Parse string as CSV with automatic detection of separators and field enclosures
     *
     * @param string $fileContent
     * @return Collection
     */
    protected function processCleanedData(string $fileContent): Collection {
        $csv = new Csv();
        $csv->file_data = $fileContent;
        $csv->auto();

        return collect($csv->data);
    }
}
