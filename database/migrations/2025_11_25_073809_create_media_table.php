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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            
            // File information
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path'); // Storage path
            $table->string('file_url'); // Public URL
            $table->enum('file_type', ['image', 'video', 'document', 'audio'])->default('image');
            $table->string('mime_type');
            $table->bigInteger('file_size'); // Size in bytes
            
            // Image/Video dimensions (nullable for non-visual files)
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            // Metadata
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->text('description')->nullable();
            
            // Uploader information (polymorphic for future support of users)
            $table->unsignedBigInteger('uploaded_by');
            $table->string('uploaded_by_type')->default('App\Models\Admin');
            
            // Future: folder support
            $table->unsignedBigInteger('folder_id')->nullable();
            
            // Flags
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('file_type');
            $table->index('uploaded_by');
            $table->index('created_at');
            $table->index(['file_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
