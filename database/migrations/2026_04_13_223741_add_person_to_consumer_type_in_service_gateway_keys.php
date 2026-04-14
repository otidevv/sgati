<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL: reemplazar el check constraint del enum para incluir 'person'
        DB::statement("ALTER TABLE service_gateway_keys DROP CONSTRAINT IF EXISTS service_gateway_keys_consumer_type_check");
        DB::statement("ALTER TABLE service_gateway_keys ADD CONSTRAINT service_gateway_keys_consumer_type_check CHECK (consumer_type IN ('internal','external','person'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE service_gateway_keys DROP CONSTRAINT IF EXISTS service_gateway_keys_consumer_type_check");
        DB::statement("ALTER TABLE service_gateway_keys ADD CONSTRAINT service_gateway_keys_consumer_type_check CHECK (consumer_type IN ('internal','external'))");
    }
};
