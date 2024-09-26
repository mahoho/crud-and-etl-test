<?php

namespace App\Models\ETL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Schema;

/**
 * App\Models\ETL\RawModel
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int $row_within_file
 * @property boolean $processed
 * @property string $file_name
 * @property string $execution_uid
 * @method static \Illuminate\Database\Eloquent\Builder|RawModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RawModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RawModel query()
 * @mixin \Eloquent
 */
abstract class RawModel extends Model {
    protected static array $columnsList;

    protected $casts = [
        'processed'       => 'boolean',
        'row_within_file' => 'int',
    ];

    protected static function booted() {
        self::retrieved(function (RawModel $model) {
            $model->prepareAttributes();
        });

        self::saving(function (RawModel $model) {
            $model->prepareAttributes();
        });

        $connectionName = (new static())->getConnectionName();
        $tableName = (new static())->getTable();

        // column name are case-insensitive in most RDBMS
        self::$columnsList[$tableName] = self::$columnsList[$tableName] ?? array_map('strtolower', Schema::connection($connectionName)->getColumnListing($tableName));
    }

    public function prepareAttributes(): void {
        $attributes = $this->getAttributes();

        $attributesWithFixedFieldNames = [];

        foreach ($attributes as $field => $value) {
            $preparedFieldName = prepareFieldName($field);

            if (!$preparedFieldName) {
                continue;
            }

            $this->addColumnIfMissing($field);
            $attributesWithFixedFieldNames[$preparedFieldName] = $value;
        }

        $this->setRawAttributes($attributesWithFixedFieldNames);
    }

    /**
     * Create generic raw table with fields if provided.
     *
     * @param $tableName
     * @param array $fields
     * @return void
     */
    public static function createRawTable($tableName, array $fields = []): void {
        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table) use ($fields) {
            $table->id();
            $table->timestamps();
            $table->integer('row_within_file');

            $fieldsPrepared = array_map(function ($field) {
                return prepareFieldName($field);
            }, $fields ?? []);

            foreach ($fieldsPrepared as $field) {
                $table->string($field, 4000)->nullable();
            }

            $table->string('processed')->default(0)->index();
            $table->string('file_name')->nullable();
            $table->string('execution_uid')->index();
        });
    }

    protected function hasColumn(string $field): bool {
        return in_array(strtolower($field), self::$columnsList[$this->table], true);
    }

    protected function addColumnIfMissing(string $field): void {
        if (!$field || $this->hasColumn($field)) {
            return;
        }

        try {
            Schema::connection($this->connection)->table($this->table, function (Blueprint $table) use ($field) {
                $table->string($field, 4000)->nullable();
            });
        } catch (QueryException $e) {
            if (Str::contains($e->getMessage(), 'Column names in each table must be unique')) {
                // could be because of racing condition by running multiple processes
                static::$columnsList[] = $field;
            } else {
                throw $e;
            }
        }

        static::$columnsList[] = $field;
    }
}
