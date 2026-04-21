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
        Schema::table('ap_photos', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });

        Schema::table('ap_photo_categories', function (Blueprint $table) {
            $table->string('language')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ap_photos', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
        });

        Schema::table('ap_photo_categories', function (Blueprint $table) {
            $table->string('language')->nullable(false)->change();
        });
    }
};
