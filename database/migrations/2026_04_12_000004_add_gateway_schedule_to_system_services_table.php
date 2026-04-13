<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_services', function (Blueprint $table) {
            $table->time('gateway_active_from')->nullable()->after('gateway_rate_per_day');
            $table->time('gateway_active_to')->nullable()->after('gateway_active_from');
        });
    }

    public function down(): void
    {
        Schema::table('system_services', function (Blueprint $table) {
            $table->dropColumn(['gateway_active_from', 'gateway_active_to']);
        });
    }
};
