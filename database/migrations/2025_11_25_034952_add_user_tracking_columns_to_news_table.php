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
            $table->unsignedBigInteger('created_by')->nullable()->after('auther_id');
            $table->string('created_by_type', 50)->nullable()->default('admin')->after('created_by')->comment('admin or user');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by_type');
            $table->string('updated_by_type', 50)->nullable()->default('admin')->after('updated_by')->comment('admin or user');
            $table->unsignedBigInteger('approve_by')->nullable()->after('updated_by_type');
            $table->string('approve_by_type', 50)->nullable()->default('admin')->after('approve_by')->comment('admin or user');
            
            $table->index(['created_by', 'created_by_type']);
            $table->index(['updated_by', 'updated_by_type']);
            $table->index(['approve_by', 'approve_by_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['approve_by', 'approve_by_type']);
            $table->dropIndex(['updated_by', 'updated_by_type']);
            $table->dropIndex(['created_by', 'created_by_type']);
            
            $table->dropColumn([
                'created_by',
                'created_by_type',
                'updated_by',
                'updated_by_type',
                'approve_by',
                'approve_by_type'
            ]);
        });
    }
};
