<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectComponent;
use App\Models\WeightSetting;
use App\Models\StudentGpa;
use App\Models\GradeAlert;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Section;
use App\Exports\GradesExport;
use App\Exports\GpaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GradingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Grade Entry Form
    public function gradeEntryForm(Request $request)
    {
        $subjects = Subject::all();
        $components = SubjectComponent::where('is_active', true)->get();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        $sections = Section::all();

        $selectedSubject = $request->get('subject_id');
        $selectedSection = $request->get('section_id');
        $selectedAcademicYear = $request->get('academic_year_id');
        $selectedSemester = $request->get('semester_id');

        $students = collect();
        if ($selectedSubject && $selectedSection) {
            $students = Student::whereHas('sections', function($query) use ($selectedSection) {
                $query->where('section_id', $selectedSection);
            })->whereHas('subjects', function($query) use ($selectedSubject) {
                $query->where('subject_id', $selectedSubject);
            })->get();
        }

        return view('grading.grade-entry', compact(
            'subjects', 'components', 'academicYears', 'semesters', 'sections',
            'students', 'selectedSubject', 'selectedSection', 'selectedAcademicYear', 'selectedSemester'
        ));
    }

    // Store Grades
    public function storeGrades(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'component_id' => 'required|exists:subject_components,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.score' => 'nullable|numeric|min:0',
            'grades.*.max_score' => 'required|numeric|min:1',
        ]);

        $teacherId = Auth::user()->teacher->id ?? Auth::id();

        DB::beginTransaction();
        try {
            foreach ($request->grades as $gradeData) {
                if (isset($gradeData['score']) && $gradeData['score'] !== '') {
                    Grade::updateOrCreate(
                        [
                            'student_id' => $gradeData['student_id'],
                            'subject_id' => $request->subject_id,
                            'component_id' => $request->component_id,
                            'academic_year_id' => $request->academic_year_id,
                            'semester_id' => $request->semester_id,
                        ],
                        [
                            'teacher_id' => $teacherId,
                            'score' => $gradeData['score'],
                            'max_score' => $gradeData['max_score'],
                            'remarks' => $gradeData['remarks'] ?? null,
                        ]
                    );
                }
            }

            // Calculate GPA for affected students
            $this->calculateGpaForStudents(
                collect($request->grades)->pluck('student_id')->unique(),
                $request->academic_year_id,
                $request->semester_id
            );

            // Check for grade alerts
            $this->checkGradeAlerts($request->subject_id, $request->academic_year_id, $request->semester_id);

            DB::commit();
            return redirect()->back()->with('success', 'Grades saved successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error saving grades: ' . $e->getMessage());
        }
    }

    // GPA and Ranking View
    public function gpaRanking(Request $request)
    {
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        $sections = Section::all();

        $selectedAcademicYear = $request->get('academic_year_id', AcademicYear::latest()->first()?->id);
        $selectedSemester = $request->get('semester_id', Semester::latest()->first()?->id);
        $selectedSection = $request->get('section_id');

        $query = StudentGpa::with(['student', 'academicYear', 'semester'])
            ->where('academic_year_id', $selectedAcademicYear)
            ->where('semester_id', $selectedSemester);

        if ($selectedSection) {
            $query->whereHas('student.sections', function($q) use ($selectedSection) {
                $q->where('section_id', $selectedSection);
            });
        }

        $gpaRecords = $query->orderBy('gpa', 'desc')->get();

        // Update rankings
        $this->updateRankings($selectedAcademicYear, $selectedSemester, $selectedSection);

        return view('grading.gpa-ranking', compact(
            'gpaRecords', 'academicYears', 'semesters', 'sections',
            'selectedAcademicYear', 'selectedSemester', 'selectedSection'
        ));
    }

    // Performance Analytics
    public function performanceAnalytics(Request $request)
    {
        $studentId = $request->get('student_id');
        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $students = Student::all();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        $performanceData = null;
        $trendData = null;
        $alerts = null;

        if ($studentId) {
            $student = Student::find($studentId);
            $performanceData = $this->getStudentPerformanceData($student, $academicYearId, $semesterId);
            $trendData = $this->getPerformanceTrends($student, $academicYearId);
            $alerts = $student->getActiveAlerts();
        }

        return view('grading.performance-analytics', compact(
            'students', 'academicYears', 'semesters', 'performanceData', 'trendData', 'alerts',
            'studentId', 'academicYearId', 'semesterId'
        ));
    }

    // Export Grades
    public function exportGrades(Request $request)
    {
        $format = $request->get('format', 'excel');
        $subjectId = $request->get('subject_id');
        $sectionId = $request->get('section_id');
        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $fileName = 'grades_' . date('Y-m-d_H-i-s');

        if ($format === 'pdf') {
            $grades = $this->getGradesForExport($subjectId, $sectionId, $academicYearId, $semesterId);
            $pdf = PDF::loadView('exports.grades-pdf', compact('grades'));
            return $pdf->download($fileName . '.pdf');
        } else {
            return Excel::download(new GradesExport($subjectId, $sectionId, $academicYearId, $semesterId), $fileName . '.xlsx');
        }
    }

    // Export GPA Report
    public function exportGpa(Request $request)
    {
        $format = $request->get('format', 'excel');
        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');
        $sectionId = $request->get('section_id');

        $fileName = 'gpa_report_' . date('Y-m-d_H-i-s');

        if ($format === 'pdf') {
            $gpaRecords = $this->getGpaForExport($academicYearId, $semesterId, $sectionId);
            $pdf = PDF::loadView('exports.gpa-pdf', compact('gpaRecords'));
            return $pdf->download($fileName . '.pdf');
        } else {
            return Excel::download(new GpaExport($academicYearId, $semesterId, $sectionId), $fileName . '.xlsx');
        }
    }

    // Weight Settings Management
    public function weightSettings(Request $request)
    {
        $subjects = Subject::all();
        $components = SubjectComponent::where('is_active', true)->get();
        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        $selectedSubject = $request->get('subject_id');
        $selectedAcademicYear = $request->get('academic_year_id');
        $selectedSemester = $request->get('semester_id');

        $weightSettings = collect();
        if ($selectedSubject) {
            $weightSettings = WeightSetting::where('subject_id', $selectedSubject)
                ->where('academic_year_id', $selectedAcademicYear)
                ->where('semester_id', $selectedSemester)
                ->with(['component'])
                ->get();
        }

        return view('grading.weight-settings', compact(
            'subjects', 'components', 'academicYears', 'semesters',
            'weightSettings', 'selectedSubject', 'selectedAcademicYear', 'selectedSemester'
        ));
    }

    // Store Weight Settings
    public function storeWeightSettings(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|exists:semesters,id',
            'weights' => 'required|array',
            'weights.*.component_id' => 'required|exists:subject_components,id',
            'weights.*.weight' => 'required|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing weights for this subject and period
            WeightSetting::where('subject_id', $request->subject_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('semester_id', $request->semester_id)
                ->delete();

            // Insert new weights
            foreach ($request->weights as $weightData) {
                WeightSetting::create([
                    'subject_id' => $request->subject_id,
                    'component_id' => $weightData['component_id'],
                    'weight' => $weightData['weight'],
                    'academic_year_id' => $request->academic_year_id,
                    'semester_id' => $request->semester_id,
                    'is_active' => true,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Weight settings saved successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error saving weight settings: ' . $e->getMessage());
        }
    }

    // Grade Alerts Management
    public function gradeAlerts(Request $request)
    {
        $alerts = GradeAlert::with(['student', 'subject', 'academicYear', 'semester'])
            ->where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('grading.grade-alerts', compact('alerts'));
    }

    // Resolve Alert
    public function resolveAlert(Request $request, $alertId)
    {
        $alert = GradeAlert::findOrFail($alertId);
        $alert->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Alert resolved successfully!');
    }

    // Private helper methods

    private function calculateGpaForStudents($studentIds, $academicYearId, $semesterId)
    {
        foreach ($studentIds as $studentId) {
            $this->calculateStudentGpa($studentId, $academicYearId, $semesterId);
        }
    }

    private function calculateStudentGpa($studentId, $academicYearId, $semesterId)
    {
        $grades = Grade::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->with(['subject', 'component'])
            ->get();

        $totalGradePoints = 0;
        $totalUnits = 0;

        foreach ($grades as $grade) {
            if ($grade->percentage !== null) {
                $gradePoints = $this->percentageToGradePoints($grade->percentage);
                $totalGradePoints += $gradePoints;
                $totalUnits += 1; // Assuming 1 unit per subject
            }
        }

        $gpa = $totalUnits > 0 ? $totalGradePoints / $totalUnits : 0;

        StudentGpa::updateOrCreate(
            [
                'student_id' => $studentId,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
            ],
            [
                'gpa' => round($gpa, 2),
                'total_units' => $totalUnits,
                'total_grade_points' => $totalGradePoints,
            ]
        );
    }

    private function percentageToGradePoints($percentage)
    {
        if ($percentage >= 90) return 4.0;
        if ($percentage >= 85) return 3.7;
        if ($percentage >= 80) return 3.3;
        if ($percentage >= 75) return 3.0;
        if ($percentage >= 70) return 2.7;
        if ($percentage >= 65) return 2.3;
        if ($percentage >= 60) return 2.0;
        if ($percentage >= 55) return 1.7;
        if ($percentage >= 50) return 1.3;
        if ($percentage >= 45) return 1.0;
        return 0.0;
    }

    private function updateRankings($academicYearId, $semesterId, $sectionId = null)
    {
        $query = StudentGpa::where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId);

        if ($sectionId) {
            $query->whereHas('student.sections', function($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            });
        }

        $gpaRecords = $query->orderBy('gpa', 'desc')->get();

        foreach ($gpaRecords as $index => $record) {
            $record->update(['rank' => $index + 1]);
        }
    }

    private function checkGradeAlerts($subjectId, $academicYearId, $semesterId)
    {
        $lowGradeThreshold = 75; // Configurable threshold

        $lowGrades = Grade::where('subject_id', $subjectId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->where('percentage', '<', $lowGradeThreshold)
            ->with(['student'])
            ->get();

        foreach ($lowGrades as $grade) {
            GradeAlert::firstOrCreate(
                [
                    'student_id' => $grade->student_id,
                    'subject_id' => $subjectId,
                    'alert_type' => GradeAlert::TYPE_LOW_GRADE,
                    'academic_year_id' => $academicYearId,
                    'semester_id' => $semesterId,
                ],
                [
                    'message' => "Low grade in {$grade->subject->subject_name}: {$grade->percentage}%",
                    'threshold_value' => $lowGradeThreshold,
                    'current_value' => $grade->percentage,
                ]
            );
        }
    }

    private function getStudentPerformanceData($student, $academicYearId, $semesterId)
    {
        return Grade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->with(['subject', 'component'])
            ->get()
            ->groupBy('subject_id');
    }

    private function getPerformanceTrends($student, $academicYearId)
    {
        return Grade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->with(['subject', 'semester'])
            ->get()
            ->groupBy(['subject_id', 'semester_id']);
    }

    private function getGradesForExport($subjectId, $sectionId, $academicYearId, $semesterId)
    {
        $query = Grade::with(['student', 'subject', 'component', 'teacher'])
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if ($sectionId) {
            $query->whereHas('student.sections', function($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            });
        }

        return $query->get();
    }

    private function getGpaForExport($academicYearId, $semesterId, $sectionId)
    {
        $query = StudentGpa::with(['student', 'academicYear', 'semester'])
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId);

        if ($sectionId) {
            $query->whereHas('student.sections', function($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            });
        }

        return $query->orderBy('gpa', 'desc')->get();
    }
} 