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
        Schema::create('parent_guardians', function (Blueprint $table) {
            $table->id('guardian_id')->startingValue(10000);;
            $table->unsignedBigInteger('LRN'); 
            $table->string(column: 'fname');
            $table->string(column: 'lname');
            $table->string(column: 'mname')->nullable();
            $table->string(column: 'address');
            $table->string(column: 'relationship');
            $table->string(column: 'parent_pic')->nullable();
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
        Schema::dropIfExists('parent_guardians');
    }
};
