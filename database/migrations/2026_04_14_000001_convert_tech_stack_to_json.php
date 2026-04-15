<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrar datos existentes (string → JSON array) antes de cambiar el tipo
        DB::table('systems')
            ->whereNotNull('tech_stack')
            ->where('tech_stack', '!=', '')
            ->get()
            ->each(function ($row) {
                // Si ya es JSON válido lo dejamos, si no lo convertimos desde CSV
                $decoded = json_decode($row->tech_stack, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $tags = array_values(array_filter(array_map('trim', explode(',', $row->tech_stack))));
                    DB::table('systems')->where('id', $row->id)->update([
                        'tech_stack' => json_encode($tags),
                    ]);
                }
            });

        Schema::table('systems', function (Blueprint $table) {
            $table->text('tech_stack')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            $table->string('tech_stack', 255)->nullable()->change();
        });
    }
};
