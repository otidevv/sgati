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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete()->after('id');
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete()->after('password');
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete()->after('role_id');
            $table->boolean('is_active')->default(true)->after('area_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('persona_id');
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('area_id');
            $table->dropColumn('is_active');
        });
    }
};
