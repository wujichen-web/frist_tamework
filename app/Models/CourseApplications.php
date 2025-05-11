<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CourseApplications extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // 明确主键为 id
    protected $fillable = [
        'id',
        'course_code',
        'course_name',
        'course_category',
        'course_nature',
        'credits',
        'status',
        'student_num',
        'total_hours',
        'semester',
        'class_account'
    ];

    public static function selectCourse($courseCode)
    {
        // 获取当前登录教师的信息
        $teacher = Auth::user();
        if (!$teacher) {
            return ['success' => false,'message' => '未找到教师信息'];
        }

        // 检查课程是否属于该教师所在系
        $course = CourseInfo::where('course_code', $courseCode)
            ->where('department', $teacher->department)
            ->first();
        if (!$course) {
            return ['success' => false,'message' => '课程不属于您所在系或不存在'];
        }

        // 检查该课程是否已被选择
        $existingApplication = self::where('id', $teacher->id)
            ->where('course_code', $courseCode)
            ->first();
        if ($existingApplication) {
            return ['success' => false,'message' => '您已选择该课程'];
        }

        // 统计 course_info 表中同名课程的总数
        $classAccount = CourseInfo::where('course_name', $course->course_name)
            ->count();

        // 创建课程选择记录到 course_applications 表
        self::create([
            'id' => $teacher->id,
            'course_code' => $courseCode,
            'course_name' => $course->course_name,
            'course_category' => $course->course_category,
            'course_nature' => $course->course_nature,
            'credits' => $course->credits,
            'status' => '未审核',
            'semester' => $course->semester,
            'class_account' => $classAccount
        ]);

        return ['success' => true,'message' => '课程选择成功，等待审核'];
    }
}
