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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('sync_status')->default('pending')->index();
            $table->uuid('idempotency_key')->nullable()->unique();
            $table->integer('sync_attempts')->default(0);
            $table->text('last_sync_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['sync_status', 'idempotency_key', 'sync_attempts', 'last_sync_error']);
        });
    }
};
