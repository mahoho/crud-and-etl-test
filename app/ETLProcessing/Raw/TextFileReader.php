<?php

namespace App\ETLProcessing\Raw;

use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Raw file reader gets file content and re
 */
abstract class TextFileReader implements RawFileReader {

    public function process(string $filename): Collection {
        $fileContent = file_get_contents($filename);
        $cleanedFileContents = cleanTextFileContent($fileContent);

        if(!$cleanedFileContents) {
            throw new InvalidArgumentException("File $filename is empty");
        }

        return $this->processCleanedData($cleanedFileContents);
    }

    abstract protected function processCleanedData(string $fileContent): Collection;
}
