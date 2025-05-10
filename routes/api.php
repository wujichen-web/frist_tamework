<?php

use App\Http\Controllers\LrzAdminsController;
use App\Http\Controllers\LrzTeachersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// 管理员端路由
Route::prefix('admin')->group(function () {
    Route::post('login', [LrzAdminsController::class, 'LrzAdminLogin']);
    Route::post('create-user', [LrzAdminsController::class, 'LrzCreateUser']);
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('logout', [LrzAdminsController::class, 'LrzLogout']);
    });

});

// 教师端路由
Route::prefix('teacher')->group(function () {
    Route::post('login', [LrzTeachersController::class, 'LrzLogin']);
    Route::post('approve-course-directly', [LrzTeachersController::class, 'LrzApproveCourseDirectly']);
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::get('my-courses', [LrzTeachersController::class,'LrzMyCourses']);
        Route::get('selectable-courses', [LrzTeachersController::class,'LrzSelectCourses']);//可选课程
        Route::post('select-courses', [LrzTeachersController::class,'LrzSelectCourses']);//选择课程
        Route::get('selected-courses', [LrzTeachersController::class,'LrzSelectedCourses']);//已选课程
        Route::delete('selected-courses', [LrzTeachersController::class,'LrzDeleteSelectedCourses']);//删除已选课程
        Route::post('logout', [LrzTeachersController::class, 'LrzLogout']);
    });
});
