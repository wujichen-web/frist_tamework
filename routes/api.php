<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\JyhController;

// 用户注册接口
Route::post('/register', [jyhController::class, 'register']);
// 用户登录接口
Route::post('/login', [jyhController::class, 'login']);

// 需要认证的接口组
Route::middleware('auth:api')->group(function () {
    // 获取当前用户信息
    Route::get('/me', [jyhController::class, 'me']);
    // 修改密码接口
    Route::post('/change-password', [jyhController::class, 'changePassword']);
    // 退出登录接口
    Route::post('/logout', [JyhController::class, 'logout']);

    
    // 教师相关接口
    // 获取可选课程列表
    Route::get('/available-courses', [jyhController::class, 'availableCourses']);
    // 申请课程接口
    Route::post('/apply-course', [jyhController::class, 'applyCourse']);
    // 获取我的课程申请列表
    Route::get('/my-applications', [jyhController::class, 'myApplications']);
    // 删除课程申请
    Route::delete('/delete-application/{id}', [jyhController::class, 'deleteApplication']);
    // 获取已分配给我的课程
    Route::get('/my-assigned-courses', [jyhController::class, 'myAssignedCourses']);
    
    // 管理员相关接口
    Route::middleware('admin')->group(function () {
        // 添加课程接口
        Route::post('/add-course', [jyhController::class, 'addCourse']);
        // 更新课程信息接口
        Route::put('/update-course/{id}', [jyhController::class, 'updateCourse']);
        // 获取所有课程列表
        Route::get('/all-courses', [jyhController::class, 'allCourses']);
        // 获取待审核的课程申请列表
        Route::get('/application-review-list', [jyhController::class, 'applicationReviewList']);
        // 审核课程申请接口
        Route::post('/review-application/{id}', [jyhController::class, 'reviewApplication']);
        // 获取申请某课程的所有教师列表
        Route::get('/teachers-for-course/{courseId}', [jyhController::class, 'teachersForCourse']);
        // 分配课程接口
        Route::post('/assign-course', [jyhController::class, 'assignCourse']);
        // 获取所有教师列表
        Route::get('/all-teachers', [jyhController::class, 'allTeachers']);
        // 重置教师密码接口
        Route::post('/reset-teacher-password/{id}', [jyhController::class, 'resetTeacherPassword']);
        // 添加教师接口
        Route::post('/add-teacher', [jyhController::class, 'addTeacher']);
    });
});






=======
use App\Http\Controllers\YjyController;

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




// 公共接口 (无需认证)
Route::post('/YjyAdminLogin', [YjyController::class, 'YjyAdminLogin']);
Route::post('/YjyTeacherLogin', [YjyController::class, 'YjyTeacherLogin']);      // 用户登录
Route::post('/YjyAdminRegister', [YjyController::class, 'YjyAdminRegister']);
Route::post('/YjyTeacherRegister', [YjyController::class, 'YjyTeacherRegister']);// 用户注册

// 认证用户公共接口 (需 JWT 认证)
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [YjyController::class, 'logoutUser']); // 退出登录
});


//管理员端接口 (需 admin 角色)
Route::middleware(['auth:api', 'admin:admin'])->group(function () {
    Route::get('/allCourses', [YjyController::class, 'getAllCourse']);    // 查看所有课程
    //查询课程（筛选）
    Route::get('admin/courses/research', [YjyController::class, 'research']);
    //创建新课程
    Route::match(['post','put','delete'],'admin/courses/create_course', [YjyController::class, 'createCourse']);
    //修改课程
    Route::put('/admin/courses/{courses}', [YjyController::class, 'updateCourse']);
    //创建新增用户
    Route::post('/admin/users', [YjyController::class, 'createUser']);
    //查看所有教师
    Route::get('/admin/teachers', [YjyController::class, 'getAllTeacher']);
    //重置教师密码
    Route::put('/admin/teachers/{teacher}/reset-password', [YjyController::class, 'resetPassword']);
});
>>>>>>> 04eaf31 (first)
