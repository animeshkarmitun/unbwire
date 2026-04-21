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
        Schema::table('categories', function (Blueprint $col) {
            $col->boolean('is_default')->default(false)->after('parent_id');
        });

        Schema::table('authors', function (Blueprint $col) {
            $col->boolean('is_default')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $col) {
            $col->dropColumn('is_default');
        });

        Schema::table('authors', function (Blueprint $col) {
            $col->dropColumn('is_default');
        });
    }
};
