<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAssignment extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // 明确主键为 id
    // 更新可填充字段数组，添加新字段
    protected $fillable = [
        'teacher_id',
        'course_code',
        'course_name',
        'course_category',
        'course_nature',
        'credits',
        'status',
        'class_name',
        'leader',
        'major',
        'total_hours',
        'grade',
        'student_num',
        'semester',
        'department'
    ];

    // 定义与 CourseInfo 模型的关联，假设 course_code 是关联字段
    public function course()
    {
        return $this->belongsTo(CourseInfo::class, 'course_code');
    }

    // 修正与 User 模型的关联，假设 teacher_id 是关联到 User 模型（这里假设为教师模型）的外键
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
