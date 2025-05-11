<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\DB;
>>>>>>> 04eaf31 (first)

class CourseAssignment extends Model
{
    use HasFactory;
<<<<<<< HEAD
    protected $fillable = ['user_id', 'course_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
=======

    //批量操作赋值
    protected $fillable = [
        'course_id',
        'teacher_id',
    ];

    protected $with = [
        'courses', 'users'
    ]; // 默认预加载关联

    // 关联课程
>>>>>>> 04eaf31 (first)
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
<<<<<<< HEAD
=======

    // 关联教师
    public function teacher()
    {
        return $this->belongsTo(User::class);
    }

    // 关联操作管理员
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

>>>>>>> 04eaf31 (first)
}
