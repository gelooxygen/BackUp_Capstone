<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'roll',
        'blood_group',
        'religion',
        'email',
        'class',
        'section',
        'admission_id',
        'phone_number',
        'upload',
    ];

    public function enrollments() { return $this->hasMany(Enrollment::class); }
    public function subjects() { return $this->belongsToMany(Subject::class, 'enrollments'); }
    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_student', 'student_id', 'section_id');
    }
}
