<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_order_id');
            $table->foreign('supplier_order_id')->references('id')->on('supplier_orders');
            $table->unsignedBigInteger('product_variant_id');
            $table->foreign('product_variant_id')->references('id')->on('product_variants');
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->bigInteger('quantity_requested')->default(0);
            $table->bigInteger('quantity_received')->default(0);
            $table->date('date_received')->nullable();
            $table->boolean('finished')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_order_items');
    }
};
