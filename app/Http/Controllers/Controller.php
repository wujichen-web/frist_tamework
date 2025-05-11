<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
class Controller
{

=======
use App\Exports\YourExportClassNameJingsai;
use App\Exports\YourExportClassNameShuangc;
use App\Exports\YourExportClassNameSic;
use App\Models\Admins;
use App\Models;
use App\Models\Curd;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use OSS\OssClient;

class Controller
{//继承Laravel基础控制器
    //类方法从这里开始

    //管理员密码处理
    protected function YjyAdminHandle($request){
        //从请求中提取账号
        $registeredInfo['account'] = $request['account'];
        //提取判断字段
        $registeredIfo['judge'] = $request['judge'];
        //用bcrypt哈希加密密码
        $registeredInfo['password'] = $request['password'];
        //返回处理后的数据
        return $registeredInfo;
    }

    //管理员登录
    public function YjyAdminLogin(Request $request): JsonResponse {
        //获取账号
        $credentials['account'] = $request['account'];
        //获取明文密码
        $credentials['password'] = $request['password'];
        //通过admin guard 验证并生成令牌
        $token = auth('admin') -> attempt($credentials);
        //运用三目运算符
        return $token?
            json_success('登录成功!',$token,  200):
            json_fail('登录失败!账号或密码错误',null, 100 ) ;
    }
    //管理员注册
    public function YjyAdminregister(Request $request){
        //请求处理数据
        $registeredInfo = self::YjyAdminHandle($request);
        $email = $registeredInfo['account'];
        //检查账号是否存在
        $count = Admins::Yjychecknumber($email);
        if (is_error($count) == true){
            //检查是否存在错误
            return json_fail('注册失败！检测是否存在的时候出错啦',$count,100);
        }
        if ($count == 0){
            //如果账号不存在
            $student_id = Admins::YjycreateUser($registeredInfo);//创建管理员
            if (is_error($student_id) == true){
                //检查创建是否出错
                return json_fail('注册失败！添加数据的时候有问题',$student_id,100);
            }
            return json_success('注册成功！',$student_id,200);
        }
        return json_fail('注册失败！该用户信息已经被注册过了',null,101);
    }

    //用户注册
    public function YyhUserregister(Request $request){
        $code_64 = $request['code_a'];
        $code_a = base64_decode($code_64);
        $code = $request['code'];
        if($code_a === $code) {
            $registeredInfo = self::YjyAdminHandle($request);
            $count = Admins::examine($registeredInfo['account']);   //检测账号密码是否存在

            if (is_error($count) == true) {
                return json_fail('注册失败!检测是否存在的时候出错啦', $count, 100);
            }
            if ($count == 0) {
                $account = $registeredInfo['account'];
                $password = $registeredInfo['password'];
                $email = $registeredInfo['email'];
                $data = Admins::createUser($account, $password, $email);

                if (is_error($data) == true) {
                    return json_fail('注册失败!添加数据的时候有问题', $data, 100);
                }
                return json_success('注册成功!', $data, 200);
            }
            return json_fail('注册失败!该用户信息已经被注册过了', null, 101);
        }else{
            return json_fail('注册失败!该验证码不正确', null, 102);
        }
    }

    //打印并终止执行（调试
    public function YjyUserregister(Request $request){
        dd(1);
    }

    //退出登录
    public function logoutUser(){
        auth('user')->logout();
        return json_success("用户退出登录成功",null,200);
    }
>>>>>>> 04eaf31 (first)
}
