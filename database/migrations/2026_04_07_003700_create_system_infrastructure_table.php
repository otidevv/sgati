<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_infrastructure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->unique()->constrained('systems')->cascadeOnDelete();
            $table->foreignId('server_id')->nullable()->constrained('servers')->nullOnDelete();
            $table->foreignId('server_ip_id')->nullable()->constrained('server_ips')->nullOnDelete();
            $table->string('public_ip', 45)->nullable();
            $table->string('system_url', 255)->nullable();
            $table->integer('port')->nullable();
            $table->string('web_server', 50)->nullable();
            $table->boolean('ssl_enabled')->default(false);
            $table->date('ssl_expiry')->nullable();
            $table->foreignId('ssl_certificate_id')->nullable()->constrained('ssl_certificates')->nullOnDelete();
            $table->date('ssl_custom_expiry')->nullable();
            $table->enum('environment', ['production', 'staging', 'development'])->default('production');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_infrastructure');
    }
};
