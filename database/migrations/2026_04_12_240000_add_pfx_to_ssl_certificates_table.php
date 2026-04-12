<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ssl_certificates', function (Blueprint $table) {
            $table->string('pfx_file_path', 500)->nullable()->after('chain_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('ssl_certificates', function (Blueprint $table) {
            $table->dropColumn('pfx_file_path');
        });
    }
};
