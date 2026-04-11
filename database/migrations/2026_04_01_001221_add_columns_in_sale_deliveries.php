<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_deliveries', function (Blueprint $table) {
            $table->string('status')->default('pendent');
            $table->date('scheduled_date')->nullable();
            $table->unsignedBigInteger('delivery_guy_id')->nullable();
            $table->foreign('delivery_guy_id')->on('delivery_guys')->references('id');
            $table->string('delivery_guy_name')->nullable();
            $table->string('delivery_guy_phone')->nullable();
            $table->string('updated_by_name')->nullable();
            $table->string('updated_by_email')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('sale_deliveries', function (Blueprint $table) {
            $table->dropForeign(['delivery_guy_id']);
            $table->dropColumn(['status',
                'scheduled_date',
                'delivery_guy_id',
                'delivery_guy_name',
                'delivery_guy_phone',
                'updated_by_name',
                'updated_by_email',
            ]);
        });
    }
};
