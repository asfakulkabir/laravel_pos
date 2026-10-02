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
            $table->foreignId('new_product_size_id')->nullable()->change();
            $table->string('price_note')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_exchanges', function (Blueprint $table) {
            $table->foreignId('new_product_size_id')->nullable(false)->change();
            $table->dropColumn('price_note');
        });
    }
};
