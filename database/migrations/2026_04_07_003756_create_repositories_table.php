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
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();

            // Sistema al que pertenece (opcional — puede ser un repo sin sistema aún)
            $table->foreignId('system_id')
                  ->nullable()
                  ->constrained('systems')
                  ->nullOnDelete();

            // Identificación
            $table->string('name', 150);                        // alias: "CEPRE Frontend"
            $table->enum('provider', [
                'github', 'gitlab', 'bitbucket', 'gitea', 'other',
            ])->default('github');

            // Acceso
            $table->string('repo_url', 255)->nullable();        // https://github.com/org/repo
            $table->string('username', 150)->nullable();        // usuario o email de la cuenta
            $table->text('token')->nullable();                  // token/password — encriptado en modelo
            $table->enum('credential_type', [
                'token',        // Personal Access Token (más común)
                'password',     // usuario + contraseña
                'deploy_key',   // SSH deploy key
                'oauth',        // OAuth app
            ])->default('token');

            // Detalles del repo
            $table->string('default_branch', 100)->default('main');
            $table->enum('repo_type', [
                'personal',     // cuenta personal
                'organization', // organización/empresa
            ])->default('organization');
            $table->boolean('is_private')->default(true);

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};
