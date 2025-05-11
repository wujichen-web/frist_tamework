<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Users extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('account')->unique();
                $table->string('password');
                $table->string('teacher_name')->nullable();
                $table->string('department');
                $table->enum('role', ['teacher', 'admin']);
                $table->timestamps();
            });
        }
    }
        public function down()
    {
        Schema::dropIfExists('users');
    }
}
