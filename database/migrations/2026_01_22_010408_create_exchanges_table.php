<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->string('status');
            $table->decimal('exchange_value', 10, 2)->default(0);
            $table->decimal('difference_value', 10, 2)->default(0);
            $table->string('created_by_name');
            $table->string('created_by_email');
            $table->string('updated_by_name')->nullable();
            $table->string('updated_by_email')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
