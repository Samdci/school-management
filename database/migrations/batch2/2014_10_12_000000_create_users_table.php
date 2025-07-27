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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->nullable();
            $table->string('gender')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('class_name')->nullable();
            $table->string('guardian_fullname')->nullable();
            $table->string('guardian_phonenumber')->nullable();
            $table->string('home_county')->nullable();
            $table->string('kcpe_marks')->nullable();
            $table->string('cert_copy')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
            $table->boolean('must_change_password')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
