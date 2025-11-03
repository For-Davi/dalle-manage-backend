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
        Schema::create('sale_payments_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->unsignedBigInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('types_receipt');
            $table->unsignedBigInteger('receipt_id')->nullable();
            $table->foreign('receipt_id')->references('id')->on('receipts');
            $table->integer('installments')->nullable();
            $table->decimal('value', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments_methods');
    }
};
