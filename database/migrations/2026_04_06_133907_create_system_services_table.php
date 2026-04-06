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
