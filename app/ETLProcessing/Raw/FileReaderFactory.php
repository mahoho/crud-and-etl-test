<?php

namespace App\ETLProcessing\Raw;

use InvalidArgumentException;

class FileReaderFactory {
    public static function getFileReader(string $filename): RawFileReader {
        if(!file_exists($filename)) {
            throw new InvalidArgumentException("Raw file doesn't exists: $filename");
        }

        $mime = mime_content_type($filename);

        if(!$mime) {
            throw new InvalidArgumentException("Mime type not detected for raw file $filename");
        }

        if($mime === 'application/json') {
            return new JsonFileReader();
        }

        // text/plain could also be non-csv file, proper validation should occur probably
        if(in_array($mime, ['text/csv', 'text/plain'])) {
            return new CsvFileReader();
        }

        throw new InvalidArgumentException("Unsupported mime type: $mime");
    }
}
