<?php

namespace App\Http\Controllers;

use App\Models\CourseInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseApplications;
use App\Models\CourseAssignment;
use Illuminate\Support\Facades\Log;

class LrzTeachersController extends Controller
{

    // 教师登录接口
    public function LrzLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account' =>'required|string',
            'password' =>'required|string',
        ]);

        if ($validator->fails()) {
            Log::warning('教师登录时输入验证失败：'. $validator->errors()->first());
            return json_fail(
                '输入验证失败',null, 422);
        }

        $credentials = $request->only('account', 'password');

        $user = User::where('account', $credentials['account'])->where('role', 'teacher')->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            try {
                $token = JWTAuth::fromUser($user);
                Log::info('教师登录成功，账号：'. $credentials['account']);
                return json_success('登陆成功', ['token' => $token], 200);
            } catch (\Exception $e) {
                Log::error('教师登录生成令牌时出现错误：'. $e->getMessage());
                return json_fail('生成令牌时出现错误',null, 500);
            }
        }

        Log::warning('教师登录失败，账号：'. $credentials['account']. '，原因：账号或密码错误');
        return json_fail('账号或密码错误',null, 401);
    }

    // 我的课程接口，可筛选学期
    public function LrzMyCourses(Request $request): JsonResponse
    {
        $semester = $request->input('semester');
        // 获取前端传递的每页行数参数，默认为 10
        $perPage = $request->input('per_page', 10);

        $Id = Auth::id();
        $query = CourseAssignment::where('id', $Id);

        if ($semester) {
            $query->where('semester', $semester);
        }
        // 根据每页行数进行分页
        $courses = $query->orderBy('course_name')->paginate($perPage);

        return json_success('我的课程',  ['data' => $courses], 200);
    }

    public function LrzSelectCourses(Request $request): JsonResponse
    {
        // 获取前端传递的每页行数参数，默认为 10
        $perPage = $request->input('per_page', 10);
        // 获取前端传递的页码参数，默认为第 1 页
        $page = $request->input('page', 1);
        // 获取前端传递的学期参数
        $semester = $request->input('semester');
        // 获取前端传递的关键字参数
        $keyword = $request->input('keyword');

        $teacher = Auth::user();
        if (!$teacher) {
            Log::error('selectCourses 方法中未找到教师信息');
            return json_fail('未找到教师信息',null, 404);
        }
        $department = $teacher->department;

        if ($request->isMethod('get')) {
            // GET 请求，仅获取可选择课程列表
            $query = CourseInfo::where('department', $department);

            if ($semester) {
                $query->where('semester', $semester);
            }

            if ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('course_name', 'like', "%$keyword%")
                        ->orWhere('course_code', 'like', "%$keyword%");
                });
            }

            $courses = $query->paginate($perPage, ['*'], 'page', $page);

            return json_success('可选择的课程', ['data' => $courses], 200);
        } elseif ($request->isMethod('post')) {
            // POST 请求，执行选择课程操作
            try {
                $courseCode = $request->input('course_code');
                if (!$courseCode) {
                    Log::warning('选择课程时未提供课程代码');
                    return json_fail('未提供课程代码', null,400);
                }
                $result = CourseApplications::selectCourse($courseCode);
                if ($result['success']) {
                    Log::info('课程选择成功，课程代码: '. $courseCode);
                    return response()->json(['message' => $result['message']], 200);
                } else {
                    Log::warning('课程选择失败，原因: '. $result['message']);
                    return response()->json(['message' => $result['message']], 400);
                }
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                Log::error('选择课程时未找到课程记录: '. $e->getMessage());
                return json_fail('未找到该课程记录',null, 404);
            } catch (\Exception $e) {
                Log::error('选择课程时出现异常: '. $e->getMessage());
                return json_fail('选择课程时出现异常，请稍后重试', null,500);
            }
        }

        return json_fail('不支持的请求方法', null,405);
    }

    // 已选课程接口
    public function LrzSelectedCourses(Request $request): JsonResponse
    {
        // 获取前端传递的每页行数参数，默认为 10
        $perPage = $request->input('per_page', 10);
        // 获取当前页码
        $page = $request->input('page', 1);
        // 获取前端传递的学期参数
        $semester = $request->input('semester');

        $Id = Auth::id();

        $query = CourseApplications::where('id', $Id);

        if ($semester) {
            $query->where('semester', $semester);
        }

        // 查询该教师所选课程及审核状态，按学期分组
        $selectedCourses = $query->select('course_name', 'course_nature', 'credits','status','semester')
            ->orderBy('semester')
            ->paginate($perPage, ['*'], 'page', $page);

        return json_success('已选课程', ['data' => $selectedCourses], 200);
    }

    public function LrzDeleteSelectedCourses(Request $request): JsonResponse
    {
        try {
            // 从请求体中获取课程申请记录的 id
            $recordId = $request->input('id');

            // 检查 id 是否存在
            if (!$recordId) {
                Log::warning('deleteSelectedCourses 方法中未提供课程申请记录的 id');
                return json_fail('未提供课程申请记录的 id', null,400);
            }

            // 根据 id 查找课程申请记录
            $courseApplication = CourseApplications::find($recordId);

            // 检查课程记录是否存在
            if (!$courseApplication) {
                Log::warning('deleteSelectedCourses 方法中课程记录不存在，请求的课程 ID 可能无效');
                return json_fail('课程记录不存在', null,404);
            }

            // 检查必要字段是否存在
            if (empty($courseApplication->course_code)) {
                Log::error('deleteSelectedCourses 方法中课程记录的 course_code 字段为空，课程 ID：' . $courseApplication->id);
                return json_fail('课程记录数据不完整，无法处理删除请求', null,500);
            }

            // 将课程状态标记为待审批删除
            $courseApplication->status = '待审批删除';
            $courseApplication->save();

            Log::info('课程删除申请已提交，课程 ID：' . $courseApplication->id . '，课程代码：' . $courseApplication->course_code);
            return json_success('课程删除申请已提交，正在审批中', null,200);
        } catch (\Exception $e) {
            Log::error('deleteSelectedCourses 方法中发生异常：' . $e->getMessage());
            return json_fail( '处理删除请求时发生错误，请稍后重试', null,500);
        }
    }


    // 审核通过后将数据从 course_applications 表传到 assignment 表
