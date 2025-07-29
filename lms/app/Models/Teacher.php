<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'full_name',
        'gender',
        'date_of_birth',
        'qualification',
        'experience',
        'phone_number',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id');
    }

    public function gradeLevels()
    {
        return $this->hasMany(\App\Models\TeacherGradeLevel::class, 'teacher_id');
    }
}
