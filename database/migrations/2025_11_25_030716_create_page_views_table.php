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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained('visitors')->onDelete('cascade');
            $table->string('url');
            $table->string('path')->index();
            $table->string('title')->nullable();
            $table->string('referrer')->nullable();
            $table->integer('load_time')->nullable()->comment('Page load time in milliseconds');
            $table->timestamp('viewed_at')->index();
            $table->timestamps();
            
            $table->index(['path', 'viewed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
