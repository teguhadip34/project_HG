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
// ..._create_waste_types_table.php
Schema::create('waste_types', function (Blueprint $table) {
    $table->id();
    $table->foreignId('waste_category_id')->constrained()->cascadeOnDelete();
    $table->string('label')->unique(); // label dari model AI, mis. "plastic"
    $table->string('name');            // nama tampil, mis. "Plastik"
    $table->text('disposal_tip')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_types');
    }
};
