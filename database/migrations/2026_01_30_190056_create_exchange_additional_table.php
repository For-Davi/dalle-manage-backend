<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('exchange_additional', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exchange_id');
            $table->foreign('exchange_id')->references('id')->on('exchanges');
            $table->decimal('change', 10, 2);
            $table->decimal('fees', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('exchange_additional');
    }
};
