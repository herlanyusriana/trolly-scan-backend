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
        Schema::table('trolley_movements', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vehicle_snapshot')->nullable();
            $table->string('driver_snapshot')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trolley_movements', function (Blueprint $table) {
            $table->dropColumn(['vehicle_snapshot', 'driver_snapshot']);
            $table->dropConstrainedForeignId('vehicle_id');
            $table->dropConstrainedForeignId('driver_id');
        });
    }
};
