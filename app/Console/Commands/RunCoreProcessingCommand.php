<?php

namespace App\Console\Commands;

use App\ETLProcessing\Core\RawToCoreProcessor;
use App\ETLProcessing\DataSourceFactory;
use App\Models\ETL\RawExecutionProcess;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RunCoreProcessingCommand extends Command {
    protected $signature = 'etl:core-processing {execution-uid} {--id=} {--id-from=} {--id-to=}';

    protected $description = 'Run core processing from already imported core data';

    public function handle(): void {
        $executionUid = $this->argument('execution-uid');

        $processStatus = RawExecutionProcess::where(['execution_uid' => $executionUid])->first();

        if(!$processStatus) {
            $this->error("Wrong execution UID: '$executionUid'.");
            return;
        }

        $sourceName = $processStatus->source_name;

        $coreProcessorClass = DataSourceFactory::getCoreProcessor($sourceName);

        if(!$coreProcessorClass) {
            $this->error("Core processor class not specified for source: '$sourceName'.");
            return;
        }

        /** @var RawToCoreProcessor $coreProcessor */
        $coreProcessor = new $coreProcessorClass;

        $processStatus->rows_failed = 0;
        $processStatus->rows_processed = 0;
        $processStatus->save();

        $rawModelClass = DataSourceFactory::getRawModelClass($sourceName);

        /** @var Builder $q */
        $q = $rawModelClass::query();

        $id = $this->option('id');
        $idFrom = $this->option('id-from');
        $idTo = $this->option('id-from');

        if($id) {
            $q->where('id', '=', $id);
        } else {
            if($idFrom) {
                $q->where('id', '>=', $idFrom);
            }

            if($idTo) {
                $q->where('id', '<=', $idTo);
            }
        }

        $rawModels = $q->get();

        $totalLines = $rawModels->count();

        foreach ($rawModels as $index => $rawModel) {
            $indexPlusOne = $index + 1;

            $errors = [];

            try {
                $coreProcessor->process($rawModel);
                $processStatus->rows_processed++;
                $rawModel->processed = 1;
                $rawModel->save();
            } catch (\Throwable $e) {
                // ETL should not stop if one or several rows are problematic
                $processStatus->rows_failed++;
                $errors[] = "[Row $indexPlusOne]: " . $e->getMessage() . ' on ' . $e->getFile() . ' on line ' . $e->getLine();
            }

            $now = date('Y-m-d H:i:s');
            $this->line("[$now] Processed $sourceName Core: $indexPlusOne/$totalLines");

            $processStatus->errors = $errors;
            $processStatus->save();
        }
    }
}