//    public function approveCourseApplication(CourseApplications $courseApplication)
//    {
//        if (!$courseApplication) {
//            Log::warning('approveCourseApplication 方法中课程申请记录不存在');
//            return response()->json(['message' => '课程申请记录不存在'], 404);
//        }
//
//        // 检查该申请是否已经审核通过
//        if ($courseApplication->status === '已通过审核') {
//            Log::warning('approveCourseApplication 方法中课程申请已审核通过');
//            return response()->json(['message' => '该课程申请已审核通过'], 400);
//        }
    public function LrzApproveCourseDirectly(Request $request): JsonResponse
    {
        $courseApplicationId = $request->input('course_application_id');

        if (!$courseApplicationId) {
            Log::warning('approveCourseDirectly 方法中未提供课程申请记录 ID');
            return json_fail('未提供课程申请记录 ID',null,400 );
        }

        $courseApplication = CourseApplications::find($courseApplicationId);

        if (!$courseApplication) {
            Log::warning('approveCourseDirectly 方法中课程申请记录不存在');
            return json_fail('课程申请记录不存在', null,404);
        }

        // 检查该申请是否已经审核通过
        if ($courseApplication->status === '已通过审核') {
            Log::warning('approveCourseDirectly 方法中课程申请已审核通过');
            return json_success('该课程申请已审核通过', null,200);
        }

        // 将数据插入到 CourseAssignment 表
        CourseAssignment::create([
            'id' => $courseApplication->id,
            'semester' => $courseApplication->semester,
            'course_code' => $courseApplication->course_code,
            'course_name' => $courseApplication->course_name,
            'course_category' => $courseApplication->course_category,
            'course_nature' => $courseApplication->course_nature,
            'credits' => $courseApplication->credits,
            'status' => '已通过审核',
            'class_name' => $courseApplication->class_name?? null,
            'leader' => $courseApplication->leader?? null,
            'major' => $courseApplication->major?? null,
            'total_hours' => $courseApplication->total_hours?? null,
            'grade' => $courseApplication->grade?? null,
            'student_num' => $courseApplication->student_num?? null,
            'department' => $courseApplication->department?? null
        ]);

        // 更新 course_applications 表中的记录状态为已通过审核
        $courseApplication->status = '已通过审核';
        $courseApplication->save();

        Log::info('课程申请直接审核通过，课程申请记录 ID：'. $courseApplicationId. '，数据已迁移到 assignment 表');
        return json_success( '课程申请直接审核通过，数据已迁移到 assignment 表', null,200);
    }

    public function LrzLogout(): JsonResponse
    {
        auth('uses')->logout();
        return json_success("用户退出登录成功", null, 200);
    }


}
