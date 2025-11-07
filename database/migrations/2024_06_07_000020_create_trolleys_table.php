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
        Schema::create('trolleys', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['internal', 'external'])->default('internal');
            $table->enum('kind', ['reinforce', 'backplate', 'compbase'])->default('reinforce');
            $table->enum('status', ['in', 'out'])->default('in');
            $table->unsignedInteger('capacity')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trolleys');
    }
};
