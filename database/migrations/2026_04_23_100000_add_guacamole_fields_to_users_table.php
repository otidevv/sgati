<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('guacamole_username', 150)->nullable()->after('session_token');
            $table->text('guacamole_password')->nullable()->after('guacamole_username');   // encriptado
            $table->timestamp('guacamole_synced_at')->nullable()->after('guacamole_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['guacamole_username', 'guacamole_password', 'guacamole_synced_at']);
        });
    }
};
