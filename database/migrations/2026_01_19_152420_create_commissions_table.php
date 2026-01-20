<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->string('type');
            $table->string('status');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name');
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->string('seller_name');
            $table->string('seller_email')->nullable();
            $table->decimal('percentage', 10, 2)->nullable();
            $table->decimal('commission_value', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
