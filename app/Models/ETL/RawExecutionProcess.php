<?php

namespace App\Models\ETL;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DataArchitecture\Raw\RawExecutionProcess
 * 
 * Indicates processing Raw -> Core for a specific execution
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RawExecutionProcess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RawExecutionProcess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RawExecutionProcess query()
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $execution_uid
 * @property string $file_name
 * @property string|null $comment
 * @property int|null $total_rows
 * @property int|null $rows_processed
 * @property int|null $rows_failed
 * @property bool $active
 * @property string|null $source_name
 * @property array|null $errors
 * @property string|null $full_command
 * @mixin \Eloquent
 */
class RawExecutionProcess extends Model {
    protected $table = 'raw__execution_process';

    protected $casts = [
        'active'         => 'boolean',
        'total_rows'     => 'int',
        'rows_processed' => 'int',
        'rows_failed'    => 'int',
        'errors'         => 'json',
    ];
}
