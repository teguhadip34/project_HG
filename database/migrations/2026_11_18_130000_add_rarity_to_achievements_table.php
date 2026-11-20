<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('achievements', 'rarity')) {
            Schema::table('achievements', function (Blueprint $table) {
                $table->string('rarity', 20)->default('common')->after('xp_reward');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('achievements', 'rarity')) {
            Schema::table('achievements', function (Blueprint $table) {
                $table->dropColumn('rarity');
            });
        }
    }
};
