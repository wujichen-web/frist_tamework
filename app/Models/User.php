<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $table = "users";
    public $timestamps = true;
    protected $primaryKey = "id";
    protected $guarded = [];
    protected $fillable = [
        'account', 'password', 'teacher_name', 'department', 'role'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return ["role" => $this->role];
    }

    public static function createUser($registeredInfo)
    {
        try {
            $user = self::create([
                'account' => $registeredInfo['account'],
                'password' => Hash::make($registeredInfo['password']),
                'teacher_name' => $registeredInfo['teacher_name']?? null,
                'department' => $registeredInfo['department'],
                'role' => $registeredInfo['role']
            ]);
            return $user->id;
        } catch (Exception $e) {
            return 'error'.$e->getMessage();
        }
    }
}
