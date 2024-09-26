<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id(column: 'adminid');
            $table->string(column: 'firstname');
            $table->string(column: 'middlename');
            $table->string(column: 'lastname');
            $table->string(column: 'role');
            $table->string(column: 'address');
            $table->string(column: 'email');
            $table->string(column: 'password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
