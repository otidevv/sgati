<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('server_ips', function (Blueprint $table) {
            $table->dropUnique(['server_id', 'ip_address']);
            $table->unsignedSmallInteger('port')->nullable()->after('ip_address');
            // La unicidad ahora es (server_id, ip_address, port): misma IP con distinto puerto es válida
            $table->unique(['server_id', 'ip_address', 'port']);
        });
    }

    public function down(): void
    {
        Schema::table('server_ips', function (Blueprint $table) {
            $table->dropUnique(['server_id', 'ip_address', 'port']);
            $table->dropColumn('port');
            $table->unique(['server_id', 'ip_address']);
        });
    }
};
