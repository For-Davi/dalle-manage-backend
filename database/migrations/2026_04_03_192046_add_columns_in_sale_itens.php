<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('sale_itens', function (Blueprint $table) {
            $table->boolean('delivered');
            $table->integer('quantity_delivered');
        });
    }

    public function down(): void
    {
        Schema::table('sale_itens', function (Blueprint $table) {
            $table->dropColumn('delivered');
            $table->dropColumn('quantity_delivered');
        });
    }
};
