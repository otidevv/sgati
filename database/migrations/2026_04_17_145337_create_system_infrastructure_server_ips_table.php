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
        Schema::create('system_infrastructure_server_ips', function (Blueprint $table) {
            $table->foreignId('system_infrastructure_id')
                  ->constrained('system_infrastructure')
                  ->cascadeOnDelete();
            $table->foreignId('server_ip_id')
                  ->constrained('server_ips')
                  ->cascadeOnDelete();
            $table->primary(['system_infrastructure_id', 'server_ip_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_infrastructure_server_ips');
    }
};
