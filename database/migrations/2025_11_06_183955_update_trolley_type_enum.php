<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE trolleys MODIFY type ENUM('internal','external','reinforce','backplate','compbase') NOT NULL DEFAULT 'internal'");

        DB::table('trolleys')
            ->whereIn('type', ['reinforce', 'compbase'])
            ->update(['type' => 'internal']);

        DB::table('trolleys')
            ->where('type', 'backplate')
            ->update(['type' => 'external']);

        DB::statement("ALTER TABLE trolleys MODIFY type ENUM('internal','external') NOT NULL DEFAULT 'internal'");

        if (! Schema::hasColumn('trolleys', 'kind')) {
            Schema::table('trolleys', function (Blueprint $table): void {
                $table->enum('kind', ['reinforce', 'backplate', 'compbase'])->default('reinforce')->after('type');
            });
        }

        DB::table('trolleys')
            ->where('type', 'internal')
            ->update(['kind' => 'reinforce']);

        DB::table('trolleys')
            ->where('type', 'external')
            ->update(['kind' => 'backplate']);
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
