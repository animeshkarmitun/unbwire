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
        if (Schema::hasTable('support_ticket_attachments')) {
            Schema::dropIfExists('support_ticket_attachments');
        }
        
        Schema::create('support_ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('reply_id')->nullable();
            $table->string('file_name', 255);
            $table->string('original_name', 255);
            $table->string('file_path', 500);
            $table->bigInteger('file_size');
            $table->string('mime_type', 100);
            $table->string('uploaded_by_type', 100);
            $table->unsignedBigInteger('uploaded_by_id');
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            $table->foreign('reply_id')->references('id')->on('support_ticket_replies')->onDelete('cascade');
            
            $table->index('ticket_id');
            $table->index('reply_id');
            $table->index(['uploaded_by_type', 'uploaded_by_id'], 'attachments_uploader_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_attachments');
    }
};
