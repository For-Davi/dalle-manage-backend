<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_exchange_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->foreign('return_id')->references('id')->on('returns');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('product_price', 10, 2);
            $table->string('product_color')->nullable();
            $table->string('product_color_name')->nullable();
            $table->integer('quantity');   
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_exchange_items');
    }
};
