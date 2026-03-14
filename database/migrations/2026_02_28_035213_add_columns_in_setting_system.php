<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->boolean('has_credit_expired_data')->default(1);
            $table->integer('quantity_credit_expire_days')->default(3);
        });
    }

    public function down(): void
    {
        Schema::table('setting_system', function (Blueprint $table) {
            $table->dropColumn([
                'has_credit_expired_data',
                'quantity_credit_expire_days',
            ]);
        });
    }
};
