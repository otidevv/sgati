<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_gateway_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_service_id')->constrained('system_services')->cascadeOnDelete();
            $table->foreignId('gateway_key_id')->nullable()->constrained('service_gateway_keys')->nullOnDelete();
            $table->string('method', 10);
            $table->string('path_info', 500)->nullable();
            $table->text('query_string')->nullable();
            $table->string('ip_address', 45);
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->unsignedSmallInteger('response_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at');                    // solo created_at, sin updated_at

            $table->index(['system_service_id', 'created_at']);
            $table->index(['gateway_key_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_gateway_logs');
    }
};
