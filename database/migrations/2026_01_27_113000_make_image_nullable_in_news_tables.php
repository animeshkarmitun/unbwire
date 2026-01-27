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
        Schema::table('news', function (Blueprint $table) {
            $table->text('image')->nullable()->change();
        });

        Schema::table('archived_news', function (Blueprint $table) {
            $table->text('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting back to not nullable might fail if there are null values, 
        // but for down() we generally try to reverse structure. 
        // If there are NULLs, this would fail in practice.
        Schema::table('news', function (Blueprint $table) {
            $table->text('image')->nullable(false)->change();
        });

        Schema::table('archived_news', function (Blueprint $table) {
            $table->text('image')->nullable(false)->change();
        });
    }
};
