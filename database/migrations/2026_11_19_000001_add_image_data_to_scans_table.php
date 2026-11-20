<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('scans', 'image_data')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->mediumText('image_data')->nullable();
            });
        }

        Schema::table('scans', function (Blueprint $table) {
            $table->string('image_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('scans', 'image_data')) {
            Schema::table('scans', function (Blueprint $table) {
                $table->dropColumn('image_data');
            });
        }
    }
};