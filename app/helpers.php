<?php

/**
 * Apply general rules for database column name in different RDBMS to use it without quoting.
 *
 * @param string $field
 * @return string
 */
function prepareFieldName(string $field): string {
    // remove quotes and replace simple chars with underscores
    $preparedField = trim(str_replace([
        ' ',
        '.',
        '(',
        ')'
    ], '_', $field), " \n\r\t\v\0_\"'");

    // remove non-alpha numeric chars
    $preparedField = preg_replace("/[^\w]/", '', $preparedField);

    // make sure name does start with letter:
    $preparedField = preg_replace("/^\d+/", '', $preparedField);

    return strtolower($preparedField);
}

/**
 * Clean text content by removing symbols that may break parsing or lead to corrupted data.
 *
 * @param string $text
 * @return string
 */
function cleanTextFileContent(string $text) : string {
    if (!$text) {
        return '';
    }

    // we need plain text, not html entities
    $text = html_entity_decode($text);

    // remove bom
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);

    // remove non-printable characters
    $text = preg_replace('/[^[:print:]\r\n]/', '', $text);

    // remove broken utf-8 symbols
    $encoding = mb_detect_encoding($text, mb_detect_order());

    if($encoding === "UTF-8") {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }

    return iconv($encoding, "UTF-8//IGNORE", $text) ?: '';
}
