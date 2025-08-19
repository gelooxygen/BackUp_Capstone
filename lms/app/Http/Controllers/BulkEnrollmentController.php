<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;

class BulkEnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    /**
     * Show form to assign multiple students to one subject
     */
    public function assignStudentsToSubjectForm()
    {
        $subjects = Subject::orderBy('subject_name')->get();
        $academicYears = AcademicYear::orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        
        return view('bulk-enrollment.assign-students-to-subject', compact('subjects', 'academicYears', 'semesters', 'sections'));
    }

    /**
     * Handle assigning multiple students to one subject
     */
    public function assignStudentsToSubject(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_id' => 'nullable|exists:sections,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        DB::beginTransaction();
        
        try {
            $subject = Subject::findOrFail($request->subject_id);
            $academicYear = AcademicYear::findOrFail($request->academic_year_id);
            $semester = Semester::findOrFail($request->semester_id);
            $section = $request->section_id ? Section::findOrFail($request->section_id) : null;
            
            $enrolledCount = 0;
            $alreadyEnrolledCount = 0;
            $errors = [];

            foreach ($request->student_ids as $studentId) {
                // Check if student is already enrolled in this subject for this academic period
                $existingEnrollment = Enrollment::where([
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                    'academic_year_id' => $request->academic_year_id,
                    'semester_id' => $request->semester_id,
                ])->first();

                if ($existingEnrollment) {
                    $alreadyEnrolledCount++;
                    continue;
                }

                // Create new enrollment
                Enrollment::create([
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                    'academic_year_id' => $request->academic_year_id,
                    'semester_id' => $request->semester_id,
                    'section_id' => $request->section_id,
                    'status' => 'active',
                    'enrollment_date' => now(),
                ]);
                
                $enrolledCount++;
            }

            DB::commit();

            $message = "Successfully enrolled {$enrolledCount} students in {$subject->subject_name}";
            if ($alreadyEnrolledCount > 0) {
                $message .= " ({$alreadyEnrolledCount} students were already enrolled)";
            }

            Toastr::success($message, 'Success');
            return redirect()->route('bulk-enrollment.assign-students-to-subject');

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to assign students: ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }

    /**
     * Show form to assign multiple subjects to one student
     */
    public function assignSubjectsToStudentForm()
    {
        $students = Student::with('user')->orderBy('first_name')->get();
        $subjects = Subject::orderBy('subject_name')->get();
        $academicYears = AcademicYear::orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        
        return view('bulk-enrollment.assign-subjects-to-student', compact('students', 'subjects', 'academicYears', 'semesters', 'sections'));
    }

    /**
     * Handle assigning multiple subjects to one student
     */
    public function assignSubjectsToStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        DB::beginTransaction();
        
        try {
            Log::info('BulkEnrollmentController: Starting assignSubjectsToStudent for student_id: ' . $request->student_id);
            
            $student = Student::with('user')->findOrFail($request->student_id);
            Log::info('BulkEnrollmentController: Found student: ' . ($student->first_name ?? 'N/A') . ' ' . ($student->last_name ?? 'N/A'));
            Log::info('BulkEnrollmentController: Student user relationship: ' . ($student->user ? 'exists' : 'null'));
            
            $academicYear = AcademicYear::findOrFail($request->academic_year_id);
            $semester = Semester::findOrFail($request->semester_id);
            $section = $request->section_id ? Section::findOrFail($request->section_id) : null;
            
            $enrolledCount = 0;
            $alreadyEnrolledCount = 0;

            foreach ($request->subject_ids as $subjectId) {
                // Check if student is already enrolled in this subject for this academic period
                $existingEnrollment = Enrollment::where([
                    'student_id' => $request->student_id,
                    'subject_id' => $subjectId,
                    'academic_year_id' => $request->academic_year_id,
                    'semester_id' => $request->semester_id,
                ])->first();

                if ($existingEnrollment) {
                    $alreadyEnrolledCount++;
                    continue;
                }

                // Create new enrollment
                Enrollment::create([
                    'student_id' => $request->student_id,
                    'subject_id' => $subjectId,
                    'academic_year_id' => $request->academic_year_id,
                    'semester_id' => $request->semester_id,
                    'section_id' => $request->section_id,
                    'status' => 'active',
                    'enrollment_date' => now(),
                ]);
                
                $enrolledCount++;
            }

            DB::commit();

            // Get student name safely
            $studentName = $student->user ? $student->user->name : 
                          (trim($student->first_name . ' ' . $student->last_name) ?: 'Unknown Student');
            
            $message = "Successfully enrolled {$studentName} in {$enrolledCount} subjects";
            if ($alreadyEnrolledCount > 0) {
                $message .= " ({$alreadyEnrolledCount} subjects were already enrolled)";
            }

            Toastr::success($message, 'Success');
            return redirect()->route('bulk-enrollment.assign-subjects-to-student');

        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Failed to assign subjects: ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }

    /**
     * Get students for AJAX request (for subject assignment)
     */
    public function getStudents(Request $request)
    {
        try {
            Log::info('BulkEnrollmentController: getStudents method called');
            
            // Check if user is authenticated
            if (!auth()->check()) {
                Log::warning('BulkEnrollmentController: User not authenticated');
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            // Check if user has admin role
            if (auth()->user()->role_name !== 'Admin') {
                Log::warning('BulkEnrollmentController: User does not have admin role. Role: ' . auth()->user()->role_name);
                return response()->json(['error' => 'Access denied. Admin role required.'], 403);
            }
            
            $query = Student::with('user');
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('admission_id', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('email', 'like', "%{$search}%");
                      });
                });
            }
            
            $students = $query->limit(50)->get();
            
            Log::info('BulkEnrollmentController: Found ' . $students->count() . ' students');
            
            return response()->json($students);
            
        } catch (\Exception $e) {
            Log::error('BulkEnrollmentController: Error in getStudents: ' . $e->getMessage());
            Log::error('BulkEnrollmentController: Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to fetch students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subjects for AJAX request (for student assignment)
     */
    public function getSubjects(Request $request)
    {
        $query = Subject::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('subject_name', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
        }
        
        $subjects = $query->limit(50)->get();
        
        return response()->json($subjects);
    }
    
    /**
     * Test method for debugging
     */
    public function testStudents()
    {
        try {
            $students = Student::all();
            $studentCount = $students->count();
            
            if ($studentCount > 0) {
                $firstStudent = $students->first();
                $userRelationship = $firstStudent->user;
                
                return response()->json([
                    'success' => true,
                    'student_count' => $studentCount,
                    'first_student' => [
                        'id' => $firstStudent->id,
                        'first_name' => $firstStudent->first_name,
                        'last_name' => $firstStudent->last_name,
                        'user_id' => $firstStudent->user_id,
                        'email' => $firstStudent->email,
                        'has_user_relationship' => $userRelationship ? true : false,
                        'user_data' => $userRelationship ? $userRelationship->toArray() : null
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'student_count' => 0,
                    'message' => 'No students found in database'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Show combined bulk enrollment form
     */
    public function combinedBulkEnrollmentForm()
    {
        $subjects = Subject::orderBy('subject_name')->get();
        $students = Student::with('user')->orderBy('first_name')->get();
        $academicYears = AcademicYear::orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        
        return view('bulk-enrollment.combined-bulk-enrollment', compact('subjects', 'students', 'academicYears', 'semesters', 'sections'));
    }
}
