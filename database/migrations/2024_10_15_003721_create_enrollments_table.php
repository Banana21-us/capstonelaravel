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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id('enrol_id');
            $table->unsignedBigInteger('LRN');
            $table->date('regapproval_date');
            $table->date( 'date_register');
            $table->string('payment_approval');
            $table->string('grade_level');
            $table->string('guardian_name');
            $table->string('last_attended');
            $table->string('public_private');
            $table->string('strand');
            $table->string('school_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
