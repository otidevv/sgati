<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_services', function (Blueprint $table) {
            $table->boolean('gateway_enabled')->default(false)->after('is_active');
            $table->string('gateway_slug', 80)->nullable()->unique()->after('gateway_enabled');
            $table->boolean('gateway_require_key')->default(false)->after('gateway_slug');
            $table->unsignedSmallInteger('gateway_rate_per_minute')->nullable()->after('gateway_require_key');
            $table->unsignedSmallInteger('gateway_rate_per_day')->nullable()->after('gateway_rate_per_minute');
        });
    }

    public function down(): void
    {
        Schema::table('system_services', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_enabled',
                'gateway_slug',
                'gateway_require_key',
                'gateway_rate_per_minute',
                'gateway_rate_per_day',
            ]);
        });
    }
};
