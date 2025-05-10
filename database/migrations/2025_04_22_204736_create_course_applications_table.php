<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseApplicationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('course_applications')) {
            Schema::create('course_applications', function (Blueprint $table) {
                $table->id();
                $table->string('course_name', 255); // 课程名称，设置合适长度
                $table->string('course_category', 50); // 课程类别
                $table->string('course_nature', 20); // 课程性质
                $table->integer('credits'); // 学分
                $table->enum('status', ['选择', '已选择'])->default('选择'); // 状态
                $table->timestamps();
            });
        }
    }
    public function down()
    {
        Schema::dropIfExists('course_applications');
    }
}
