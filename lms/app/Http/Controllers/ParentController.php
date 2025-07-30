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
    public function dashboard(Request $request)
    {
        try {
            $parent = Auth::user();
            
            if ($parent->role_name !== 'Parent') {
                abort(403, 'Access denied. Only parents can view this dashboard.');
            }

            // Get all children linked to this parent
            $children = Student::where('parent_email', $parent->email)->get();
            
            if ($children->isEmpty()) {
                return view('dashboard.parent_dashboard', [
                    'children' => collect(),
                    'selectedChild' => null,
                    'grades' => collect(),
                    'attendance' => collect(),
                    'lessons' => collect(),
                    'activities' => collect(),
                    'submissions' => collect(),
                    'performanceInsights' => [],
                    'currentAcademicYear' => null,
                    'currentSemester' => null,
                    'noChildren' => true
                ]);
            }

            // Get selected child (default to first child)
            $selectedChildId = $request->input('child_id', $children->first()->id);
            $selectedChild = $children->find($selectedChildId);
            
            if (!$selectedChild) {
                $selectedChild = $children->first();
            }

            // Get current academic year and semester (fallback to latest if no active ones)
            $currentAcademicYear = AcademicYear::latest()->first();
            $currentSemester = Semester::latest()->first();

            // Get grades for selected child
            $grades = $this->getChildGrades($selectedChild, $currentAcademicYear, $currentSemester);
            
            // Get attendance for selected child
            $attendance = $this->getChildAttendance($selectedChild, $request);
            
            // Get lessons and activities for selected child
            $lessons = $this->getChildLessons($selectedChild, $currentAcademicYear, $currentSemester);
            $activities = $this->getChildActivities($selectedChild, $currentAcademicYear, $currentSemester);
            
            // Get submissions for selected child
            $submissions = $this->getChildSubmissions($selectedChild, $currentAcademicYear, $currentSemester);
            
            // Get performance insights
            $performanceInsights = $this->getPerformanceInsights($selectedChild, $currentAcademicYear, $currentSemester);

            return view('dashboard.parent_dashboard', compact(
                'children',
                'selectedChild',
                'grades',
                'attendance',
                'lessons',
                'activities',
                'submissions',
                'performanceInsights',
                'currentAcademicYear',
                'currentSemester'
            ));
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Parent Dashboard Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a simple error view
            return view('dashboard.parent_dashboard', [
                'children' => collect(),
                'selectedChild' => null,
                'grades' => collect(),
                'attendance' => collect(),
                'lessons' => collect(),
                'activities' => collect(),
                'submissions' => collect(),
                'performanceInsights' => [],
                'currentAcademicYear' => null,
                'currentSemester' => null,
                'error' => 'An error occurred while loading the dashboard: ' . $e->getMessage()
            ]);
        }
    }

    private function getChildGrades($child, $academicYear, $semester)
    {
        $query = Grade::where('student_id', $child->id)
            ->with(['subject', 'component', 'teacher']);

        if ($academicYear) {
            $query->where('academic_year_id', $academicYear->id);
        }

        if ($semester) {
            $query->where('semester_id', $semester->id);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    private function getChildAttendance($child, $request)
    {
        $query = Attendance::where('student_id', $child->id)
            ->with(['subject', 'teacher']);

        // Filter by month if provided
        if ($request->filled('month')) {
            $month = Carbon::parse($request->month);
            $query->whereYear('date', $month->year)
                  ->whereMonth('date', $month->month);
        } else {
            // Default to current month
            $query->whereYear('date', now()->year)
                  ->whereMonth('date', now()->month);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    private function getChildLessons($child, $academicYear, $semester)
    {
        // Get lessons for the child's section
        $query = Lesson::where('section_id', $child->sections->first()->id ?? 0)
            ->with(['teacher', 'subject', 'activities'])
            ->where('status', 'published');

        if ($academicYear) {
            $query->where('academic_year_id', $academicYear->id);
        }

        if ($semester) {
            $query->where('semester_id', $semester->id);
        }

        return $query->orderBy('lesson_date', 'desc')->limit(10)->get();
    }

    private function getChildActivities($child, $academicYear, $semester)
    {
        // Get activities from lessons in the child's section
        $query = Activity::whereHas('lesson', function($q) use ($child, $academicYear, $semester) {
            $q->where('section_id', $child->sections->first()->id ?? 0)
              ->where('status', 'published');
            
            if ($academicYear) {
                $q->where('academic_year_id', $academicYear->id);
            }
            
            if ($semester) {
                $q->where('semester_id', $semester->id);
            }
        })->with(['lesson.subject', 'lesson.teacher']);

        return $query->orderBy('due_date', 'asc')->limit(15)->get();
    }

    private function getChildSubmissions($child, $academicYear, $semester)
    {
        $query = ActivitySubmission::where('student_id', $child->id)
            ->with(['activity.lesson.subject', 'activity.lesson.teacher', 'grades']);

        if ($academicYear) {
            $query->whereHas('activity.lesson', function($q) use ($academicYear) {
                $q->where('academic_year_id', $academicYear->id);
            });
        }

        if ($semester) {
            $query->whereHas('activity.lesson', function($q) use ($semester) {
                $q->where('semester_id', $semester->id);
            });
        }

        return $query->orderBy('submitted_at', 'desc')->limit(10)->get();
    }

    private function getPerformanceInsights($child, $academicYear, $semester)
    {
        $insights = [];

        // Get current GPA
        $currentGpa = StudentGpa::where('student_id', $child->id);
        if ($academicYear) {
            $currentGpa->where('academic_year_id', $academicYear->id);
        }
        if ($semester) {
            $currentGpa->where('semester_id', $semester->id);
        }
        $currentGpa = $currentGpa->latest()->first();

        // Low GPA warning
        if ($currentGpa && $currentGpa->gpa < 2.5) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Low GPA Alert',
                'message' => "Current GPA: {$currentGpa->gpa}. Consider additional support.",
                'color' => 'warning'
            ];
        }

        // Get overdue activities
        $overdueActivities = Activity::whereHas('lesson', function($q) use ($child, $academicYear, $semester) {
            $q->where('section_id', $child->sections->first()->id ?? 0);
            if ($academicYear) $q->where('academic_year_id', $academicYear->id);
            if ($semester) $q->where('semester_id', $semester->id);
        })->where('due_date', '<', now())
          ->whereDoesntHave('submissions', function($q) use ($child) {
              $q->where('student_id', $child->id);
          })->count();

        if ($overdueActivities > 0) {
            $insights[] = [
                'type' => 'danger',
                'icon' => 'fas fa-clock',
                'title' => 'Overdue Activities',
                'message' => "{$overdueActivities} activity(ies) past due date.",
                'color' => 'danger'
            ];
        }

        // Get low grades
        $lowGrades = Grade::where('student_id', $child->id)
            ->where('percentage', '<', 75);
        if ($academicYear) $lowGrades->where('academic_year_id', $academicYear->id);
        if ($semester) $lowGrades->where('semester_id', $semester->id);
        $lowGrades = $lowGrades->with('subject')->get();

        if ($lowGrades->count() > 0) {
            $subjects = $lowGrades->pluck('subject.subject_name')->unique()->implode(', ');
            $insights[] = [
                'type' => 'info',
                'icon' => 'fas fa-chart-line',
                'title' => 'Low Grades Detected',
                'message' => "Low grades in: {$subjects}",
                'color' => 'info'
            ];
        }

        // Get attendance issues
        $recentAttendance = Attendance::where('student_id', $child->id)
            ->where('date', '>=', now()->subDays(30))
            ->get();

        $absentCount = $recentAttendance->where('status', 'absent')->count();
        if ($absentCount > 3) {
            $insights[] = [
                'type' => 'warning',
                'icon' => 'fas fa-user-times',
                'title' => 'Attendance Concern',
                'message' => "{$absentCount} absences in the last 30 days.",
                'color' => 'warning'
            ];
        }

        return $insights;
    }

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
} 