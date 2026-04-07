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
        Schema::create('server_ips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('servers')->cascadeOnDelete();
            $table->string('ip_address', 45);                          // IPv4 o IPv6
            $table->enum('type', ['private', 'public'])->default('private');
            $table->string('interface', 50)->nullable();               // eth0, eth1, ens3...
            $table->boolean('is_primary')->default(false);             // IP principal del servidor
            $table->text('notes')->nullable();
            $table->timestamps();

            // Evitar IPs duplicadas en el mismo servidor
            $table->unique(['server_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_ips');
    }
};
