<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_movements', function (Blueprint $table) {
            $table->string('status');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->unsignedBigInteger('return_id')->nullable();
            $table->foreign('return_id')->references('id')->on('returns');
            $table->string('created_by_name');
            $table->string('created_by_email');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            $table->string('updated_by_name')->nullable();
            $table->string('updated_by_email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('product_movements', function (Blueprint $table) {
            $table->dropForeign(['return_id']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['sale_id']);
            $table->dropColumn([
                'status',
                'sale_id',
                'return_id',
                'created_by_name',
                'created_by_email',
                'updated_by',
                'updated_by_name',
                'updated_by_email',
            ]);
        });
    }
};
