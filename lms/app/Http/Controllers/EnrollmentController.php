<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrollments = \App\Models\Enrollment::with(['student', 'subject', 'academicYear', 'semester'])->orderBy('id', 'desc')->get();
        return view('enrollments.index', compact('enrollments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = \App\Models\Student::orderBy('first_name')->get();
        $subjects = \App\Models\Subject::orderBy('subject_name')->get();
        $academicYears = \App\Models\AcademicYear::orderBy('start_date', 'desc')->get();
        $semesters = \App\Models\Semester::orderBy('name')->get();
        return view('enrollments.create', compact('students', 'subjects', 'academicYears', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => [
                'required',
                'exists:students,id',
                Rule::unique('enrollments')->where(function ($query) use ($request) {
                    return $query->where('subject_id', $request->subject_id)
                        ->where('academic_year_id', $request->academic_year_id)
                        ->where('semester_id', $request->semester_id);
                }),
            ],
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
        ], [
            'student_id.unique' => 'This student is already enrolled in this subject for the selected year and semester.'
        ]);

        Enrollment::create($request->only(['student_id', 'subject_id', 'academic_year_id', 'semester_id']));

        return redirect()->route('enrollments.index')->with('success', 'Enrollment successful!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Enrollment $enrollment)
    {
        $students = \App\Models\Student::orderBy('first_name')->get();
        $subjects = \App\Models\Subject::orderBy('subject_name')->get();
        $academicYears = \App\Models\AcademicYear::orderBy('start_date', 'desc')->get();
        $semesters = \App\Models\Semester::orderBy('name')->get();
        return view('enrollments.edit', compact('enrollment', 'students', 'subjects', 'academicYears', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        //
    }
}
