<?php
namespace App\Http\Controllers;

use App\Models\CourseAssignment;
use App\Http\Middleware\CheckRole;
use Exception;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class YjyController extends Controller
{
    //通用接口

    //管理员密码处理
    protected function YjyAdminHandle($request)
    {
        //从请求中提取账号
        $registeredInfo['username'] = $request['username'];
        //用bcrypt哈希加密密码
        $registeredInfo['password'] = $request['password'];
        $registeredInfo['teacher_name'] = $request['teacher_name'];
        $registeredInfo['department'] = $request['department'];
        $registeredInfo['role'] = 'admin';
        //返回处理后的数据
        return $registeredInfo;
    }

    //管理员注册
    public function YjyAdminRegister(Request $request)
    {
        //请求处理数据
        $registeredInfo = self::YjyAdminHandle($request);
        $username = $registeredInfo['username'];
        //检查账号是否存在
        $count = User::Yjychecknumber($username);
        if ($count) {
            //检查是否存在错误
            return json_fail('注册失败！检测是否存在的时候出错啦', $count, 100);
        }
        if ($count == 0) {
            //如果账号不存在
            $Admin_id = User::YjycreateAdmin($registeredInfo);//创建管理员
            if (is_error($Admin_id) == true) {
                //检查创建是否出错
                return json_fail('注册失败！添加数据的时候有问题', $Admin_id, 100);
            }
            return json_success('注册成功！', $Admin_id, 200);
        }
        return json_fail('注册失败！该用户信息已经被注册过了', null, 101);
    }

    //管理员登录
    public function YjyAdminLogin(Request $request): JsonResponse
    {
        //获取账号
        $credentials['username'] = $request['username'];
        //获取明文密码
        $credentials['password'] = $request['password'];
        //通过验证并生成令牌
        $token = auth('api')->attempt($credentials);
        //运用三目运算符
        return $token ?
            json_success('登录成功!', $token, 200) :
            json_fail('登录失败!账号或密码错误', null, 100);
    }

    //教师密码处理
    protected function YjyTeacherHandle($request)
    {
        //从请求中提取账号
        $registeredInfo['username'] = $request['username'];
        //用bcrypt哈希加密密码
        $registeredInfo['password'] = $request['password'];
        $registeredInfo['teacher_name'] = $request['teacher_name'];
        $registeredInfo['department'] = $request['department'];
        //教师需要管理员审核
        $registeredInfo['role'] = 'teacher';
        //返回处理后的数据
        return $registeredInfo;
    }

    //教师注册
    public function YjyTeacherRegister(Request $request)
    {
        //请求处理数据
        $registeredInfo = self::YjyTeacherHandle($request);
        $username = $registeredInfo['username'];
        //检查账号是否存在
        $count = User::Yjychecknumber($username);
        if (is_error($count) == true) {
            //检查是否存在错误
            return json_fail('注册失败！检测是否存在的时候出错啦', $count, 100);
        }
        if ($count == 0) {
            //如果账号不存在
            $Teacher_id = User::YjycreateTeacher($registeredInfo);//创建教师
            if (is_error($Teacher_id) == true) {
                //检查创建是否出错
                return json_fail('注册失败！添加数据的时候有问题', $Teacher_id, 100);
            }
            return json_success('注册成功！', $Teacher_id, 200);
        }
        return json_fail('注册失败！该用户信息已经被注册过了', null, 101);
    }

    //教师登录
    public function YjyTeacherLogin(Request $request): JsonResponse
    {
        //获取账号
        $credentials['username'] = $request['username'];
        //获取明文密码
        $credentials['password'] = $request['password'];
        //通过验证并生成令牌
        $token = auth('api')->attempt($credentials);
        //运用三目运算符
        return $token ?
            json_success('登录成功!', $token, 200) :
            json_fail('登录失败!账号或密码错误', null, 100);
    }

    //退出登录
    public function logoutUser()
    {
        auth('api')->logout();
        return json_success("用户退出登录成功", null, 200);
    }










    //管理员接口


    //查看所有课程
    public function getAllCourse(Request $request)
    {
        try {
            // 动态获取每页数据量，默认 10 条
            $perPage = $request->input('per_page', 10);

            // 分页查询
            $courses = Course::paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $courses->items(),   // 当前页数据
                'pagination' => [
                    'total' => $courses->total(),       // 总数据量
                    'per_page' => $courses->perPage(),   // 每页数据量
                    'current_page' => $courses->currentPage(), // 当前页码
                    'last_page' => $courses->lastPage(),       // 最后一页
                    'next_page_url' => $courses->nextPageUrl(), // 下一页链接
                    'prev_page_url' => $courses->previousPageUrl(), // 上一页链接
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '获取课程失败：' . $e->getMessage()
            ], 100);
        }
    }


    // 创建新课程
    public function createCourse(Request $request)
    {
        $CourseData = self::dealCourse($request);
        $course = Course::create($CourseData + ['status' => Course::STATUS_APPROVED]);
        return $course ?
            json_success('课程创建成功', $course, 200) :
            json_fail('课程创建失败', null, 100);
    }


    //处理课程数据
    public static function dealCourse(Request $request)
    {
        $CourseData['course_code'] = $request['course_code'];
        $CourseData['course_name'] = $request['course_name'];
        $CourseData['course_category'] = $request['course_category'];
        $CourseData['course_type'] = $request['course_type'];
        $CourseData['credit'] = $request['credit'];
        $CourseData['course_hours'] = $request['course_hours'];
        $CourseData['class_name'] = $request['class_name'];
        $CourseData['class_size'] = $request['class_size'];
        $CourseData['semester'] = $request['semester'];
        $CourseData['grade'] = $request['grade'];
        $CourseData['major'] = $request['major'];
        $CourseData['department'] = $request['department'];
        return $CourseData;
    }


