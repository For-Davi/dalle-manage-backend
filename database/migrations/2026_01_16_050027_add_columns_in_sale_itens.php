<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_itens', function (Blueprint $table) {
                $table->string('product_color')->nullable();
                $table->string('product_color_name')->nullable();
                $table->string('product_grid_size')->nullable();
                $table->string('product_grid_name')->nullable();
                $table->unsignedBigInteger('product_code');
                $table->string('product_category')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sale_itens', function (Blueprint $table) {
                $table->dropColumn('product_color');
                $table->dropColumn('product_color_name');
                $table->dropColumn('product_grid_size');
                $table->dropColumn('product_grid_name');
                $table->dropColumn('product_code');
                $table->dropColumn('product_category');
        });
    }
};
