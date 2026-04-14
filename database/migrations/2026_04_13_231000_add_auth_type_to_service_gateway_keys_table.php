<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_gateway_keys', function (Blueprint $table) {
            // Tipo de autenticación requerida para este consumidor
            // bearer     → Authorization: Bearer <token>
            // api_key    → X-API-Key: <token>
            // query_param→ ?api_key=<token>
            // none       → sin autenticación (acceso abierto a este consumidor)
            $table->string('auth_type', 20)->default('bearer')->after('is_active');
        });

        // Poner constraint CHECK en PostgreSQL
        \DB::statement("ALTER TABLE service_gateway_keys
            ADD CONSTRAINT sgk_auth_type_check
            CHECK (auth_type IN ('bearer','api_key','query_param','none'))");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE service_gateway_keys DROP CONSTRAINT IF EXISTS sgk_auth_type_check");
        Schema::table('service_gateway_keys', function (Blueprint $table) {
            $table->dropColumn('auth_type');
        });
    }
};
