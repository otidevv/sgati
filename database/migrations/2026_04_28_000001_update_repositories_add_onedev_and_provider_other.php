<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL usa CHECK constraint para emular ENUM
        DB::statement("ALTER TABLE repositories DROP CONSTRAINT IF EXISTS repositories_provider_check");
        DB::statement("ALTER TABLE repositories ADD CONSTRAINT repositories_provider_check CHECK (provider IN ('github','gitlab','bitbucket','gitea','onedev','other'))");

        Schema::table('repositories', function (Blueprint $table) {
            $table->string('provider_other', 100)->nullable()->after('provider');
        });
    }

    public function down(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->dropColumn('provider_other');
        });

        DB::statement("ALTER TABLE repositories DROP CONSTRAINT IF EXISTS repositories_provider_check");
        DB::statement("ALTER TABLE repositories ADD CONSTRAINT repositories_provider_check CHECK (provider IN ('github','gitlab','bitbucket','gitea','other'))");
    }
};
