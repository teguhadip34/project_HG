<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('scans', 'annotated_image_data')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->mediumText('annotated_image_data')->nullable()->after('image_data');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('scans', 'annotated_image_data')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->dropColumn('annotated_image_data');
            });
        }
    }
};
