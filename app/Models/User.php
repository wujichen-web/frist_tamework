<?php

namespace App\Models;

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
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
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

}
