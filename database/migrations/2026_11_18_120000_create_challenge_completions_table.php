<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('challenge_code');
            $table->date('period_start');                 // awal hari/minggu tantangan
            $table->unsignedInteger('xp_reward');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Satu hadiah per tantangan per periode.
            $table->unique(['user_id', 'challenge_code', 'period_start'], 'challenge_once_per_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_completions');
    }
};
