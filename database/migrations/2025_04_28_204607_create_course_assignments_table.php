<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseAssignmentsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('course_assignments')) {
            Schema::create('course_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id'); // 教师 ID
                $table->unsignedBigInteger('course_code');  // 课程 code
                $table->string('course_name');            // 课程名称
                $table->string('course_category');        // 课程类别
                $table->string('course_nature');          // 课程性质
                $table->integer('credits');               // 学分
                $table->string('status')->default('已通过审核'); // 选课状态，默认已通过审核
                $table->timestamps();

                // 定义外键约束
                $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
                $table->foreign('course_code')->references('course_code')->on('course_info')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('course_assignments');
    }
}
