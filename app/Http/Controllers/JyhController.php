<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseApplication;
use App\Models\CourseAssignment;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class JyhController extends Controller
{

    //注册接口
    public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|unique:users',
        'password' => 'required|string|min:6',
        'teacher_name' => 'required|string',
        'department' => 'required|string',
        'role' => 'required|in:teacher,admin'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    $user = User::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'teacher_name' => $request->teacher_name,
        'department' => $request->department,
        'role' => $request->role
    ]);

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user
    ], 201);
}



//登录接口
    public function login(Request $request)
{
    $credentials = $request->only('username', 'password');

    if (! $token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $this->respondWithToken($token);
}

protected function respondWithToken($token)
{
    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => JWTAuth::factory()->getTTL() * 60
    ]);
}


//退出登录
public function logout(Request $request)
{
    JWTAuth::invalidate(JWTAuth::getToken());

    return response()->json(['message' => 'Successfully logged out']);
}



//获取当前用户信息
public function me()
{
    return response()->json(auth()->user());
}

//修改密码
public function changePassword(Request $request)
{
    $request->validate([
        'new_password' => 'required|string|min:6'
    ]);

    $user = auth()->user();
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['message' => 'Password changed successfully']);
}


//
//教师相关接口
//

//获取可选课程接口
public function availableCourses(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    // 获取所有课程（按课程名称分组）
    $courses = Course::select('course_name', 'course_category', 'course_type', 'credit')
        ->selectRaw('COUNT(DISTINCT class_name) as class_count')
        ->groupBy('course_name', 'course_category', 'course_type', 'credit')
        ->paginate($perPage, ['*'], 'page', $page);

    // 检查用户是否已申请每门课程
    $user = auth()->user();
    $appliedCourseIds = $user->applications()->pluck('course_id')->toArray();

    $courses->getCollection()->transform(function ($course) use ($appliedCourseIds) {
        $course->is_applied = in_array($course->id, $appliedCourseIds);
        return $course;
    });

    return response()->json($courses);
}


//申请课程
public function applyCourse(Request $request)
{
    $request->validate([
        'course_name' => 'required|string'
    ]);

    $user = auth()->user();

    // 查找课程（按名称）
    $courses = Course::where('course_name', $request->course_name)->get();

    if ($courses->isEmpty()) {
        return response()->json(['message' => 'Course not found'], 404);
    }

    // 为每个课程创建申请
    foreach ($courses as $course) {
        // 检查是否已申请
        $existingApplication = CourseApplication::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$existingApplication) {
            CourseApplication::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'pending'
            ]);
        }
    }

    return response()->json(['message' => 'Course application submitted']);
}

//获取已申请课程
public function myApplications(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    $user = auth()->user();

    $applications = CourseApplication::with('course')
        ->where('user_id', $user->id)
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json($applications);
}


//删除申请（仅限未审核或未通过的）
public function deleteApplication($id)
{
    $user = auth()->user();

    $application = CourseApplication::where('user_id', $user->id)
        ->where('id', $id)
        ->whereIn('status', ['pending', 'rejected'])
        ->first();

    if (!$application) {
        return response()->json(['message' => 'Application not found or cannot be deleted'], 404);
    }

    $application->delete();

    return response()->json(['message' => 'Application deleted']);
}

//获取我的课程（已分配）
public function myAssignedCourses()
{
    $user = auth()->user();

    $assignments = CourseAssignment::with('course')
        ->where('user_id', $user->id)
        ->get();

    return response()->json($assignments);
}

//
//管理员相关接口
//

//添加课程
public function addCourse(Request $request)
{
    $request->validate([
        'course_name' => 'required|string',
        'course_code' => 'required|string',
        'course_category' => 'required|in:major,public',
        'course_type' => 'required|in:required,elective',
        'credit' => 'required|integer',
        'class_hours' => 'required|integer',
        'grade' => 'required|string',
        'major' => 'required|string',
        'department' => 'required|string',
        'class_name' => 'required|string',
        'class_size' => 'required|integer',
        'semester' => 'required|string'
    ]);

    $course = Course::create($request->all());

    return response()->json(['message' => 'Course added', 'course' => $course], 201);
}

