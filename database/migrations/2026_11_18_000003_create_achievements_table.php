<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description');
            $table->string('icon', 16);
            $table->string('metric');                       // lihat config/gamification.php
            $table->unsignedInteger('target');
            $table->unsignedInteger('xp_reward')->default(0);
            $table->timestamp('unlocked_at')->nullable();   // null = masih terkunci
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
