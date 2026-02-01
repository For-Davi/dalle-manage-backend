<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('exchange_payments_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exchange_id');
            $table->foreign('exchange_id')->references('id')->on('exchanges');
            $table->unsignedBigInteger('receipt_id')->nullable();
            $table->foreign('receipt_id')->references('id')->on('receipts');
            $table->string('receipt_name');
            $table->unsignedBigInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('types_receipt');
            $table->decimal('value', 10, 2);
            $table->decimal('change', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_payments_methods');
    }
};
