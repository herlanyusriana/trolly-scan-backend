<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('trolleys', 'kind')) {
            Schema::table('trolleys', function (Blueprint $table): void {
                $table
                    ->enum('kind', ['reinforce', 'backplate', 'compbase'])
                    ->default('reinforce')
                    ->after('type');
            });

            DB::table('trolleys')
                ->where('type', 'external')
                ->update(['kind' => 'backplate']);

            DB::table('trolleys')
                ->where('type', 'internal')
                ->update(['kind' => 'reinforce']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('trolleys', 'kind')) {
            Schema::table('trolleys', function (Blueprint $table): void {
                $table->dropColumn('kind');
            });
        }
    }
};
