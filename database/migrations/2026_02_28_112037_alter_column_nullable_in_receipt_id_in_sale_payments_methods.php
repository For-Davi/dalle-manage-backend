<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_payments_methods', function (Blueprint $table) {
            $table->unsignedBigInteger('receipt_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        $defaultReceiptId = DB::table('receipts')->min('id');

        DB::table('sale_payments_methods')
            ->whereNull('receipt_id')
            ->update(['receipt_id' => $defaultReceiptId]);

        Schema::table('sale_payments_methods', function (Blueprint $table) {
            $table->unsignedBigInteger('receipt_id')->nullable(false)->default(0)->change();
        });
    }
};
