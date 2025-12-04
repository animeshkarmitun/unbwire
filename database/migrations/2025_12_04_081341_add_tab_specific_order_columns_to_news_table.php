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
            $table->integer('breaking_order')->default(0)->after('order_position');
            $table->integer('slider_order')->default(0)->after('breaking_order');
            $table->integer('popular_order')->default(0)->after('slider_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['breaking_order', 'slider_order', 'popular_order']);
        });
    }
};
