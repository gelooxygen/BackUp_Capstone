<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AttendanceExport;
use App\Models\User;
use App\Models\Section;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasRole(User::ROLE_TEACHER) && !auth()->user()->hasRole(User::ROLE_ADMIN)) {
                abort(403, 'Only teachers and administrators can manage attendance.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        $subjects = $teacher ? $teacher->subjects : Subject::all();
        $sections = $teacher ? Section::where('adviser_id', $teacher->id)->get() : Section::all();
        
        $subjectId = $request->input('subject_id');
        $sectionId = $request->input('section_id');
        $month = $request->input('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);
        $days = range(1, $daysInMonth);

        // Get students based on section and subject
        $students = collect();
        if ($sectionId) {
            $students = Student::whereHas('sections', function($q) use ($sectionId) {
                $q->where('sections.id', $sectionId);
            });
            if ($subjectId) {
                $students->whereHas('subjects', function($q) use ($subjectId) {
                    $q->where('subjects.id', $subjectId);
                });
            }
            $students = $students->orderBy('first_name')->get();
        }

        // Get attendance records
        $attendanceQuery = Attendance::whereYear('date', $year)->whereMonth('date', $monthNum);
        if ($subjectId) {
            $attendanceQuery->where('subject_id', $subjectId);
        }
        if ($teacher) {
            $attendanceQuery->where('teacher_id', $teacher->id);
        }
        $attendances = $attendanceQuery->get();

        // Build attendance map and summary
        $attendanceMap = [];
        foreach ($attendances as $attendance) {
            $day = (int)date('j', strtotime($attendance->date));
            $attendanceMap[$attendance->student_id][$day] = $attendance->status;
        }

        $summary = [];
        foreach ($students as $student) {
            $present = 0;
            $total = 0;
            foreach ($days as $day) {
                if (isset($attendanceMap[$student->id][$day])) {
                    $total++;
                    if ($attendanceMap[$student->id][$day] === 'present') {
                        $present++;
                    }
                }
            }
            $percentage = $total > 0 ? round(($present / $total) * 100, 2) : null;
            $summary[$student->id] = [
                'present' => $present,
                'total' => $total,
                'percentage' => $percentage,
            ];
        }

        return view('attendance.index', compact('subjects', 'sections', 'students', 'days', 'attendanceMap', 'summary', 'subjectId', 'sectionId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $teacher = auth()->user()->teacher;
        $subjects = $teacher ? $teacher->subjects : Subject::all();
        $sections = $teacher ? Section::where('adviser_id', $teacher->id)->get() : Section::all();
        
        $subjectId = $request->input('subject_id');
        $sectionId = $request->input('section_id');
        $date = $request->input('date', now()->toDateString());
        
        $students = collect();
        $existing = [];
        
        if ($sectionId && $subjectId) {
            // Get students from the selected section who are enrolled in the subject
            $students = Student::whereHas('sections', function($q) use ($sectionId) {
                $q->where('sections.id', $sectionId);
            })->whereHas('subjects', function($q) use ($subjectId) {
            $q->where('subjects.id', $subjectId);
            })->get();

            $existing = Attendance::where('subject_id', $subjectId)
                ->where('date', $date)
                ->pluck('status', 'student_id')
                ->toArray();
        }

        return view('attendance.create', compact('subjects', 'sections', 'students', 'subjectId', 'sectionId', 'date', 'existing'));
    }

    public function studentView(Request $request)
    {
        $student = auth()->user()->student;
        if (!$student) {
            abort(403, 'Only students can view their attendance.');
        }

        $subjects = $student->subjects;
        $month = $request->input('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $query = Attendance::where('student_id', $student->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->with(['subject', 'teacher']);

        if ($subjectId = $request->input('subject_id')) {
            $query->where('subject_id', $subjectId);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        // Calculate summary
        $total = $attendances->count();
        $present = $attendances->where('status', 'present')->count();
        $absent = $total - $present;
        $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        $summary = [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $percentage,
        ];

        return view('attendance.student_view', compact('subjects', 'attendances', 'summary'));
    }

    public function parentView(Request $request)
    {
        $parent = auth()->user();
        if (!$parent->hasRole(User::ROLE_PARENT)) {
            abort(403, 'Only parents can view their children\'s attendance.');
        }

        // Get children (you'll need to implement the relationship between parents and students)
        $children = Student::where('parent_email', $parent->email)->get();
        $subjects = Subject::all();
        $selectedStudent = null;
        $attendances = collect();
        $summary = [];

        if ($studentId = $request->input('student_id')) {
            $selectedStudent = $children->find($studentId);
            if ($selectedStudent) {
                $month = $request->input('month', now()->format('Y-m'));
                $year = substr($month, 0, 4);
                $monthNum = substr($month, 5, 2);

                $query = Attendance::where('student_id', $studentId)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $monthNum)
                    ->with(['subject', 'teacher']);

                if ($subjectId = $request->input('subject_id')) {
                    $query->where('subject_id', $subjectId);
                }

                $attendances = $query->orderBy('date', 'desc')->get();

                // Calculate summary
                $total = $attendances->count();
                $present = $attendances->where('status', 'present')->count();
                $absent = $total - $present;
                $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

                $summary = [
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'percentage' => $percentage,
                ];
            }
        }

        return view('attendance.parent_view', compact('children', 'subjects', 'selectedStudent', 'attendances', 'summary'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:present,absent',
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);

        $teacher = auth()->user()->teacher;
        if (!$teacher) {
            return back()->with('error', 'Only teachers can mark attendance.');
        }

        // Verify teacher has access to this section/subject
        $hasAccess = $teacher->subjects()->where('subjects.id', $request->subject_id)->exists() ||
                    Section::where('id', $request->section_id)
                        ->where('adviser_id', $teacher->id)
                        ->exists();

        if (!$hasAccess && !auth()->user()->hasRole(User::ROLE_ADMIN)) {
            return back()->with('error', 'You do not have permission to mark attendance for this section/subject.');
        }

        foreach ($request->attendance as $studentId => $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?? null,
                    'teacher_id' => $teacher->id,
                ]
            );
        }

        return redirect()->route('attendance.create', [
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'date' => $request->date,
        ])->with('success', 'Attendance saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        // Will show details for a single attendance record
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        // Will show attendance edit form
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        // Will handle attendance update
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        // Will handle attendance deletion
    }

    /**
     * Export attendance summary to PDF or Excel.
     */
    public function export(Request $request)
    {
        $teacher = auth()->user()->teacher;
        if (!$teacher && !auth()->user()->hasRole(User::ROLE_ADMIN)) {
            return back()->with('error', 'You do not have permission to export attendance.');
        }

        $subjects = Subject::orderBy('subject_name')->get();
        $subjectId = $request->input('subject_id');
        $sectionId = $request->input('section_id');
        $month = $request->input('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);
        $days = range(1, $daysInMonth);

        // Get students for the subject/section or all
        $studentsQuery = Student::query();
        
        if ($sectionId) {
            $studentsQuery->whereHas('sections', function($q) use ($sectionId) {
                $q->where('sections.id', $sectionId);
            });
        }
        
        if ($subjectId) {
            $studentsQuery->whereHas('subjects', function($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            });
        }
        
        $students = $studentsQuery->orderBy('first_name')->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students found for the selected criteria.');
        }

        // Get attendance records for the month/subject
        $attendanceQuery = Attendance::whereYear('date', $year)->whereMonth('date', $monthNum);
        if ($subjectId) {
            $attendanceQuery->where('subject_id', $subjectId);
        }
        if ($teacher) {
            $attendanceQuery->where('teacher_id', $teacher->id);
        }
        $attendances = $attendanceQuery->get();

        // Map: [student_id][day] => status
        $attendanceMap = [];
        foreach ($attendances as $attendance) {
            $day = (int)date('j', strtotime($attendance->date));
            $attendanceMap[$attendance->student_id][$day] = $attendance->status;
        }

        // Summary per student
        $summary = [];
        $exportData = [];
        foreach ($students as $student) {
            $present = 0;
            $total = 0;
            $row = [$student->first_name . ' ' . $student->last_name];
            foreach ($days as $day) {
                $status = $attendanceMap[$student->id][$day] ?? null;
                $row[] = $status === 'present' ? 'P' : ($status === 'absent' ? 'A' : ($status === 'late' ? 'L' : '-'));
                if ($status) {
                    $total++;
                    if ($status === 'present') {
                        $present++;
                    }
                }
            }
            $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
            $row[] = $present;
            $row[] = $total;
            $row[] = $percentage;
            $exportData[] = $row;
            $summary[$student->id] = [
                'present' => $present,
                'total' => $total,
                'percentage' => $percentage,
            ];
        }

        $format = $request->input('format', 'excel');
        $filename = 'attendance_summary_' . $month . ($subjectId ? '_subject_' . $subjectId : '') . '.' . ($format === 'excel' ? 'xlsx' : 'pdf');

        if ($format === 'excel') {
            return Excel::download(new AttendanceExport($exportData, $days), $filename);
        } elseif ($format === 'pdf') {
            $pdf = Pdf::loadView('attendance.export_pdf', [
                'students' => $students,
                'days' => $days,
                'attendanceMap' => $attendanceMap,
                'summary' => $summary,
            ]);
            return $pdf->download($filename);
        }
        return back()->with('error', 'Export format not supported.');
    }
}
