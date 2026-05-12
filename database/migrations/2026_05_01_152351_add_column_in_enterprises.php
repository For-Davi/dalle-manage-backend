<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->boolean('first_payment_subscription')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            $table->dropColumn('first_payment_subscription');
        });
    }
};
