<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
        return view('dashboard.home');
    }

    /** profile user */
    public function userProfile()
    {
        return view('dashboard.profile');
    }

    /** teacher dashboard */
    public function teacherDashboardIndex()
    {
        return view('dashboard.teacher_dashboard');
    }

    /** student dashboard */
    public function studentDashboardIndex()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
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
        return view('dashboard.admin_dashboard');
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
        if ($user->role_name === User::ROLE_ADMIN) {
            return view('dashboard.admin_dashboard');
        } elseif ($user->role_name === User::ROLE_TEACHER) {
            return view('dashboard.teacher_dashboard');
        } elseif ($user->role_name === User::ROLE_STUDENT) {
            return view('dashboard.student_dashboard');
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
