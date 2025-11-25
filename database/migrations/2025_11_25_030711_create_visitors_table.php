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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_id', 64)->unique()->index();
            $table->timestamp('first_visit_at');
            $table->timestamp('last_visit_at');
            $table->integer('total_visits')->default(0);
            $table->integer('total_page_views')->default(0);
            $table->boolean('is_bot')->default(false);
            $table->text('user_agent')->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->default('desktop');
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('referrer')->nullable();
            $table->enum('referrer_type', ['direct', 'organic', 'social', 'referral', 'email', 'other'])->default('direct');
            $table->timestamps();
            
            $table->index(['country_code', 'last_visit_at']);
            $table->index(['referrer_type', 'last_visit_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
