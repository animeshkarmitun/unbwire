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
        if (Schema::hasTable('support_ticket_sla_logs')) {
            Schema::dropIfExists('support_ticket_sla_logs');
        }
        
        Schema::create('support_ticket_sla_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->enum('sla_type', ['response', 'resolution']);
            $table->integer('target_hours');
            $table->decimal('actual_hours', 10, 2)->nullable();
            $table->enum('status', ['met', 'breached', 'pending'])->default('pending');
            $table->timestamp('breached_at')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            
            $table->index('ticket_id');
            $table->index('status');
            $table->index('sla_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_ticket_sla_logs');
    }
};
