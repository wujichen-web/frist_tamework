<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{

    public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('username')->unique();
        $table->string('password');
        $table->string('teacher_name');
        $table->string('department');
        $table->enum('role', ['teacher', 'admin']);
        $table->rememberToken();
        $table->timestamps();
    });
}

 
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
