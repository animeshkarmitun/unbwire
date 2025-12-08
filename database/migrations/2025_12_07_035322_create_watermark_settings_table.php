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
        Schema::create('watermark_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->string('watermark_image')->nullable()->comment('Path to watermark image');
            $table->integer('watermark_size')->default(20)->comment('Watermark size as percentage (1-100)');
            $table->integer('opacity')->default(100)->comment('Watermark opacity (1-100)');
            $table->integer('offset')->default(10)->comment('Offset from edges in pixels');
            $table->string('position')->default('center')->comment('Position: center, top-left, top-center, top-right, middle-left, middle-right, bottom-left, bottom-center, bottom-right');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watermark_settings');
    }
};
