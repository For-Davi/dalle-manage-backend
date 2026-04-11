<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id')->nullable();
            $table->foreign('permission_id')->on('permissions')->references('id');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->on('roles')->references('id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
