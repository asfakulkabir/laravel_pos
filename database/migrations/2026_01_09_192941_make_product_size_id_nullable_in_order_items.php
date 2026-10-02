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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_size_id']);
            $table->unsignedBigInteger('product_size_id')->nullable()->change();
            $table->foreign('product_size_id')
                ->references('id')
                ->on('product_sizes')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_size_id']);
            $table->unsignedBigInteger('product_size_id')->nullable(false)->change();
            $table->foreign('product_size_id')
                ->references('id')
                ->on('product_sizes');
        });
    }
};
