<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE system_databases DROP CONSTRAINT IF EXISTS system_databases_engine_check');
        DB::statement("ALTER TABLE system_databases ADD CONSTRAINT system_databases_engine_check CHECK (engine IN ('postgresql','mysql','mariadb','oracle','sqlserver','sqlite','mongodb','other'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE system_databases DROP CONSTRAINT IF EXISTS system_databases_engine_check');
        DB::statement("ALTER TABLE system_databases ADD CONSTRAINT system_databases_engine_check CHECK (engine IN ('postgresql','mysql','oracle','sqlserver','sqlite','mongodb','other'))");
    }
};
