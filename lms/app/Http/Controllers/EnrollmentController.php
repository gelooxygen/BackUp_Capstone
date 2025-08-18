<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrollments = Enrollment::with(['student', 'subject', 'academicYear', 'semester'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('enrollments.index', compact('enrollments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::all();
        $sections = Section::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        
        return view('enrollments.create', compact('subjects', 'sections', 'academicYears', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role_name' => 'required|string|in:Student,Parent,Teacher',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            
            // Student-specific fields
            'admission_id' => 'nullable|string|max:50',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'parent_email' => 'nullable|email',
            'year_level' => 'nullable|string|max:20',
            
            // Teacher-specific fields
            'teacher_id' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
            
            // Enrollment fields
            'subject_id' => 'nullable|exists:subjects,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester_id' => 'nullable|exists:semesters,id',
        ]);

        DB::beginTransaction();
        
        try {
            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role_name' => $request->role_name,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'department' => $request->department,
                'position' => $request->position,
                'date_of_birth' => $request->date_of_birth,
                'join_date' => Carbon::now()->toDayDateTimeString(),
                'status' => 'active',
            ]);

            // Create role-specific profile
            if ($request->role_name === 'Student') {
                $this->createStudentProfile($user, $request);
            } elseif ($request->role_name === 'Teacher') {
                $this->createTeacherProfile($user, $request);
            }

            // Create enrollment if subject and academic info provided
            if ($request->subject_id && $request->academic_year_id && $request->semester_id) {
                $this->createEnrollment($user, $request);
            }

            DB::commit();
            
            Toastr::success('User created successfully!', 'Success');
            return redirect()->route('enrollments.index');
            
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to create user: ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'subject', 'academicYear', 'semester']);
        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enrollment $enrollment)
    {
        $subjects = Subject::all();
        $sections = Section::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        
        return view('enrollments.edit', compact('enrollment', 'subjects', 'sections', 'academicYears', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'status' => 'required|string|in:active,inactive,completed,dropped',
        ]);

        $enrollment->update($request->all());
        
        Toastr::success('Enrollment updated successfully!', 'Success');
        return redirect()->route('enrollments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        
        Toastr::success('Enrollment deleted successfully!', 'Success');
        return redirect()->route('enrollments.index');
    }

    /**
     * Create student profile
     */
    private function createStudentProfile(User $user, Request $request)
    {
        Student::create([
            'user_id' => $user->user_id,
            'first_name' => explode(' ', $user->name)[0] ?? $user->name,
            'last_name' => count(explode(' ', $user->name)) > 1 ? implode(' ', array_slice(explode(' ', $user->name), 1)) : '',
            'email' => $user->email,
            'admission_id' => $request->admission_id,
            'gender' => $request->gender,
            'parent_email' => $request->parent_email,
            'year_level' => $request->year_level,
            'status' => 'active',
        ]);
    }

    /**
     * Create teacher profile
     */
    private function createTeacherProfile(User $user, Request $request)
    {
        Teacher::create([
            'user_id' => $user->user_id,
            'first_name' => explode(' ', $user->name)[0] ?? $user->name,
            'last_name' => count(explode(' ', $user->name)) > 1 ? implode(' ', array_slice(explode(' ', $user->name), 1)) : '',
            'email' => $user->email,
            'teacher_id' => $request->teacher_id,
            'specialization' => $request->specialization,
            'status' => 'active',
        ]);
    }

    /**
     * Create enrollment record
     */
    private function createEnrollment(User $user, Request $request)
    {
        $student = Student::where('user_id', $user->user_id)->first();
        
        if ($student) {
            Enrollment::create([
                'student_id' => $student->id,
                'subject_id' => $request->subject_id,
                'section_id' => $request->section_id,
                'academic_year_id' => $request->academic_year_id,
                'semester_id' => $request->semester_id,
                'status' => 'active',
                'enrollment_date' => Carbon::now(),
            ]);
        }
    }
}
