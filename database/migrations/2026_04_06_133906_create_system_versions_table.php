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
        Schema::create('system_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
            $table->string('version', 20);
            $table->date('release_date');
            $table->enum('environment', ['production', 'staging', 'development'])->default('production');
            $table->text('changes')->nullable();
            $table->string('git_commit', 100)->nullable();
            $table->string('git_branch', 100)->nullable();
            $table->foreignId('deployed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_versions');
    }
};
