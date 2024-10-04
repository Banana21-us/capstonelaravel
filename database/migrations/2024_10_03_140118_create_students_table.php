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
        Schema::create('students', function (Blueprint $table) {
            $table->id('LRN');
            $table->string(column: 'fname');
            $table->string(column: 'lname');
            $table->string(column: 'mname');
            $table->string(column: 'suffix');
            $table->string(column: 'bdate');
            $table->string(column: 'bplace');
            $table->string(column: 'gender');
            $table->string(column: 'religion');
            $table->string(column: 'address');
            $table->string(column: 'contact_no');
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
        Schema::dropIfExists('students');
    }
};
