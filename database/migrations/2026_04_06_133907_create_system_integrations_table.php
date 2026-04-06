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
        Schema::create('system_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_system_id')->constrained('systems')->cascadeOnDelete();
            $table->foreignId('target_system_id')->constrained('systems')->cascadeOnDelete();
            $table->enum('connection_type', ['api', 'direct_db', 'file', 'sftp', 'other']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_integrations');
    }
};
