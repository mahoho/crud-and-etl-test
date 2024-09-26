<?php

namespace App\Console\Commands;

use App\ETLProcessing\DataSourceFactory;
use App\ETLProcessing\Raw\FileReaderFactory;
use App\Models\ETL\RawExecutionProcess;
use App\Models\ETL\RawModel;
use Artisan;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class ImportRawFileCommand extends Command {
    protected $signature = 'etl:import-raw-file {file} {--source-name=hotels} {--execution-uid=} {--process-core=true}';

    protected $description = 'Import file into raw database';

    public function handle(): void {
        $file = $this->argument('file');

        if (!$file || !file_exists($file)) {
            $this->error('File does not exists');
            return ;
        }

        // just to make sure system will handle big files
        ini_set('memory_limit', '4G');
        ini_set('max_execution_time', 0);

        $executionUid = $this->option('execution-uid') ?? Uuid::uuid4()->toString();

        $sourceName = $this->option('source-name');

        /** @var RawExecutionProcess $processStatus */
        $processStatus = RawExecutionProcess::where(['execution_uid' => $executionUid])->firstOrNew();
        $processStatus->execution_uid = $executionUid;
        $processStatus->file_name = $file;
        $processStatus->rows_failed = 0;
        $processStatus->rows_processed = 0;
        $processStatus->source_name = $sourceName;
        $processStatus->full_command = $this->getFullCommand();
        $processStatus->save();

        $fileReader = FileReaderFactory::getFileReader($file);

        $rows = $fileReader->process($file);

        $totalLines = $rows->count();
        $processStatus->total_rows = $totalLines;

        $processStatus->save();

        $modelClass = DataSourceFactory::getRawModelClass($sourceName);

        if (!$modelClass) {
            $this->error("Raw model class not specified for source: '$sourceName'.");
            return;
        }

        $systemColumns = [
            'row_within_file',
            'updated_at',
            'execution_uid',
            'file_name',
            'processed',
        ];

        foreach ($rows as $index => $data) {
            $indexPlusOne = $index + 1;

            /** @var RawModel $model */
            $model = new $modelClass();

            foreach ($data as $field => $value) {
                $columnPrepared = prepareFieldName($field);

                if(!$columnPrepared || in_array($columnPrepared, $systemColumns)) {
                    continue;
                }

                $model->{$columnPrepared} = $model->{$columnPrepared} ?: $value;
            }

            $model->execution_uid = $executionUid;
            $model->file_name = $processStatus->file_name;
            $model->row_within_file = $indexPlusOne;
            $model->save();

            $now = date('Y-m-d H:i:s');
            $this->line("[$now] Processed Raw: $indexPlusOne/$totalLines");

            $processStatus->rows_processed++;
            $processStatus->save();
        }

        $processCore = filter_var($this->option('process-core'), FILTER_VALIDATE_BOOLEAN);

        if($processCore) {
            Artisan::call(RunCoreProcessingCommand::class, [
                'execution-uid' => $executionUid,
            ]);
        }
    }

    /**
     * Get full command for simplifying debug
     *
     * @return string
     */
    protected function getFullCommand(): string {
        $baseCommand = trim(explode('{', $this->signature)[0]);

        foreach ($this->options() as $key => $value) {
            if(is_null($value)){
                continue;
            }

            if(is_bool($value)) {
                $value = $value ? "true" : 'false';
            }

            $baseCommand .= " --$key=$value";
        }

        // depending on env, path to php exec could be different and not always present in PATH
        $php = config('app.bin.php');
        $artisan = base_path('artisan');

        return "$php $artisan $baseCommand";
    }
}
