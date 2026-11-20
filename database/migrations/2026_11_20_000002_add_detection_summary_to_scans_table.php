<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('scans', 'detection_summary')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->json('detection_summary')->nullable()->after('detected_label');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('scans', 'detection_summary')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->dropColumn('detection_summary');
            });
        }
    }
};