//修改课程
public function updateCourse(Request $request, $id)
{
    $request->validate([
        'course_name' => 'string',
        'course_code' => 'string',
        'course_category' => 'in:major,public',
        'course_type' => 'in:required,elective',
        'credit' => 'integer',
        'class_hours' => 'integer',
        'grade' => 'string',
        'major' => 'string',
        'department' => 'string',
        'class_name' => 'string',
        'class_size' => 'integer',
        'semester' => 'string'
    ]);

    $course = Course::find($id);

    if (!$course) {
        return response()->json(['message' => 'Course not found'], 404);
    }

    $course->update($request->all());

    return response()->json(['message' => 'Course updated', 'course' => $course]);
}

//获取所有课程
public function allCourses(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    $courses = Course::with(['assignments.user'])
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json($courses);
}

//获取申请审核列表
public function applicationReviewList(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    $applications = CourseApplication::with(['user', 'course'])
        ->where('status', 'pending')
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json($applications);
}

//审核申请
public function reviewApplication(Request $request, $id)
{
    $request->validate([
        'action' => 'required|in:approve,reject'
    ]);

    $application = CourseApplication::find($id);

    if (!$application) {
        return response()->json(['message' => 'Application not found'], 404);
    }

    $application->status = $request->action == 'approve' ? 'approved' : 'rejected';
    $application->save();

    return response()->json(['message' => 'Application reviewed']);
}

//获取选择某课程的老师
public function teachersForCourse($courseId)
{
    $course = Course::find($courseId);

    if (!$course) {
        return response()->json(['message' => 'Course not found'], 404);
    }

    $teachers = User::whereHas('applications', function($query) use ($courseId) {
            $query->where('course_id', $courseId)
                ->where('status', 'approved');
        })
        ->get();

    return response()->json($teachers);
}



//分配课程给老师
public function assignCourse(Request $request)
{
    $request->validate([
        'course_id' => 'required|exists:courses,id',
        'teacher_id' => 'required|exists:users,id'
    ]);

    // 检查老师是否已申请并通过该课程
    $application = CourseApplication::where('user_id', $request->teacher_id)
        ->where('course_id', $request->course_id)
        ->where('status', 'approved')
        ->first();

    if (!$application) {
        return response()->json(['message' => 'Teacher has not applied or been approved for this course'], 400);
    }

    // 检查是否已分配
    $existingAssignment = CourseAssignment::where('course_id', $request->course_id)
        ->where('user_id', $request->teacher_id)
        ->first();

    if ($existingAssignment) {
        return response()->json(['message' => 'Course already assigned to this teacher'], 400);
    }

    CourseAssignment::create([
        'user_id' => $request->teacher_id,
        'course_id' => $request->course_id
    ]);

    return response()->json(['message' => 'Course assigned to teacher']);
}

//获取所有老师
public function allTeachers(Request $request)
{
    $perPage = $request->input('per_page', 10);
    $page = $request->input('page', 1);

    $teachers = User::where('role', 'teacher')
        ->paginate($perPage, ['*'], 'page', $page);

    return response()->json($teachers);
}

//重置老师密码
public function resetTeacherPassword(Request $request, $id)
{
    $request->validate([
        'new_password' => 'required|string|min:6'
    ]);

    $teacher = User::where('role', 'teacher')->find($id);

    if (!$teacher) {
        return response()->json(['message' => 'Teacher not found'], 404);
    }

    $teacher->password = Hash::make($request->new_password);
    $teacher->save();

    return response()->json(['message' => 'Teacher password reset']);
}

//添加老师
public function addTeacher(Request $request)
{
    $request->validate([
        'username' => 'required|string|unique:users',
        'password' => 'required|string|min:6',
        'teacher_name' => 'required|string',
        'department' => 'required|string',
        'role' => 'required|in:teacher,admin'
    ]);

    $teacher = User::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'teacher_name' => $request->teacher_name,
        'department' => $request->department,
        'role' => $request->role
    ]);

    return response()->json(['message' => 'Teacher added', 'teacher' => $teacher], 201);
}


}
