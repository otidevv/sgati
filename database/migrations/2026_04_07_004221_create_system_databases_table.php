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
            $table->foreignId('database_server_id')->nullable()->constrained('database_servers')->nullOnDelete();
            $table->string('db_name', 100);
            $table->enum('engine', ['postgresql', 'mysql', 'mariadb', 'oracle', 'sqlserver', 'sqlite', 'mongodb', 'other']);
            $table->string('schema_name', 100)->nullable();
            $table->string('db_user', 100)->nullable();
            $table->text('db_password')->nullable();
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
