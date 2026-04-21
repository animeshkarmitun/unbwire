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
        Schema::create('ap_photo_tag_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ap_photo_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->foreign('ap_photo_id')->references('id')->on('ap_photos')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('ap_photo_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ap_photo_tag_relations');
    }
};
