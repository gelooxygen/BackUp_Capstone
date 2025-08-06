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

    /** teacher dashboard */
    public function teacherDashboardIndex()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            // Instead of redirecting back (which could cause a loop), show an error view
            return view('dashboard.teacher_dashboard', [
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
            ])->with('error', 'Teacher profile not found. Please contact administrator.');
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
        
        return view('dashboard.teacher_dashboard', compact(
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
        ));
    }

    /** teacher classes */
    public function teacherClasses()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $teacher = $user->teacher;
        
        if (!$teacher) {
            // Instead of redirecting back, return the view with empty data
            $teacherSubjects = collect();
            $teacherSections = collect();
            $recentEnrollments = collect();
            $attendanceStats = collect();
            
            return view('teacher.classes', compact(
                'teacher',
                'teacherSubjects',
                'teacherSections',
                'recentEnrollments',
                'attendanceStats'
            ));
        }
        
        // Get teacher's subjects with enrollment counts
        $teacherSubjects = $teacher->subjects()
            ->withCount(['enrollments' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();
        
        // Get sections where teacher is adviser
        $teacherSections = Section::where('adviser_id', $teacher->id)
            ->withCount('students')
            ->get();
        
        // Get recent enrollments for teacher's subjects (only if teacher has subjects)
        $recentEnrollments = collect();
        if ($teacherSubjects->count() > 0) {
            $recentEnrollments = Enrollment::whereIn('subject_id', $teacherSubjects->pluck('id'))
                ->where('status', 'active')
                ->with(['student', 'subject'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }
        
        // Get attendance statistics for teacher's subjects (only if teacher has subjects)
        $attendanceStats = collect();
        if ($teacherSubjects->count() > 0) {
            $attendanceStats = Attendance::whereIn('subject_id', $teacherSubjects->pluck('id'))
                ->selectRaw('
                    subject_id,
                    COUNT(*) as total_records,
                    SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                    SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count
                ')
                ->groupBy('subject_id')
                ->get()
                ->keyBy('subject_id');
        }
        
        return view('teacher.classes', compact(
            'teacher',
            'teacherSubjects',
            'teacherSections',
            'recentEnrollments',
            'attendanceStats'
        ));
    }

    /** student dashboard */
    public function studentDashboardIndex()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            // Try to find student by user_id as fallback
            $student = Student::where('user_id', $user->user_id)->first();
            
            if (!$student) {
                return redirect()->back()->with('error', 'Student profile not found. Please contact administrator.');
            }
        }
        
        // Get student's enrollments with related data
        $enrollments = $student->enrollments()
            ->with(['subject', 'academicYear', 'semester'])
            ->where('status', 'active')
            ->get();
        
        return view('dashboard.student_dashboard', compact('student', 'enrollments'));
    }

    /** admin dashboard */
    public function adminDashboardIndex()
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
            
        return view('dashboard.admin_dashboard', compact(
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
        ));
    }

    /** parent dashboard */
    public function parentDashboardIndex(Request $request)
    {
        return app(\App\Http\Controllers\ParentController::class)->dashboard($request);
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
        
        if ($user->role_name === User::ROLE_ADMIN) {
            return $this->adminDashboardIndex();
        } elseif ($user->role_name === User::ROLE_TEACHER) {
            return $this->teacherDashboardIndex();
        } elseif ($user->role_name === User::ROLE_STUDENT) {
            $student = $user->student;
            
            if (!$student) {
                // Try to find student by user_id as fallback
                $student = Student::where('user_id', $user->user_id)->first();
                
                if (!$student) {
                    return redirect()->back()->with('error', 'Student profile not found. Please contact administrator.');
                }
            }
            
            // Get student's enrollments with related data
            $enrollments = $student->enrollments()
                ->with(['subject', 'academicYear', 'semester'])
                ->where('status', 'active')
                ->get();
            
            return view('dashboard.student_dashboard', compact('student', 'enrollments'));
        } elseif ($user->role_name === User::ROLE_PARENT) {
            return app(\App\Http\Controllers\ParentController::class)->dashboard(request());
        }
        abort(403);
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
