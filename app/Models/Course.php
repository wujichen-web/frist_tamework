<?php

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
}