// 修改课程（不能修改教师字段）
    public function updateCourse(Request $request, $CourseId)
    {
        try {
            $Course = Course::updatedCourse($request, $CourseId);
            return response()->json([
                'msg' => '修改课程成功！',
                'data' => $Course
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->errors()
            ], 100);
        }
    }


    //查看所有教师
    public function getAllTeacher(Request $request)
    {
        try {
            // 验证分页参数
            $validator = Validator::make($request->all(), [
                'page' => 'sometimes|integer|min:1',
                'per_page' => 'sometimes|integer|min:1|max:20', // 限制每页最多20条
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => '参数错误',
                    'errors' => $validator->errors()
                ], 400);
            }

            // 动态分页查询教师用户
            $perPage = $request->input('per_page', 10);
            $teachers = User::where('role', 'teacher')
                ->paginate($perPage);

            // 返回分页数据（包含教师列表和分页信息）
            return response()->json([
                'status' => 'success',
                'data' => $teachers->items(),
                'pagination' => [
                    'total' => $teachers->total(),
                    'per_page' => $teachers->perPage(),
                    'current_page' => $teachers->currentPage(),
                    'last_page' => $teachers->lastPage(),
                    'next_page_url' => $teachers->nextPageUrl(),
                    'prev_page_url' => $teachers->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '获取教师列表失败：' . $e->getMessage()
            ], 100);
        }
    }


    //创建用户
    public function createUser(Request $request)
    {
        // 验证通过后，创建用户
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'teacher_name' => $request->teacher_name,
            'department' => $request->department,
            'role' => $request->role // 确保此处是 teacher 或 admin
        ]);
        return $user ?
            json_success('用户创建成功', $user, 200) :
            json_fail('用户创建失败', null, 100);
    }


    // 重置密码
    public function resetPassword(Request $request, User $teacher)
    {
        try {

            // 更新密码
            $teacher->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => '教师密码已重置'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '操作失败：' . $e->getMessage()
            ], 100);
        }
    }



}
