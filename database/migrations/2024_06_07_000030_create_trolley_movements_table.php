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
        Schema::create('trolley_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trolley_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mobile_user_id')->nullable()->constrained('mobile_users')->nullOnDelete();
            $table->foreignId('checked_out_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('checked_in_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->enum('status', ['out', 'in'])->default('out');
            $table->timestamp('checked_out_at');
            $table->timestamp('expected_return_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('destination')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trolley_movements');
    }
};
