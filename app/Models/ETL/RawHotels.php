<?php

namespace App\Models\ETL;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $row_within_file
 * @property bool $processed
 * @property string|null $file_name
 * @property string $execution_uid
 * @method static \Illuminate\Database\Eloquent\Builder|RawHotels newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RawHotels newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RawHotels query()
 * @property string|null $hotel_name
 * @property string|null $image
 * @property string|null $city
 * @property string|null $address
 * @property string|null $description
 * @property string|null $stars
 * @mixin \Eloquent
 */
class RawHotels extends RawModel {
    protected $table = 'raw__hotels';
}
