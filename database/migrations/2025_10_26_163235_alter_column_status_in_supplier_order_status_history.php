<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_order_status_history', function (Blueprint $table) {
            $table->string('status')->default('waiting')->change();
        });
    }

    public function down(): void
    {
        Schema::table('supplier_order_status_history', function (Blueprint $table) {
            $table->string('status')->default(null)->change();
        });
    }
};
