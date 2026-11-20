<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('scans', 'detected_label')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->string('detected_label')->nullable()->after('waste_type_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('scans', 'detected_label')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->dropColumn('detected_label');
            });
        }
    }
};
