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
        Schema::table('system_infrastructure', function (Blueprint $table) {
            // Vincular al servidor que aloja este sistema
            $table->foreignId('server_id')
                  ->nullable()
                  ->after('system_id')
                  ->constrained('servers')
                  ->nullOnDelete();

            // Eliminar campos que ahora viven en `servers`
            $table->dropColumn(['server_name', 'server_os', 'server_ip']);
        });
    }

    public function down(): void
    {
        Schema::table('system_infrastructure', function (Blueprint $table) {
            $table->dropForeign(['server_id']);
            $table->dropColumn('server_id');

            $table->string('server_name', 100)->nullable();
            $table->string('server_os', 100)->nullable();
            $table->string('server_ip', 45)->nullable();
        });
    }
};
