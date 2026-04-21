<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_itens', function (Blueprint $table) {
            $table->decimal('product_discount', 10, 2)->default(0);
            $table->decimal('product_discount_value', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('sale_itens', function (Blueprint $table) {
            $table->dropColumn(['product_discount', 'product_discount_value']);
        });
    }
};
