<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_infrastructure', function (Blueprint $table) {
            $table->foreignId('ssl_certificate_id')
                  ->nullable()
                  ->after('ssl_expiry')
                  ->constrained('ssl_certificates')
                  ->nullOnDelete();
            $table->date('ssl_custom_expiry')->nullable()->after('ssl_certificate_id');
        });
    }

    public function down(): void
    {
        Schema::table('system_infrastructure', function (Blueprint $table) {
            $table->dropForeign(['ssl_certificate_id']);
            $table->dropColumn(['ssl_certificate_id', 'ssl_custom_expiry']);
        });
    }
};
