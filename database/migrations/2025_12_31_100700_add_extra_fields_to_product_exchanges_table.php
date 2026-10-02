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
        Schema::table('product_exchanges', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('difference_amount');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('reason');
            $table->enum('discount_type', ['flat', 'percent'])->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_exchanges', function (Blueprint $table) {
            $table->dropColumn(['reason', 'discount_amount', 'discount_type']);
        });
    }
};
