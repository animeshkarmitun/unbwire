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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade');
            $table->string('session_id', 64)->index();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent');
            $table->string('referrer')->nullable();
            $table->enum('referrer_type', ['direct', 'organic', 'social', 'referral', 'email', 'other'])->default('direct');
            $table->string('landing_page');
            $table->string('exit_page')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->default('desktop');
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->boolean('is_bot')->default(false);
            $table->integer('duration')->nullable()->comment('Session duration in seconds');
            $table->integer('page_views_count')->default(1);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            
            $table->index(['started_at', 'country_code']);
            $table->index(['referrer_type', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
