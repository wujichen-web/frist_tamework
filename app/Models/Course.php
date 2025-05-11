<?php
<<<<<<< HEAD

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_name', 'course_code', 'course_category', 'course_type', 
        'credit', 'class_hours', 'grade', 'major', 'department', 
        'class_name', 'class_size', 'semester'
    ];
    
    public function applications()
    {
        return $this->hasMany(CourseApplication::class);
    }
    
    public function assignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }
=======
namespace App\Models;

use App\Http\Controllers\YjyController;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Course extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];
    //允许所有字段批量赋值
    protected $fillable = [
        'course_code', 'course_name', 'course_category', 'course_type',
        'credit', 'course_hours', 'class_name', 'class_size',
        'semester', 'grade', 'major', 'department'
    ];
    // 字段类型转换
    protected $casts = [
        'credit'    => 'integer',
        'course_hours'     => 'integer',
        'class_size'    => 'integer',
        'created_at'       => 'datetime:Y-m-d H:i:s',
        'updated_at'       => 'datetime:Y-m-d H:i:s'
    ];    //关联数据库表名
    protected $table = 'courses';
    //启用自动维护create_at 和update_at时间戳字段
    public $timestamps = true;
    //自定义主键字段名
    protected $primaryKey = 'id';

    // 课程状态
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // 一门课程属于一个教师（可以为空）
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    //定义与选课表的一对多关系
    public function selectedCourse()
    {
        return $this->hasMany(CourseAssignment::class, 'course_id');
    }

    // 检查是否已审核通过
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public static function UpdatedCourse(Request $request,$courseId)
    {

        try {
            $CourseData['course_code'] = $request['course_code'];
            $CourseData['course_name'] = $request['course_name'];
            $CourseData['course_category'] = $request['course_category'];
            $CourseData['course_type'] = $request['course_type'];
            $CourseData['credit'] = $request['credit'];
            $CourseData['course_hours'] = $request['course_hours'];
            $CourseData['class_name'] = $request['class_name'];
            $CourseData['class_size'] = $request['class_size'];
            $CourseData['grade'] = $request['grade'];
            $CourseData['major'] = $request['major'];
            $CourseData['department'] = $request['department'];

            $course = self::findOrFail($courseId);
            $course->update($CourseData);
            return $course;
        } catch (Exception $e) {
            return $e; // 抛出异常由上层处理
        }
    }

>>>>>>> 04eaf31 (first)
}
