<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_cancellations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->on('sales')->references('id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->on('users')->references('id');
            $table->string('created_by_name');
            $table->string('created_by_email');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_cancellations');
    }
};
