<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_order_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_order_id');
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();
            $table->foreign('supplier_order_id')->references('id')->on('supplier_orders');
            $table->foreign('changed_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_order_status_history');
    }
};
