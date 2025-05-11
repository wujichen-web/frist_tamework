<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{

    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_name');
            $table->string('course_code');
            $table->enum('course_category', ['major', 'public'])->default('major');
            $table->enum('course_type', ['required', 'elective'])->default('required');
            $table->integer('credit');
            $table->integer('class_hours');
            $table->string('grade'); // 如2021级
            $table->string('major');
            $table->string('department');
            $table->string('class_name'); // 如财务管理24201
            $table->integer('class_size');
            $table->string('semester'); // 如2024-2025学年第二学期
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
