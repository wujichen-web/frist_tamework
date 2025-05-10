<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;


class LrzAdminsController extends Controller
{
    // 管理员登录接口
    public function LrzAdminLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account' =>'required|string',
            'password' =>'required|string',
        ]);

        if ($validator->fails()) {
            return json_fail('输入验证失败',null, 422);
        }

        $credentials = $request->only('account', 'password');

        $user = User::where('account', $credentials['account'])->where('role', 'admin')->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            try {
                $token = JWTAuth::fromUser($user);
                return json_success('登陆成功', ['token' => $token], 200);
            } catch (\Exception $e) {
                return json_fail(
                    '生成令牌时出现错误',null, 500);
            }
        }

        return response()->json([
            'message' => '登陆失败账号或密码错误'
        ], 100);
    }


    // 新增用户（教师或管理员）接口，仅管理员端可用
    public
    function LrzCreateUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required|string|alpha_num|unique:users,account',
            'password' => 'required|string|min:6',
            'teacher_name' => 'string|nullable',
            'department' => 'required|string',
            'role' => 'required|in:teacher,admin'
        ]);

        if ($validator->fails()) {
            return json_fail('输入验证失败', ['errors' => $validator->errors()], 422);
        }

        $registeredInfo = $request->only('account', 'password', 'teacher_name', 'department', 'role');

        $result = User::createUser($registeredInfo);
        if (is_numeric($result)) {
            return json_success('用户创建成功', ['user_id' => $result], 200);
        } else {
            return json_fail('用户创建失败', ['error' => $result], 100);
        }
    }
}


