<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('server_ip_ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_ip_id')->constrained('server_ips')->cascadeOnDelete();
            $table->unsignedSmallInteger('port');
            $table->enum('protocol', ['tcp', 'udp', 'both'])->default('tcp');
            $table->string('description', 200)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['server_ip_id', 'port']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('server_ip_ports');
    }
};
