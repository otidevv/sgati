<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_gateway_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_service_id')->constrained('system_services')->cascadeOnDelete();
            $table->string('name', 120);                         // "Aplicación X - oficina Y"
            $table->string('key_prefix', 8);                    // primeros 8 chars (para display)
            $table->string('key_hash', 64);                     // sha256 hex
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('rate_per_minute')->nullable(); // sobreescribe global
            $table->unsignedSmallInteger('rate_per_day')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('allowed_ips')->nullable();             // whitelist de IPs
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('total_requests')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['system_service_id', 'is_active']);
            $table->index('key_prefix');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_gateway_keys');
    }
};
