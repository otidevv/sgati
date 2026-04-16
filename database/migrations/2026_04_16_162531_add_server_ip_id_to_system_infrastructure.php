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
        Schema::table('system_infrastructure', function (Blueprint $table) {
            $table->foreignId('server_ip_id')
                  ->nullable()
                  ->after('server_id')
                  ->constrained('server_ips')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('system_infrastructure', function (Blueprint $table) {
            $table->dropForeign(['server_ip_id']);
            $table->dropColumn('server_ip_id');
        });
    }
};
