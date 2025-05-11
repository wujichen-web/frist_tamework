<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CourseInfo extends Model
{
    use HasFactory;

    protected $table = 'course_info';

    /**
     * 获取可供选择的课程列表
     *
     * @param int $perPage 每页显示的课程数量，默认值为10
     * @param int $page 当前页码，默认值为1
     * @param string|null $semester 学期，可选参数
     * @param string|null $keyword 搜索关键字，可选参数
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|null 如果用户未登录则返回null，否则返回课程列表的分页对象
     */
    public static function getSelectableCourses($perPage = 10, $page = 1, $semester = null, $keyword = null)
    {
        // 获取当前登录教师的信息
        $teacher = Auth::user();
        if (!$teacher) {
            // 可以选择记录日志或者抛出异常，这里简单返回null
            return null;
        }

        // 获取教师所在系
        $department = $teacher->department;

        // 初始化查询构建器
        $query = self::where('department', $department);

        // 根据学期筛选课程
        if ($semester) {
            $query->where('semester', $semester);
        }

        // 根据关键字搜索课程
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('course_name', 'like', "%$keyword%")
                    ->orWhere('course_code', 'like', "%$keyword%");
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
