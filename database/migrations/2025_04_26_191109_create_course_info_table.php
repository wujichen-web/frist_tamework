<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseInfoTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('course_info')) {
            Schema::create('course_info', function (Blueprint $table) {
                $table->string('semester', 255)->nullable(false);
                $table->string('course_name', 255)->nullable(false);
                $table->string('course_code', 255)->nullable(false);
                $table->string('major', 255)->nullable(false);
                $table->string('department', 255)->nullable(false);
                $table->string('course_category', 255)->nullable(false);
                $table->string('course_nature', 255)->nullable(false);
                $table->integer('credits')->nullable(false);
                $table->integer('total_hours')->nullable(false);
                $table->string('grade', 255)->nullable(false);
                $table->string('class_number', 255)->nullable(false);
                $table->integer('class_size')->nullable(false);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('course_info');
    }
}
