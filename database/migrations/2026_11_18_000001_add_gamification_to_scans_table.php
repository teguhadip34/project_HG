
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('scans', 'xp_earned')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->unsignedInteger('xp_earned')
                    ->default(0)
                    ->after('confidence');
            });
        }

        if (! Schema::hasColumn('scans', 'image_hash')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->string('image_hash', 40)
                    ->nullable()
                    ->index()
                    ->after('xp_earned');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('scans', 'image_hash')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->dropColumn('image_hash');
            });
        }

        if (Schema::hasColumn('scans', 'xp_earned')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->dropColumn('xp_earned');
            });
        }
    }
};
