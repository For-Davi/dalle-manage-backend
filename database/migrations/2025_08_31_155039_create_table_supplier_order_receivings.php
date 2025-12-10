<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_order_receivings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_order_item_id');
            $table->integer('quantity_received');
            $table->integer('quantity_stocked')->default(0);
            $table->date('receiving_date');
            $table->text('observation')->nullable();
            $table->unsignedBigInteger('received_by')->nullable();
            $table->timestamps();
            $table->foreign('supplier_order_item_id')->references('id')->on('supplier_order_items');
            $table->foreign('received_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_order_receivings');
    }
};
