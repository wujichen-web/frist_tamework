<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    public function run()
    {
        try {
            // 检查数据库中是否已经存在账户名为 'test1' 的用户
            if (!User::where('account', 'test1')->exists()) {
                // 定义用户数据，添加 id 字段
                $userData = [
                    'id' => 2,
                    'account' => 'yuyang',
                    'password' => Hash::make('123456'),
                    'teacher_name' => '余阳',
                    'department' => '智能系统应用系',
                    'role' => 'admin'
                ];

                // 创建新的用户记录
                User::create($userData);
            }
        } catch (\Exception $e) {
            // 记录错误信息到日志中
            Log::error('Users seeding failed: '. $e->getMessage());
        }
    }
}
