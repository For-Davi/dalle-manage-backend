<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->decimal('exchange_value', 10, 2);
            $table->decimal('difference_value', 10, 2);
            $table->decimal('current_value', 10, 2);
            $table->decimal('fees', 10, 2);
            $table->decimal('change', 10, 2);
            $table->decimal('freight_fees', 10, 2);
            $table->decimal('freight_change', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->dropColumn([
            'exchange_value',
            'difference_value',
            'current_value',
            'fees',
            'change',
            'freight_fees',
            'freight_change'
        ]);
        });
    }
};
