<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiamos a VARCHAR para soporte en PostgreSQL/MySQL
        DB::statement("ALTER TABLE system_responsibles ALTER COLUMN level TYPE VARCHAR(50)");

        // Actualizamos valores existentes del enum anterior al más cercano
        DB::statement("UPDATE system_responsibles SET level = 'soporte'     WHERE level = 'soporte'");
        DB::statement("UPDATE system_responsibles SET level = 'supervision' WHERE level = 'supervision'");
        DB::statement("UPDATE system_responsibles SET level = 'administrador' WHERE level = 'principal'");
        DB::statement("UPDATE system_responsibles SET level = 'soporte'     WHERE level = 'operador'");

        // Eliminar restricción CHECK anterior si existe
        DB::statement("ALTER TABLE system_responsibles DROP CONSTRAINT IF EXISTS system_responsibles_level_check");

        // Nueva restricción CHECK con los niveles del sistema
        DB::statement("ALTER TABLE system_responsibles ADD CONSTRAINT system_responsibles_level_check
            CHECK (level IN ('lider_proyecto','desarrollador','mantenimiento','administrador','analista','soporte','supervision'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE system_responsibles DROP CONSTRAINT IF EXISTS system_responsibles_level_check");
        DB::statement("ALTER TABLE system_responsibles ADD CONSTRAINT system_responsibles_level_check
            CHECK (level IN ('principal','soporte','supervision','operador'))");
    }
};
