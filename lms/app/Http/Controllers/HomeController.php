<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\CalendarEvent;
use App\Models\Attendance;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    /** home dashboard */
    public function index()
    {
        return $this->dashboard();
    }

    /** profile user */
    public function userProfile()
    {
        return view('dashboard.profile');
    }

    public function teacherClasses()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }
        
        // Get teacher's subjects with sections and students
        $teacherSubjects = $teacher->subjects()->with(['sections' => function($query) {
            $query->with(['enrollments' => function($q) {
                $q->with(['student'])->where('status', 'active');
            }]);
        }])->get();
        
        return view('teacher.classes', compact('teacher', 'teacherSubjects'));
    }

    /**
     * Unified dashboard route that returns the correct dashboard for each role.
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Initialize variables for all roles
        $data = [
            'user' => $user,
            'admin' => null,
            'teacher' => null,
            'student' => null,
            'parent' => null
        ];

        // Load data based on user role
        if ($user->role_name === User::ROLE_ADMIN) {
            $data['admin'] = $this->loadAdminData();
        } elseif ($user->role_name === User::ROLE_TEACHER) {
            $data['teacher'] = $this->loadTeacherData();
        } elseif ($user->role_name === User::ROLE_STUDENT) {
            $data['student'] = $this->loadStudentData();
        } elseif ($user->role_name === User::ROLE_PARENT) {
            $data['parent'] = $this->loadParentData();
        } else {
            abort(403);
        }

        return view('dashboard', $data);
    }

    /**
     * Load admin dashboard data
     */
    private function loadAdminData()
    {
        // Get real data for admin dashboard
        $totalStudents = \App\Models\Student::count();
        $totalTeachers = \App\Models\Teacher::count();
        $totalSubjects = \App\Models\Subject::count();
        $totalSections = \App\Models\Section::count();
        $totalEnrollments = \App\Models\Enrollment::where('status', 'active')->count();
        $totalAttendance = \App\Models\Attendance::count();
        $totalGrades = \App\Models\Grade::count();
        $totalAnnouncements = \App\Models\Announcement::count();
        
        // Get recent activities
        $recentEnrollments = \App\Models\Enrollment::with(['student', 'subject'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentAnnouncements = \App\Models\Announcement::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get top performing students (based on GPA)
        $topStudents = \App\Models\StudentGpa::with(['student', 'academicYear', 'semester'])
            ->orderBy('gpa', 'desc')
            ->take(5)
            ->get();
            
        // Get attendance statistics
        $attendanceStats = \App\Models\Attendance::selectRaw('
            COUNT(*) as total_records,
            SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
            SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count
        ')->first();
        
        $attendancePercentage = $attendanceStats->total_records > 0 
            ? round(($attendanceStats->present_count / $attendanceStats->total_records) * 100, 1)
            : 0;
            
        // Get gender distribution
        $maleStudents = \App\Models\Student::where('gender', 'Male')->count();
        $femaleStudents = \App\Models\Student::where('gender', 'Female')->count();
        
        // Get recent calendar events
        $recentEvents = \App\Models\CalendarEvent::with(['subject', 'teacher'])
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();
            
        return compact(
            'totalStudents',
            'totalTeachers', 
            'totalSubjects',
            'totalSections',
            'totalEnrollments',
            'totalAttendance',
            'totalGrades',
            'totalAnnouncements',
            'recentEnrollments',
            'recentAnnouncements',
            'topStudents',
            'attendanceStats',
            'attendancePercentage',
            'maleStudents',
            'femaleStudents',
            'recentEvents'
        );
    }

    /**
     * Load teacher dashboard data
     */
    private function loadTeacherData()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return [
                'teacher' => null,
                'totalClasses' => 0,
                'totalStudents' => 0,
                'totalLessons' => 0,
                'totalHours' => 0,
                'upcomingLessons' => collect(),
                'semesterProgress' => 0,
                'teachingHistory' => collect(),
                'upcomingEvents' => collect(),
                'attendanceStats' => (object)['total_records' => 0, 'present_count' => 0, 'absent_count' => 0],
                'attendancePercentage' => 0
            ];
        }
        
        // Get teacher's subjects
        $teacherSubjects = $teacher->subjects()->get();
        
        // Get total classes (sections where teacher is adviser)
        $totalClasses = Section::where('adviser_id', $teacher->id)->count();
        
        // Get total students across all teacher's subjects
        $totalStudents = Enrollment::whereIn('subject_id', $teacherSubjects->pluck('id'))
            ->where('status', 'active')
            ->distinct('student_id')
            ->count('student_id');
        
        // Get total lessons (subjects taught by teacher)
        $totalLessons = $teacherSubjects->count();
        
        // Get total hours (calculate from calendar events or use a default)
        $totalHours = CalendarEvent::where('teacher_id', $teacher->id)
            ->where('start_time', '>=', now()->startOfMonth())
            ->where('start_time', '<=', now()->endOfMonth())
            ->count();
        
        // Get upcoming lessons (calendar events)
        $upcomingLessons = CalendarEvent::with(['subject'])
            ->where('teacher_id', $teacher->id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();
        
        // Get semester progress (calculate based on completed vs total events)
        $totalEventsThisMonth = CalendarEvent::where('teacher_id', $teacher->id)
            ->where('start_time', '>=', now()->startOfMonth())
            ->where('start_time', '<=', now()->endOfMonth())
            ->count();
        
        $completedEventsThisMonth = CalendarEvent::where('teacher_id', $teacher->id)
            ->where('start_time', '>=', now()->startOfMonth())
            ->where('start_time', '<=', now()->endOfMonth())
            ->where('start_time', '<=', now())
            ->count();
        
        $semesterProgress = $totalEventsThisMonth > 0 
            ? round(($completedEventsThisMonth / $totalEventsThisMonth) * 100, 1)
            : 0;
        
        // Get teaching history (recent calendar events)
        $teachingHistory = CalendarEvent::with(['subject'])
            ->where('teacher_id', $teacher->id)
            ->where('start_time', '<=', now())
            ->orderBy('start_time', 'desc')
            ->take(10)
            ->get();
        
        // Get upcoming events for calendar
        $upcomingEvents = CalendarEvent::with(['subject'])
            ->where('teacher_id', $teacher->id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(10)
            ->get();
        
        // Get attendance statistics for teacher's students
        $attendanceStats = Attendance::whereIn('subject_id', $teacherSubjects->pluck('id'))
            ->selectRaw('
                COUNT(*) as total_records,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count
            ')->first();
        
        $attendancePercentage = $attendanceStats->total_records > 0 
            ? round(($attendanceStats->present_count / $attendanceStats->total_records) * 100, 1)
            : 0;
        
        return compact(
            'teacher',
            'totalClasses',
            'totalStudents', 
            'totalLessons',
            'totalHours',
            'upcomingLessons',
            'semesterProgress',
            'teachingHistory',
            'upcomingEvents',
            'attendanceStats',
            'attendancePercentage'
        );
    }

    /**
     * Load student dashboard data
     */
    private function loadStudentData()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            // Try to find student by user_id as fallback
            $student = Student::where('user_id', $user->user_id)->first();
            
            if (!$student) {
                return null;
            }
        }
        
        // Get student's enrollments with related data
        $enrollments = $student->enrollments()
            ->with(['subject', 'academicYear', 'semester'])
            ->where('status', 'active')
            ->get();
        
        return compact('student', 'enrollments');
    }

    /**
     * Load parent dashboard data
     */
    private function loadParentData()
    {
        try {
            $parent = auth()->user();
            
            if ($parent->role_name !== 'Parent') {
                return null;
            }

            // Get all children linked to this parent
            $children = Student::where('parent_email', $parent->email)->get();
            
            if ($children->isEmpty()) {
                return [
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
                ];
            }

            // Get selected child (default to first child)
            $selectedChildId = request()->input('child_id', $children->first()->id);
            $selectedChild = $children->find($selectedChildId);
            
            if (!$selectedChild) {
                $selectedChild = $children->first();
            }

            // Get current academic year and semester (fallback to latest if no active ones)
            $currentAcademicYear = \App\Models\AcademicYear::latest()->first();
            $currentSemester = \App\Models\Semester::latest()->first();

            // Get grades for selected child
            $grades = $this->getChildGrades($selectedChild, $currentAcademicYear, $currentSemester);
            
            // Get attendance for selected child
            $attendance = $this->getChildAttendance($selectedChild, request());
            
            // Get lessons and activities for selected child
            $lessons = $this->getChildLessons($selectedChild, $currentAcademicYear, $currentSemester);
            $activities = $this->getChildActivities($selectedChild, $currentAcademicYear, $currentSemester);
            
            // Get submissions for selected child
            $submissions = $this->getChildSubmissions($selectedChild, $currentAcademicYear, $currentSemester);
            
            // Get performance insights
            $performanceInsights = $this->getPerformanceInsights($selectedChild, $currentAcademicYear, $currentSemester);
            
            // Get attendance statistics
            $attendanceStats = \App\Models\Attendance::where('student_id', $selectedChild->id)
                ->selectRaw('
                    COUNT(*) as total_records,
                    SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count
                ')->first();
            
            // Get enrollments for selected child
            $enrollments = \App\Models\Enrollment::where('student_id', $selectedChild->id)
                ->where('status', 'active')
                ->with(['subject', 'academicYear', 'semester'])
                ->get();
            
            // Get upcoming events for selected child
            $upcomingEvents = \App\Models\CalendarEvent::whereHas('subject', function($q) use ($selectedChild) {
                $q->whereHas('enrollments', function($enrollmentQ) use ($selectedChild) {
                    $enrollmentQ->where('student_id', $selectedChild->id);
                });
            })->where('start_time', '>=', now())
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

            return compact(
                'children',
                'selectedChild',
                'grades',
                'attendance',
                'lessons',
                'activities',
                'submissions',
                'performanceInsights',
                'currentAcademicYear',
                'currentSemester',
                'attendanceStats',
                'enrollments',
                'upcomingEvents'
            );
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Parent Dashboard Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a simple error state
            return [
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
                'error' => 'An error occurred while loading the dashboard.'
            ];
        }
    }

    /**
     * Get child grades for parent dashboard
     */
    private function getChildGrades($child, $academicYear, $semester)
    {
        if (!$academicYear || !$semester) {
            return collect();
        }

        return \App\Models\Grade::with(['subject'])
            ->where('student_id', $child->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get child attendance for parent dashboard
     */
    private function getChildAttendance($child, $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        
        return \App\Models\Attendance::with(['subject'])
            ->where('student_id', $child->id)
            ->whereDate('date', $date)
            ->get();
    }

    /**
     * Get child lessons for parent dashboard
     */
    private function getChildLessons($child, $academicYear, $semester)
    {
        if (!$academicYear || !$semester) {
            return collect();
        }

        // Get subjects the child is enrolled in
        $enrolledSubjectIds = \App\Models\Enrollment::where('student_id', $child->id)
            ->where('status', 'active')
            ->pluck('subject_id');

        return \App\Models\Lesson::with(['subject'])
            ->whereIn('subject_id', $enrolledSubjectIds)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Get child activities for parent dashboard
     */
    private function getChildActivities($child, $academicYear, $semester)
    {
        if (!$academicYear || !$semester) {
            return collect();
        }

        // Get subjects the child is enrolled in
        $enrolledSubjectIds = \App\Models\Enrollment::where('student_id', $child->id)
            ->where('status', 'active')
            ->pluck('subject_id');

        // Get activities that belong to lessons of enrolled subjects
        return \App\Models\Activity::with(['lesson.subject'])
            ->whereHas('lesson', function($query) use ($enrolledSubjectIds, $academicYear, $semester) {
                $query->whereIn('subject_id', $enrolledSubjectIds)
                      ->where('academic_year_id', $academicYear->id)
                      ->where('semester_id', $semester->id);
            })
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
    }

    /**
     * Get child submissions for parent dashboard
     */
    private function getChildSubmissions($child, $academicYear, $semester)
    {
        if (!$academicYear || !$semester) {
            return collect();
        }

        // Get subjects the child is enrolled in
        $enrolledSubjectIds = \App\Models\Enrollment::where('student_id', $child->id)
            ->where('status', 'active')
            ->pluck('subject_id');

        // Get submissions that belong to activities of lessons of enrolled subjects
        return \App\Models\ActivitySubmission::with(['activity.lesson.subject'])
            ->where('student_id', $child->id)
            ->whereHas('activity.lesson', function($query) use ($enrolledSubjectIds, $academicYear, $semester) {
                $query->whereIn('subject_id', $enrolledSubjectIds)
                      ->where('academic_year_id', $academicYear->id)
                      ->where('semester_id', $semester->id);
            })
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Get performance insights for parent dashboard
     */
    private function getPerformanceInsights($child, $academicYear, $semester)
    {
        if (!$academicYear || !$semester) {
            return [];
        }

        // Get GPA for current academic year and semester
        $gpa = \App\Models\StudentGpa::where('student_id', $child->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->first();

        // Get subjects the child is enrolled in for current academic year and semester
        $enrolledSubjectIds = \App\Models\Enrollment::where('student_id', $child->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('semester_id', $semester->id)
            ->where('status', 'active')
            ->pluck('subject_id');

        // Get attendance percentage for enrolled subjects
        $attendanceStats = \App\Models\Attendance::where('student_id', $child->id)
            ->whereIn('subject_id', $enrolledSubjectIds)
            ->selectRaw('
                COUNT(*) as total_records,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count
            ')->first();

        $attendancePercentage = $attendanceStats && $attendanceStats->total_records > 0 
            ? round(($attendanceStats->present_count / $attendanceStats->total_records) * 100, 1)
            : 0;

        return [
            'gpa' => $gpa ? $gpa->gpa : null,
            'attendancePercentage' => $attendancePercentage,
            'academicYear' => $academicYear->name,
            'semester' => $semester->name
        ];
    }

    /**
     * Show the form for editing the authenticated user's profile.
     */
    public function editProfile()
    {
        $user = auth()->user();
        $user = \App\Models\User::find($user->id);
        $student = $user->student;
        $teacher = $user->teacher;
        return view('dashboard.edit_profile', compact('user', 'student', 'teacher'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $user = \App\Models\User::find($user->id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties(['attributes' => $request->only(['name', 'email'])])
            ->log('updated profile');
        // Role-specific updates (future extension)
        if ($user->role_name === \App\Models\User::ROLE_STUDENT && $user->student) {
            // Example: $user->student->admission_id = $request->student_id; (if editable)
            $user->student->save();
        }
        if ($user->role_name === \App\Models\User::ROLE_TEACHER && $user->teacher) {
            // Example: $user->teacher->teacher_id = $request->teacher_id; (if editable)
            $user->teacher->save();
        }
        return redirect()->route('user/profile/page')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $user = \App\Models\User::find($user->id);
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->log('changed password');
        return redirect()->route('user/profile/page')->with('success', 'Password updated successfully.');
    }

    /**
     * Display the current user's activity logs.
     */
    public function activityLog()
    {
        $user = auth()->user();
        $activities = \Spatie\Activitylog\Models\Activity::where('causer_id', $user->id)
            ->where('causer_type', get_class($user))
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('dashboard.activity_log', compact('activities'));
    }
}
