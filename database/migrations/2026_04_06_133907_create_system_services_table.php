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
        Schema::create('system_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained('systems')->cascadeOnDelete();
            $table->string('service_name', 100);
            $table->enum('service_type', ['rest_api', 'soap', 'sftp', 'smtp', 'ldap', 'database', 'other']);
            $table->string('endpoint_url', 255)->nullable();
            $table->enum('direction', ['consumed', 'exposed']);
            $table->string('auth_type', 50)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('environment', ['production', 'staging', 'development'])->default('production');
            $table->string('version', 20)->nullable();
            $table->enum('provider_type', ['internal', 'external'])->nullable();
            $table->foreignId('provider_system_id')->nullable()->constrained('systems')->nullOnDelete();
            $table->string('provider_name', 150)->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->text('api_key')->nullable();
            $table->text('api_secret')->nullable();
            $table->text('token')->nullable();
            $table->date('token_expires_at')->nullable();
            $table->foreignId('requested_by_persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_services');
    }
};
