<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->renameColumn('total', 'starting_total');
            $table->decimal('current_total', 10, 2)->after('starting_total');
            $table->string('status')->after('current_total');
        });
    }

    public function down(): void
    {

        Schema::table('sales', function (Blueprint $table) {
            $table->renameColumn('starting_total', 'total');
            $table->dropColumn('current_total');
            $table->dropColumn('status');
        });

    }
};
