<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_databases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
            $table->string('db_name', 100);
            $table->enum('engine', ['postgresql', 'mysql', 'oracle', 'sqlserver', 'sqlite', 'mongodb', 'other']);
            $table->string('server_host', 100)->nullable();
            $table->integer('port')->nullable();
            $table->string('schema_name', 100)->nullable();
            $table->string('responsible', 100)->nullable();
            $table->enum('environment', ['production', 'staging', 'development'])->default('production');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_databases');
    }
};
