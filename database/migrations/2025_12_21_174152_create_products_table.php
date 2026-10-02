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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained('sub_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            
            $table->string('main_image')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->decimal('wirehouse_cost', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->decimal('costing_price', 10, 2)->nullable();
            
            $table->integer('stock')->default(0); // Total stock
            $table->string('barcode_image')->nullable();
            $table->string('qr_code')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
