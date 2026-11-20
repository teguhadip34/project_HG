<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Status "terbuka" kini disimpan per-user di tabel user_achievements.
    public function up(): void
    {
        if (Schema::hasColumn('achievements', 'unlocked_at')) {
            Schema::table('achievements', function (Blueprint $table) {
                $table->dropColumn('unlocked_at');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('achievements', 'unlocked_at')) {
            Schema::table('achievements', function (Blueprint $table) {
                $table->timestamp('unlocked_at')->nullable();
            });
        }
    }
};
