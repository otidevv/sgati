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
        Schema::table('servers', function (Blueprint $table) {
            // Tipo de servidor
            $table->enum('host_type', ['physical', 'virtual', 'cloud'])
                  ->default('physical')
                  ->after('function');

            // Recursos de hardware
            $table->unsignedSmallInteger('cpu_cores')->nullable()->after('host_type');   // núcleos
            $table->unsignedSmallInteger('ram_gb')->nullable()->after('cpu_cores');      // RAM en GB
            $table->unsignedSmallInteger('storage_gb')->nullable()->after('ram_gb');     // almacenamiento en GB

            // Nube (solo si host_type = 'cloud')
            $table->enum('cloud_provider', [
                'aws', 'gcp', 'azure', 'digitalocean', 'linode', 'other',
            ])->nullable()->after('storage_gb');
            $table->string('cloud_region', 50)->nullable()->after('cloud_provider');     // us-east-1, sa-east-1...
            $table->string('cloud_instance', 100)->nullable()->after('cloud_region');    // t3.medium, e2-standard-2...
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->dropColumn([
                'host_type', 'cpu_cores', 'ram_gb', 'storage_gb',
                'cloud_provider', 'cloud_region', 'cloud_instance',
            ]);
        });
    }
};
