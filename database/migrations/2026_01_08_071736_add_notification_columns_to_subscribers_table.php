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
        Schema::table('subscribers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            $table->boolean('email_notifications_enabled')->default(true)->after('email');
            $table->string('language_preference', 2)->nullable()->after('email_notifications_enabled');
            $table->timestamp('last_notified_at')->nullable()->after('language_preference');
            $table->string('unsubscribe_token', 64)->nullable()->unique()->after('last_notified_at');
            
            $table->index('user_id');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['email']);
            $table->dropColumn([
                'user_id',
                'email_notifications_enabled',
                'language_preference',
                'last_notified_at',
                'unsubscribe_token'
            ]);
        });
    }
};
