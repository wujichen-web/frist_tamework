<?php
namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle($request, Closure $next, $role) {
        // 检查用户是否登录
        if (!auth()->check()) {
            return redirect()->route('YjyAdminLogin');
        }

        // 验证角色是否匹配（例如：$role='admin' 或 'teacher'）
        if (auth()->user()->role !== $role) {
            abort(403, '无权限访问此页面'); // 返回403错误
        }

        return $next($request);
    }
}
