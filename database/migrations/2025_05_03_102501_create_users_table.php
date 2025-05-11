<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();//自增主键，不需要在请求里主动赋值，他会自己增加
            $table->string('username')->unique();//string类型默认长度255个字符
            //对于超长文本，可以使用 text() 或 longText()
            $table->string('password');//字符串列
            $table->string('teacher_name');
            $table->string('department');
            $table->enum('role',['teacher','admin'])->default('teacher');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();//时间戳，比如你的创建时间，申请时间
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
}
