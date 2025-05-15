<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyCoursesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('my_courses')) {
            Schema::create('my_courses', function (Blueprint $table) {
                $table->id();
                $table->string('course_name', 255); // 课程名
                $table->string('course_code', 255); // 课程代码
                $table->string('course_category', 50); // 类别
                $table->string('course_nature', 20); // 性质
                $table->integer('credits'); // 学分
                $table->integer('total_hours'); // 课时
                $table->string('class_number', 50); // 班级
                $table->integer('class_size'); // 人数
                $table->string('semester', 255); // 学期
                $table->timestamps();
            });
        }
    }
    public function down()
    {
        Schema::dropIfExists('my_courses');
    }
}
