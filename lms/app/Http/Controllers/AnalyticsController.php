<?php

namespace App\Http\Controllers;

use App\Services\AcademicAnalyticsService;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AcademicAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Student Analytics Dashboard
     */
    public function studentDashboard(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $academicYears = AcademicYear::all();
        $semesters = Semester::all();

        $analytics = $this->analyticsService->getStudentAnalytics(
            $student->id, 
            $academicYearId, 
            $semesterId
        );

        return view('analytics.student-dashboard', compact(
            'analytics',
            'academicYears',
            'semesters',
            'academicYearId',
            'semesterId'
        ));
    }

    /**
     * Teacher Analytics Dashboard
     */
    public function teacherDashboard(Request $request)
    {
        if (Auth::user()->role_name !== 'Teacher') {
            abort(403, 'Unauthorized action.');
        }

        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }

        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        $subjects = Subject::all();

        $analytics = $this->analyticsService->getTeacherAnalytics(
            $teacher->id, 
            $academicYearId, 
            $semesterId
        );

        return view('analytics.teacher-dashboard', compact(
            'analytics',
            'academicYears',
            'semesters',
            'subjects',
            'academicYearId',
            'semesterId'
        ));
    }

    /**
     * Admin Analytics Dashboard
     */
    public function adminDashboard(Request $request)
    {
        if (Auth::user()->role_name !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }

        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $academicYears = AcademicYear::all();
        $semesters = Semester::all();
        $sections = Section::all();

        $analytics = $this->analyticsService->getAdminAnalytics($academicYearId, $semesterId);

        return view('analytics.admin-dashboard', compact(
            'analytics',
            'academicYears',
            'semesters',
            'sections',
            'academicYearId',
            'semesterId'
        ));
    }

    /**
     * API endpoint for chart data
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type');
        $role = Auth::user()->role_name;

        switch ($role) {
            case 'Student':
                return $this->getStudentChartData($request, $type);
            case 'Teacher':
                return $this->getTeacherChartData($request, $type);
            case 'Admin':
                return $this->getAdminChartData($request, $type);
            default:
                return response()->json(['error' => 'Invalid role'], 400);
        }
    }

    /**
     * Get student chart data
     */
    private function getStudentChartData(Request $request, $type)
    {
        $student = Auth::user()->student;
        if (!$student) {
            return response()->json(['error' => 'Student profile not found'], 404);
        }

        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $analytics = $this->analyticsService->getStudentAnalytics(
            $student->id, 
            $academicYearId, 
            $semesterId
        );

        switch ($type) {
            case 'grade_trends':
                return response()->json($analytics['grade_trends']);
            case 'attendance_summary':
                return response()->json($analytics['attendance_summary']);
            case 'subject_performance':
                return response()->json($analytics['subject_performance']);
            case 'gpa_trend':
                return response()->json($analytics['gpa_trend']);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    /**
     * Get teacher chart data
     */
    private function getTeacherChartData(Request $request, $type)
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return response()->json(['error' => 'Teacher profile not found'], 404);
        }

        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $analytics = $this->analyticsService->getTeacherAnalytics(
            $teacher->id, 
            $academicYearId, 
            $semesterId
        );

        switch ($type) {
            case 'class_averages':
                return response()->json($analytics['class_averages']);
            case 'attendance_overview':
                return response()->json($analytics['attendance_overview']);
            case 'assessment_breakdown':
                return response()->json($analytics['assessment_breakdown']);
            case 'top_performers':
                return response()->json($analytics['top_performers']);
            case 'low_performers':
                return response()->json($analytics['low_performers']);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    /**
     * Get admin chart data
     */
    private function getAdminChartData(Request $request, $type)
    {
        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $analytics = $this->analyticsService->getAdminAnalytics($academicYearId, $semesterId);

        switch ($type) {
            case 'year_level_comparison':
                return response()->json($analytics['year_level_comparison']);
            case 'section_performance':
                return response()->json($analytics['section_performance']);
            case 'attendance_summary':
                return response()->json($analytics['attendance_summary']);
            case 'pass_fail_rates':
                return response()->json($analytics['pass_fail_rates']);
            case 'subject_performance':
                return response()->json($analytics['subject_performance']);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    /**
     * Export analytics report
     */
    public function exportReport(Request $request)
    {
        $role = Auth::user()->role_name;
        $type = $request->get('type', 'dashboard');
        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');

        $filename = 'analytics_report_' . $role . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($role, $type, $academicYearId, $semesterId) {
            $file = fopen('php://output', 'w');
            
            switch ($role) {
                case 'Student':
                    $this->exportStudentReport($file, $academicYearId, $semesterId);
                    break;
                case 'Teacher':
                    $this->exportTeacherReport($file, $academicYearId, $semesterId);
                    break;
                case 'Admin':
                    $this->exportAdminReport($file, $academicYearId, $semesterId);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export student report
     */
    private function exportStudentReport($file, $academicYearId, $semesterId)
    {
        $student = Auth::user()->student;
        $analytics = $this->analyticsService->getStudentAnalytics(
            $student->id, 
            $academicYearId, 
            $semesterId
        );

        fputcsv($file, ['Student Analytics Report']);
        fputcsv($file, ['Student', $analytics['student']->first_name . ' ' . $analytics['student']->last_name]);
        fputcsv($file, ['']);
        
        fputcsv($file, ['Performance Indicators']);
        fputcsv($file, ['Average Score', $analytics['performance_indicators']['average_score'] . '%']);
        fputcsv($file, ['Total Assignments', $analytics['performance_indicators']['total_assignments']]);
        fputcsv($file, ['Low Grades', $analytics['performance_indicators']['low_grades_count']]);
        fputcsv($file, ['']);
        
        fputcsv($file, ['Subject Performance']);
        fputcsv($file, ['Subject', 'Average Score', 'Assignments', 'Performance Level']);
        
        foreach ($analytics['subject_performance'] as $subject) {
            fputcsv($file, [
                $subject['subject_name'],
                $subject['average_score'] . '%',
                $subject['assignments_count'],
                $subject['performance_level']
            ]);
        }
    }

    /**
     * Export teacher report
     */
    private function exportTeacherReport($file, $academicYearId, $semesterId)
    {
        $teacher = Auth::user()->teacher;
        $analytics = $this->analyticsService->getTeacherAnalytics(
            $teacher->id, 
            $academicYearId, 
            $semesterId
        );

        fputcsv($file, ['Teacher Analytics Report']);
        fputcsv($file, ['Teacher', $analytics['teacher']->full_name]);
        fputcsv($file, ['']);
        
        fputcsv($file, ['Class Averages']);
        fputcsv($file, ['Subject', 'Average Score', 'Students', 'Assignments']);
        
        foreach ($analytics['class_averages'] as $class) {
            fputcsv($file, [
                $class['subject_name'],
                $class['average_score'] . '%',
                $class['students_count'],
                $class['assignments_count']
            ]);
        }
        
        fputcsv($file, ['']);
        fputcsv($file, ['Top Performers']);
        fputcsv($file, ['Student', 'Average Score', 'Assignments']);
        
        foreach ($analytics['top_performers'] as $student) {
            fputcsv($file, [
                $student['student_name'],
                $student['average_score'] . '%',
                $student['assignments_count']
            ]);
        }
    }

    /**
     * Export admin report
     */
    private function exportAdminReport($file, $academicYearId, $semesterId)
    {
        $analytics = $this->analyticsService->getAdminAnalytics($academicYearId, $semesterId);

        fputcsv($file, ['School Analytics Report']);
        fputcsv($file, ['']);
        
        fputcsv($file, ['School Overview']);
        fputcsv($file, ['Total Students', $analytics['school_overview']['total_students']]);
        fputcsv($file, ['Total Teachers', $analytics['school_overview']['total_teachers']]);
        fputcsv($file, ['Average GPA', $analytics['school_overview']['average_gpa']]);
        fputcsv($file, ['Pass Rate', $analytics['school_overview']['pass_rate'] . '%']);
        fputcsv($file, ['']);
        
        fputcsv($file, ['Year Level Comparison']);
        fputcsv($file, ['Grade Level', 'Average GPA', 'Students']);
        
        foreach ($analytics['year_level_comparison'] as $year) {
            fputcsv($file, [
                $year['grade_level'],
                $year['average_gpa'],
                $year['students_count']
            ]);
        }
        
        fputcsv($file, ['']);
        fputcsv($file, ['Section Performance']);
        fputcsv($file, ['Section', 'Grade Level', 'Average GPA', 'Students']);
        
        foreach ($analytics['section_performance'] as $section) {
            fputcsv($file, [
                $section['section_name'],
                $section['grade_level'],
                $section['average_gpa'],
                $section['students_count']
            ]);
        }
    }
} 