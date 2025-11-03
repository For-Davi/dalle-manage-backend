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
        Schema::create('sale_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->decimal('freight_value', 10, 2);
            $table->string('cep')->nullable();
            $table->string('state');
            $table->string('city');
            $table->string('neighborhood');
            $table->string('address')->nullable();
            $table->string('number_address');
            $table->string('complement')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->longText('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_deliveries');
    }
};
