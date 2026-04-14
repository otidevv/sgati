<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_gateway_keys', function (Blueprint $table) {
            // Tipo de consumidor: interno (sistema SGATI) o externo (organización/persona externa)
            $table->enum('consumer_type', ['internal', 'external'])->default('external')->after('notes');
            // Si es interno: FK al sistema consumidor
            $table->foreignId('requesting_system_id')
                  ->nullable()
                  ->after('consumer_type')
                  ->constrained('systems')
                  ->nullOnDelete();
            // Si es externo: nombre de la organización o aplicación externa
            $table->string('consumer_organization', 150)->nullable()->after('requesting_system_id');
            // Propósito / descripción del uso (más específico que notes)
            $table->string('purpose', 255)->nullable()->after('consumer_organization');
        });
    }

    public function down(): void
    {
        Schema::table('service_gateway_keys', function (Blueprint $table) {
            $table->dropForeign(['requesting_system_id']);
            $table->dropColumn(['consumer_type', 'requesting_system_id', 'consumer_organization', 'purpose']);
        });
    }
};
