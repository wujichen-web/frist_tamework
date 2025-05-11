<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    
    protected $fillable = [
        'username', 'password', 'teacher_name', 'department', 'role'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
=======
use Couchbase\Role;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'username','password','teacher_name','department','role'
    ];


    //关联数据库表名
    protected $table = 'users';
    //启用自动维护create_at 和update_at时间戳字段
    public $timestamps = true;
    //自定义主键字段名
    protected $primaryKey = 'id';

    // 用户角色类型
    const ROLE_ADMIN = 'admin';
    const ROLE_TEACHER = 'teacher';


    // 关联课程（一个教师可以申请多个课程）
    //定义与选课表的一对多关系
    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    // JWT 获取用户标识
>>>>>>> 04eaf31 (first)
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
<<<<<<< HEAD
    
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    
    public function assignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }

    public function applications()
{
    return $this->hasMany(CourseApplication::class);
}

=======

    // JWT 自定义声明（携带角色信息）
    public function getJWTCustomClaims()
    {
        return ['role' => 'teacher'];
    }

    // 判断是否是教师
    public function isTeacher() {
        return $this->role === 'teacher';
    }
    // 检查是否是管理员
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // 检查是否是通过审核的教师
    public function isApprovedTeacher()
    {
        return $this->role === 'teacher';
    }

    //检查账号是否存在
    public static function YjycheckNumber($username)
    {
        try {
            //查询数据库中username字段匹配的记录数量
            $count = User::select('username')
                ->where('username', $username)
                ->count();
            return $count;//返回0（不存在） 或1（存在）
        } catch (Exception $e) {
            return 'error' . $e->getMessage();//返回错误信息（需改进为异常抛出）
        }
    }


    //创建管理员用户
    public static function YjycreateAdmin($registeredInfo)
    {
        try {
            //创建新管理员记录（需确保密码已经哈希加密）
            $user_id = User::create([
                'username' => $registeredInfo['username'],
                'password' => bcrypt($registeredInfo['password']),//在创建用户前对密码进行哈希处理
                'teacher_name' => $registeredInfo['teacher_name'],
                'department' => $registeredInfo['department'],
                'role' => $registeredInfo['role']
            ])->id;
            return $user_id;//返回新用户id
        } catch (Exception $e) {
            return 'error' . $e->getMessage();//返回错误信息
        }
    }

    //创建教师用户
    public static function YjycreateTeacher($registeredInfo)
    {
        try {
            //创建新管理员记录（需确保密码已经哈希加密）
            $user_id = User::create([
                'username' => $registeredInfo['username'],
                'password' => bcrypt($registeredInfo['password']),//在创建用户前对密码进行哈希处理
                'teacher_name' => $registeredInfo['teacher_name'],
                'department' => $registeredInfo['department'],
                'role' => $registeredInfo['role']
            ])->id;
            return $user_id;//返回新用户id
        } catch (Exception $e) {
            return 'error' . $e->getMessage();//返回错误信息
        }
    }
>>>>>>> 04eaf31 (first)
}
