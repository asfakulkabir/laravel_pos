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
        // Stock Intake Sessions
        Schema::create('stock_intake_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Stock Intake Items
        Schema::create('stock_intake_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('stock_intake_sessions')->cascadeOnDelete();
            $table->string('sku');
            $table->string('product_name');
            $table->string('color_name');
            $table->string('size_name');
            $table->integer('quantity');
            $table->integer('previous_stock')->default(0);
            $table->integer('new_stock')->default(0);
            $table->timestamps();
        });

        // Outlet Transfer Sessions
        Schema::create('outlet_transfer_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Outlet Transfer Items
        Schema::create('outlet_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('outlet_transfer_sessions')->cascadeOnDelete();
            $table->string('sku');
            $table->string('product_name');
            $table->string('color_name');
            $table->string('size_name');
            $table->integer('quantity');
            $table->integer('previous_stock')->default(0);
            $table->integer('new_stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlet_transfer_items');
        Schema::dropIfExists('outlet_transfer_sessions');
        Schema::dropIfExists('stock_intake_items');
        Schema::dropIfExists('stock_intake_sessions');
    }
};
