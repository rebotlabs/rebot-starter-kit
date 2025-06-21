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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add organization_id column to support organization subscriptions
            $table->foreignId('organization_id')->nullable()->after('user_id');

            // Make user_id nullable to support both user and organization subscriptions
            $table->foreignId('user_id')->nullable()->change();

            // Update indexes
            $table->dropIndex(['user_id', 'stripe_status']);
            $table->index(['user_id', 'stripe_status']);
            $table->index(['organization_id', 'stripe_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Remove indexes
            $table->dropIndex(['organization_id', 'stripe_status']);
            $table->dropIndex(['user_id', 'stripe_status']);

            // Make user_id not nullable again
            $table->foreignId('user_id')->nullable(false)->change();

            // Remove organization_id column
            $table->dropColumn('organization_id');

            // Restore original index
            $table->index(['user_id', 'stripe_status']);
        });
    }
};
