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

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $subjects = Subject::orderBy('subject_name')->get();
        $subjectId = $request->input('subject_id');
        $month = $request->input('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);
        $days = range(1, $daysInMonth);

        // Get students for the subject or all
        if ($subjectId) {
            $students = Student::whereHas('subjects', function($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            })->orderBy('first_name')->get();
        } else {
            $students = Student::orderBy('first_name')->get();
        }

        // Get attendance records for the month/subject
        $attendanceQuery = Attendance::whereYear('date', $year)->whereMonth('date', $monthNum);
        if ($subjectId) {
            $attendanceQuery->where('subject_id', $subjectId);
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

        return view('attendance.index', compact('subjects', 'students', 'days', 'attendanceMap', 'summary'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subjects = Subject::orderBy('subject_name')->get();
        $subjectId = $request->input('subject_id');
        $date = $request->input('date', now()->toDateString());
        $students = collect();
        $existing = [];
        if ($subjectId) {
            $students = Student::whereHas('subjects', function($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            })->get();
            $existing = Attendance::where('subject_id', $subjectId)
                ->where('date', $date)
                ->pluck('status', 'student_id')->toArray();
        }
        return view('attendance.create', compact('subjects', 'students', 'subjectId', 'date', 'existing'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
        ]);
        $subjectId = $request->subject_id;
        $date = $request->date;
        $teacherId = Auth::user()->teacher->id ?? null;
        foreach ($request->attendance as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                    'teacher_id' => $teacherId,
                ]
            );
        }
        return redirect()->route('attendance.create', ['subject_id' => $subjectId, 'date' => $date])
            ->with('success', 'Attendance saved successfully.');
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
        $subjects = Subject::orderBy('subject_name')->get();
        $subjectId = $request->input('subject_id');
        $month = $request->input('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);
        $days = range(1, $daysInMonth);

        // Get students for the subject or all
        if ($subjectId) {
            $students = Student::whereHas('subjects', function($q) use ($subjectId) {
                $q->where('subjects.id', $subjectId);
            })->orderBy('first_name')->get();
        } else {
            $students = Student::orderBy('first_name')->get();
        }

        // Get attendance records for the month/subject
        $attendanceQuery = Attendance::whereYear('date', $year)->whereMonth('date', $monthNum);
        if ($subjectId) {
            $attendanceQuery->where('subject_id', $subjectId);
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
                $row[] = $status === 'present' ? 'P' : ($status === 'absent' ? 'A' : '-');
                if ($status) {
                    $total++;
                    if ($status === 'present') {
                        $present++;
                    }
                }
            }
            $percentage = $total > 0 ? round(($present / $total) * 100, 2) : null;
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

        $format = $request->input('format');
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
