<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trolley_movements', function (Blueprint $table) {
            $table->unsignedInteger('sequence_number')
                ->nullable()
                ->after('status');
        });

        DB::transaction(function (): void {
            $counters = [];

            DB::table('trolley_movements')
                ->select(['id', 'checked_out_at'])
                ->whereNotNull('checked_out_at')
                ->orderBy('checked_out_at')
                ->chunk(500, function ($movements) use (&$counters): void {
                    foreach ($movements as $movement) {
                        $timestamp = Carbon::parse($movement->checked_out_at);

                        $periodStart = $timestamp->copy()->setTime(6, 0);
                        if ($timestamp->lt($periodStart)) {
                            $periodStart->subDay();
                        }

                        $key = $periodStart->toDateString();

                        $counters[$key] = ($counters[$key] ?? 0) + 1;

                        DB::table('trolley_movements')
                            ->where('id', $movement->id)
                            ->update(['sequence_number' => $counters[$key]]);
                    }
                });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trolley_movements', function (Blueprint $table) {
            $table->dropColumn('sequence_number');
        });
    }
};
