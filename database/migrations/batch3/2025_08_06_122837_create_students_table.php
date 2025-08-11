<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->onDelete('set null');
            $table->string('guardian_fullname')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('guardian_phonenumber')->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('home_county')->nullable();
            $table->string('kcpe_marks')->nullable();
            $table->string('cert_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
