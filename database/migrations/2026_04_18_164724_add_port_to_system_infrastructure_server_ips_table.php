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
        Schema::table('system_infrastructure_server_ips', function (Blueprint $table) {
            $table->unsignedSmallInteger('port')->nullable()->after('server_ip_id');
        });
    }

    public function down(): void
    {
        Schema::table('system_infrastructure_server_ips', function (Blueprint $table) {
            $table->dropColumn('port');
        });
    }
};
