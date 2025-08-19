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
        'parent_email',
        'class',
        'year_level',
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
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function attendances() {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function gpaRecords()
    {
        return $this->hasMany(StudentGpa::class);
    }

    public function gradeAlerts()
    {
        return $this->hasMany(GradeAlert::class);
    }

    // Get current GPA for a specific academic period
    public function getCurrentGpa($academicYearId = null, $semesterId = null)
    {
        $query = $this->gpaRecords();
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }
        
        return $query->latest()->first();
    }

    // Get active alerts
    public function getActiveAlerts()
    {
        return $this->gradeAlerts()->where('is_resolved', false)->get();
    }

    /**
     * Get the student's full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
