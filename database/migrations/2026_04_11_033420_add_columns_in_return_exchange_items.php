<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_exchange_items', function (Blueprint $table) {
            $table->string('product_grid_size')->nullable();
            $table->string('product_grid_name')->nullable();
            $table->boolean('delivered');
            $table->integer('quantity_delivered');
        });
    }

    public function down(): void
    {
        Schema::table('return_exchange_items', function (Blueprint $table) {
            $table->dropColumn('product_grid_size');
            $table->dropColumn('product_grid_name');
            $table->dropColumn('delivered');
            $table->dropColumn('quantity_delivered');
        });
    }
};
