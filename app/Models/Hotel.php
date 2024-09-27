<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string|null $image
 * @property string|null $description
 * @property int $city_id
 * @property string $address
 * @property int $stars
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\City|null $city
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel withoutTrashed()
 * @mixin \Eloquent
 */
class Hotel extends Model {
    use SoftDeletes;
    use HasFactory;

    protected $table = 'hotels';

    protected $fillable = [
        'name',
        'city_id',
        'address',
        'stars',
        'description',
    ];

    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'city_id');
    }
}
