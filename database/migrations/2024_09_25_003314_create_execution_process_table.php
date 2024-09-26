<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('raw__execution_process', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('execution_uid')->index();
            $table->string('file_name');
            $table->text('comment')->nullable();
            $table->integer('total_rows')->nullable();
            $table->integer('rows_processed')->nullable();
            $table->integer('rows_failed')->nullable();
            $table->boolean('active')->default(0);
            $table->string('source_name')->nullable()->index();
            $table->json('errors')->nullable();
            $table->text('full_command')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('raw__execution_process');
    }
};
