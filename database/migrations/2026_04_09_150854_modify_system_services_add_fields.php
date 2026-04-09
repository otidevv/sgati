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
        Schema::table('system_services', function (Blueprint $table) {
            // Entorno y versión
            $table->enum('environment', ['production', 'staging', 'development'])->default('production')->after('is_active');
            $table->string('version', 20)->nullable()->after('environment');

            // Proveedor
            $table->enum('provider_type', ['internal', 'external'])->nullable()->after('version');
            $table->foreignId('provider_system_id')->nullable()->after('provider_type')
                  ->constrained('systems')->nullOnDelete();
            $table->string('provider_name', 150)->nullable()->after('provider_system_id');

            // Vigencia
            $table->date('valid_from')->nullable()->after('provider_name');
            $table->date('valid_until')->nullable()->after('valid_from');

            // Credenciales (encriptadas)
            $table->text('api_key')->nullable()->after('valid_until');
            $table->text('api_secret')->nullable()->after('api_key');
            $table->text('token')->nullable()->after('api_secret');
            $table->date('token_expires_at')->nullable()->after('token');

            // Quién solicitó
            $table->foreignId('requested_by_persona_id')->nullable()->after('token_expires_at')
                  ->constrained('personas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('system_services', function (Blueprint $table) {
            $table->dropForeign(['provider_system_id']);
            $table->dropForeign(['requested_by_persona_id']);
            $table->dropColumn([
                'environment', 'version',
                'provider_type', 'provider_system_id', 'provider_name',
                'valid_from', 'valid_until',
                'api_key', 'api_secret', 'token', 'token_expires_at',
                'requested_by_persona_id',
            ]);
        });
    }
};
