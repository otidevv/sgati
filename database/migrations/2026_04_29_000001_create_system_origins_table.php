<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_origins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained()->cascadeOnDelete();

            $table->enum('origin_type', ['donated', 'third_party', 'internal', 'state']);

            // ── Donado ────────────────────────────────────────────
            $table->string('donor_name', 150)->nullable();
            $table->string('donor_institution', 200)->nullable();
            $table->enum('donation_type', ['thesis', 'research_project', 'direct_donation', 'agreement'])->nullable();
            $table->string('thesis_title', 300)->nullable();
            $table->string('thesis_author', 200)->nullable();
            $table->string('thesis_university', 200)->nullable();
            $table->date('donation_date')->nullable();
            $table->string('donation_document', 100)->nullable(); // N° de resolución/acta

            // ── Creado por terceros ────────────────────────────────
            $table->string('company_name', 200)->nullable();
            $table->string('contact_name', 150)->nullable();
            $table->string('contact_email', 150)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('contract_number', 100)->nullable();
            $table->date('contract_date')->nullable();
            $table->decimal('contract_value', 12, 2)->nullable();
            $table->date('warranty_expiry')->nullable();

            // ── Desarrollo interno ────────────────────────────────
            $table->string('team_name', 150)->nullable();
            $table->date('dev_start_date')->nullable();
            $table->date('dev_end_date')->nullable();
            $table->enum('methodology', ['scrum', 'kanban', 'waterfall', 'rup', 'other'])->nullable();
            $table->string('project_code', 60)->nullable();

            // ── Sistema del Estado ────────────────────────────────
            $table->string('state_entity', 200)->nullable();
            $table->string('state_entity_code', 50)->nullable();
            $table->string('state_system_code', 100)->nullable();
            $table->string('state_official_url', 255)->nullable();
            $table->text('legal_basis')->nullable();
            $table->date('state_implementation_date')->nullable();

            // ── Común ─────────────────────────────────────────────
            $table->text('origin_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_origins');
    }
};
