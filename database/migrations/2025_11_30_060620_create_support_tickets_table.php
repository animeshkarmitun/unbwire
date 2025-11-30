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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 50)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed', 'cancelled'])->default('open');
            $table->string('subject', 255);
            $table->text('description');
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->enum('source', ['web', 'email', 'phone', 'api'])->default('web');
            $table->timestamp('sla_due_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->tinyInteger('satisfaction_rating')->nullable();
            $table->text('satisfaction_feedback')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('support_ticket_categories')->onDelete('restrict');
            
            $table->index('ticket_number');
            $table->index('user_id');
            $table->index('admin_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('priority');
            $table->index('created_at');
            $table->index(['status', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
