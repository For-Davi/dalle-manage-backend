<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->unsignedBigInteger('enterprise_id');
            $table->foreign('enterprise_id')->references('id')->on('enterprises');
            $table->string('status'); // canceled , completely_finished, partial_finished,  waiting, conference
            $table->date('date_delivery_expected')->nullable();
            $table->date('date_issue')->nullable();
            $table->string('order_number')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->longText('cancellation_reason')->nullable();
            $table->date('date_received')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_orders');
    }
};
