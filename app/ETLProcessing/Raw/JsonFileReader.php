<?php

namespace App\ETLProcessing\Raw;

use Illuminate\Support\Collection;

class JsonFileReader extends TextFileReader {

    /**
     * Gets json file content as collection
     *
     * @param string $fileContent
     * @return Collection
     * @throws \JsonException
     */
    protected function processCleanedData(string $fileContent): Collection {
        // json_validate threats numeric and boolean literals as valid json
        if(!in_array($fileContent[0], ['{', '[']) || !json_validate($fileContent)){
            throw new \JsonException("JSON content is not valid in file $fileContent");
        }

        return collect(json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR));
    }
}
