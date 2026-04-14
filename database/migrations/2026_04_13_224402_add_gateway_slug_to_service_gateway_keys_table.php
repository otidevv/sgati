<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_gateway_keys', function (Blueprint $table) {
            // Slug único por consumidor — genera su propia URL pública del gateway
            $table->string('gateway_slug', 80)->nullable()->unique()->after('key_hash');
        });
    }

    public function down(): void
    {
        Schema::table('service_gateway_keys', function (Blueprint $table) {
            $table->dropUnique(['gateway_slug']);
            $table->dropColumn('gateway_slug');
        });
    }
};
