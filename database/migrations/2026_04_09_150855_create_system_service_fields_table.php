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
        Schema::create('system_service_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_service_id')->constrained('system_services')->cascadeOnDelete();
            $table->enum('direction', ['request', 'response']);
            $table->string('field_name', 100);
            $table->enum('field_type', ['string', 'integer', 'boolean', 'number', 'array', 'object', 'date', 'datetime', 'uuid', 'other'])->default('string');
            $table->boolean('is_required')->default(false);
            $table->string('description', 255)->nullable();
            $table->string('example_value', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_service_fields');
    }
};
