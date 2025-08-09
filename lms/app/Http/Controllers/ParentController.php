<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Activity;
use App\Models\ActivitySubmission;
use App\Models\StudentGpa;
use App\Models\Subject;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ParentController extends Controller
{
    public function childGrades($childId, Request $request)
    {
        $parent = Auth::user();
        $child = Student::where('parent_email', $parent->email)
            ->where('id', $childId)
            ->firstOrFail();

        $academicYear = AcademicYear::latest()->first();
        $semester = Semester::latest()->first();

        $grades = $this->getChildGrades($child, $academicYear, $semester);
        $subjects = Subject::all();

        return view('parent.child_grades', compact('child', 'grades', 'subjects', 'academicYear', 'semester'));
    }

    public function childAttendance($childId, Request $request)
    {
        $parent = Auth::user();
        $child = Student::where('parent_email', $parent->email)
            ->where('id', $childId)
            ->firstOrFail();

        $attendance = $this->getChildAttendance($child, $request);
        $subjects = Subject::all();

        return view('parent.child_attendance', compact('child', 'attendance', 'subjects'));
    }

    public function childActivities($childId, Request $request)
    {
        $parent = Auth::user();
        $child = Student::where('parent_email', $parent->email)
            ->where('id', $childId)
            ->firstOrFail();

        $academicYear = AcademicYear::latest()->first();
        $semester = Semester::latest()->first();

        $activities = $this->getChildActivities($child, $academicYear, $semester);
        $submissions = $this->getChildSubmissions($child, $academicYear, $semester);

        return view('parent.child_activities', compact('child', 'activities', 'submissions', 'academicYear', 'semester'));
    }

    public function childProfile($childId, Request $request)
    {
        $parent = Auth::user();
        $child = Student::where('parent_email', $parent->email)
            ->where('id', $childId)
            ->firstOrFail();

        // Get current academic year and semester
        $academicYear = AcademicYear::latest()->first();
        $semester = Semester::latest()->first();

        // Get child's enrollments
        $enrollments = $child->enrollments()
            ->with(['subject', 'academicYear', 'semester'])
            ->where('status', 'active')
            ->get();

        // Get basic statistics
        $totalGrades = Grade::where('student_id', $child->id)->count();
        $averageGrade = Grade::where('student_id', $child->id)->avg('percentage') ?? 0;
        $totalAttendance = Attendance::where('student_id', $child->id)->count();
        $presentAttendance = Attendance::where('student_id', $child->id)
            ->where('status', 'present')
            ->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 2) : 0;

        // Get recent grades
        $recentGrades = Grade::where('student_id', $child->id)
            ->with('subject')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent attendance
        $recentAttendance = Attendance::where('student_id', $child->id)
            ->with('subject')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('parent.child_profile', compact(
            'child',
            'enrollments',
            'academicYear',
            'semester',
            'totalGrades',
            'averageGrade',
            'totalAttendance',
            'presentAttendance',
            'attendancePercentage',
            'recentGrades',
            'recentAttendance'
        ));
    }
} 