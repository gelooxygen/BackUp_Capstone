<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Subject;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\ActivitySubmission;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AcademicAnalyticsService
{
    /**
     * Get student analytics data
     */
    public function getStudentAnalytics($studentId, $academicYearId = null, $semesterId = null)
    {
        $student = Student::findOrFail($studentId);
        
        return [
            'student' => $student,
            'grade_trends' => $this->getStudentGradeTrends($studentId, $academicYearId, $semesterId),
            'attendance_summary' => $this->getStudentAttendanceSummary($studentId, $academicYearId, $semesterId),
            'performance_indicators' => $this->getStudentPerformanceIndicators($studentId, $academicYearId, $semesterId),
            'subject_performance' => $this->getStudentSubjectPerformance($studentId, $academicYearId, $semesterId),
            'recent_activities' => $this->getStudentRecentActivities($studentId),
            'gpa_trend' => $this->getStudentGpaTrend($studentId, $academicYearId, $semesterId)
        ];
    }

    /**
     * Get teacher analytics data
     */
    public function getTeacherAnalytics($teacherId, $academicYearId = null, $semesterId = null)
    {
        $teacher = Teacher::findOrFail($teacherId);
        
        return [
            'teacher' => $teacher,
            'class_averages' => $this->getTeacherClassAverages($teacherId, $academicYearId, $semesterId),
            'top_performers' => $this->getTeacherTopPerformers($teacherId, $academicYearId, $semesterId),
            'low_performers' => $this->getTeacherLowPerformers($teacherId, $academicYearId, $semesterId),
            'attendance_overview' => $this->getTeacherAttendanceOverview($teacherId, $academicYearId, $semesterId),
            'assessment_breakdown' => $this->getTeacherAssessmentBreakdown($teacherId, $academicYearId, $semesterId),
            'class_performance_trends' => $this->getTeacherClassPerformanceTrends($teacherId, $academicYearId, $semesterId)
        ];
    }

    /**
     * Get admin analytics data
     */
    public function getAdminAnalytics($academicYearId = null, $semesterId = null)
    {
        return [
            'school_overview' => $this->getSchoolOverview($academicYearId, $semesterId),
            'year_level_comparison' => $this->getYearLevelComparison($academicYearId, $semesterId),
            'section_performance' => $this->getSectionPerformance($academicYearId, $semesterId),
            'attendance_summary' => $this->getSchoolAttendanceSummary($academicYearId, $semesterId),
            'pass_fail_rates' => $this->getPassFailRates($academicYearId, $semesterId),
            'subject_performance' => $this->getSchoolSubjectPerformance($academicYearId, $semesterId)
        ];
    }

    /**
     * Get student grade trends
     */
    private function getStudentGradeTrends($studentId, $academicYearId = null, $semesterId = null)
    {
        $query = Grade::where('student_id', $studentId)
            ->with(['subject', 'academicYear', 'semester']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->orderBy('created_at')->get();

        $trends = [];
        foreach ($grades as $grade) {
            $period = $grade->academicYear->name . ' - ' . $grade->semester->name;
            $trends[] = [
                'period' => $period,
                'subject' => $grade->subject->subject_name,
                'score' => $grade->percentage,
                'date' => $grade->created_at->format('Y-m-d')
            ];
        }

        return $trends;
    }

    /**
     * Get student attendance summary
     */
    private function getStudentAttendanceSummary($studentId, $academicYearId = null, $semesterId = null)
    {
        $query = Attendance::where('student_id', $studentId);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $attendance = $query->get();

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthAttendance = $attendance->filter(function($record) use ($month) {
                return $record->date->month === $month;
            });

            $totalDays = $monthAttendance->count();
            $presentDays = $monthAttendance->where('status', 'present')->count();
            $percentage = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

            $monthlyData[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'percentage' => round($percentage, 2)
            ];
        }

        return [
            'monthly_data' => $monthlyData,
            'overall_percentage' => $this->calculateOverallAttendancePercentage($attendance),
            'total_days' => $attendance->count(),
            'present_days' => $attendance->where('status', 'present')->count()
        ];
    }

    /**
     * Get student performance indicators
     */
    private function getStudentPerformanceIndicators($studentId, $academicYearId = null, $semesterId = null)
    {
        $query = Grade::where('student_id', $studentId);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        $averageScore = $grades->avg('percentage') ?? 0;
        $lowGrades = $grades->where('percentage', '<', 75)->count();
        $excellentGrades = $grades->where('percentage', '>=', 90)->count();

        return [
            'average_score' => round($averageScore, 2),
            'low_grades_count' => $lowGrades,
            'excellent_grades_count' => $excellentGrades,
            'total_assignments' => $grades->count(),
            'performance_level' => $this->getPerformanceLevel($averageScore),
            'alerts' => $this->generateStudentAlerts($grades, $studentId)
        ];
    }

    /**
     * Get student subject performance
     */
    private function getStudentSubjectPerformance($studentId, $academicYearId = null, $semesterId = null)
    {
        $query = Grade::where('student_id', $studentId)
            ->with(['subject']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        $subjectPerformance = [];
        foreach ($grades->groupBy('subject_id') as $subjectId => $subjectGrades) {
            $subject = $subjectGrades->first()->subject;
            $averageScore = $subjectGrades->avg('percentage');
            
            $subjectPerformance[] = [
                'subject_name' => $subject->subject_name,
                'average_score' => round($averageScore, 2),
                'assignments_count' => $subjectGrades->count(),
                'performance_level' => $this->getPerformanceLevel($averageScore)
            ];
        }

        return $subjectPerformance;
    }

    /**
     * Get student GPA trend
     */
    private function getStudentGpaTrend($studentId, $academicYearId = null, $semesterId = null)
    {
        $query = Grade::where('student_id', $studentId)
            ->with(['academicYear', 'semester']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->orderBy('created_at')->get();

        $gpaTrend = [];
        foreach ($grades->groupBy(function($grade) {
            return $grade->academicYear->name . ' - ' . $grade->semester->name;
        }) as $period => $periodGrades) {
            $gpa = $this->calculateGPA($periodGrades);
            $gpaTrend[] = [
                'period' => $period,
                'gpa' => round($gpa, 2),
                'date' => $periodGrades->first()->created_at->format('Y-m-d')
            ];
        }

        return $gpaTrend;
    }

    /**
     * Get teacher class averages
     */
    private function getTeacherClassAverages($teacherId, $academicYearId = null, $semesterId = null)
    {
        $query = Grade::whereHas('subject.teachers', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->with(['subject', 'student']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        $classAverages = [];
        foreach ($grades->groupBy('subject_id') as $subjectId => $subjectGrades) {
            $subject = $subjectGrades->first()->subject;
            $averageScore = $subjectGrades->avg('percentage');
            
            $classAverages[] = [
                'subject_name' => $subject->subject_name,
                'average_score' => round($averageScore, 2),
                'students_count' => $subjectGrades->groupBy('student_id')->count(),
                'assignments_count' => $subjectGrades->count()
            ];
        }

        return $classAverages;
    }

    /**
     * Get teacher top performers
     */
    private function getTeacherTopPerformers($teacherId, $academicYearId = null, $semesterId = null)
    {
        $query = Grade::whereHas('subject.teachers', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->with(['subject', 'student']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        $studentAverages = [];
        foreach ($grades->groupBy('student_id') as $studentId => $studentGrades) {
            $student = $studentGrades->first()->student;
            $averageScore = $studentGrades->avg('percentage');
            
            $studentAverages[] = [
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'average_score' => round($averageScore, 2),
                'assignments_count' => $studentGrades->count()
            ];
        }

        return collect($studentAverages)->sortByDesc('average_score')->take(10)->values();
    }

    /**
     * Get teacher low performers
     */
    private function getTeacherLowPerformers($teacherId, $academicYearId = null, $semesterId = null)
    {
        $query = Grade::whereHas('subject.teachers', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->with(['subject', 'student']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        $studentAverages = [];
        foreach ($grades->groupBy('student_id') as $studentId => $studentGrades) {
            $student = $studentGrades->first()->student;
            $averageScore = $studentGrades->avg('percentage');
            
            if ($averageScore < 75) { // Only include low performers
                $studentAverages[] = [
                    'student_name' => $student->first_name . ' ' . $student->last_name,
                    'average_score' => round($averageScore, 2),
                    'assignments_count' => $studentGrades->count()
                ];
            }
        }

        return collect($studentAverages)->sortBy('average_score')->take(10)->values();
    }

    /**
     * Get teacher attendance overview
     */
    private function getTeacherAttendanceOverview($teacherId, $academicYearId = null, $semesterId = null)
    {
        // Get sections taught by the teacher
        $sections = Section::where('adviser_id', $teacherId)->pluck('id');

        $query = Attendance::whereIn('section_id', $sections);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $attendance = $query->get();

        $sectionAttendance = [];
        foreach ($sections as $sectionId) {
            $section = Section::find($sectionId);
            $sectionRecords = $attendance->where('section_id', $sectionId);
            
            $totalDays = $sectionRecords->count();
            $presentDays = $sectionRecords->where('status', 'present')->count();
            $percentage = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

            $sectionAttendance[] = [
                'section_name' => $section->name,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'percentage' => round($percentage, 2)
            ];
        }

        return $sectionAttendance;
    }

    /**
     * Get teacher assessment breakdown
     */
    private function getTeacherAssessmentBreakdown($teacherId, $academicYearId = null, $semesterId = null)
    {
        $query = Grade::whereHas('subject.teachers', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->with(['component']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        $assessmentBreakdown = [];
        foreach ($grades->groupBy('component_id') as $componentId => $componentGrades) {
            $component = $componentGrades->first()->component;
            $averageScore = $componentGrades->avg('percentage');
            
            $assessmentBreakdown[] = [
                'assessment_type' => $component->name,
                'average_score' => round($averageScore, 2),
                'assignments_count' => $componentGrades->count()
            ];
        }

        return $assessmentBreakdown;
    }

    /**
     * Get school overview
     */
    private function getSchoolOverview($academicYearId = null, $semesterId = null)
    {
        $query = Grade::query();

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        return [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_subjects' => Subject::count(),
            'total_sections' => Section::count(),
            'average_gpa' => round($this->calculateOverallGPA($grades), 2),
            'pass_rate' => round($this->calculatePassRate($grades), 2)
        ];
    }

    /**
     * Get year level comparison
     */
    private function getYearLevelComparison($academicYearId = null, $semesterId = null)
    {
        $sections = Section::all();
        $yearLevelData = [];

        foreach ($sections->groupBy('grade_level') as $gradeLevel => $gradeSections) {
            $sectionIds = $gradeSections->pluck('id');
            
            $query = Grade::whereHas('student.sections', function($q) use ($sectionIds) {
                $q->whereIn('section_id', $sectionIds);
            });

            if ($academicYearId) $query->where('academic_year_id', $academicYearId);
            if ($semesterId) $query->where('semester_id', $semesterId);

            $grades = $query->get();
            $averageGPA = $this->calculateOverallGPA($grades);

            $yearLevelData[] = [
                'grade_level' => $gradeLevel,
                'average_gpa' => round($averageGPA, 2),
                'students_count' => $grades->groupBy('student_id')->count()
            ];
        }

        return $yearLevelData;
    }

    /**
     * Get section performance
     */
    private function getSectionPerformance($academicYearId = null, $semesterId = null)
    {
        $sections = Section::all();
        $sectionData = [];

        foreach ($sections as $section) {
            $query = Grade::whereHas('student.sections', function($q) use ($section) {
                $q->where('section_id', $section->id);
            });

            if ($academicYearId) $query->where('academic_year_id', $academicYearId);
            if ($semesterId) $query->where('semester_id', $semesterId);

            $grades = $query->get();
            $averageGPA = $this->calculateOverallGPA($grades);

            $sectionData[] = [
                'section_name' => $section->name,
                'grade_level' => $section->grade_level,
                'average_gpa' => round($averageGPA, 2),
                'students_count' => $grades->groupBy('student_id')->count()
            ];
        }

        return $sectionData;
    }

    /**
     * Get school attendance summary
     */
    private function getSchoolAttendanceSummary($academicYearId = null, $semesterId = null)
    {
        $query = Attendance::query();

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $attendance = $query->get();

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthAttendance = $attendance->filter(function($record) use ($month) {
                return $record->date->month === $month;
            });

            $totalDays = $monthAttendance->count();
            $presentDays = $monthAttendance->where('status', 'present')->count();
            $percentage = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

            $monthlyData[] = [
                'month' => date('F', mktime(0, 0, 0, $month, 1)),
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'percentage' => round($percentage, 2)
            ];
        }

        return [
            'monthly_data' => $monthlyData,
            'overall_percentage' => $this->calculateOverallAttendancePercentage($attendance)
        ];
    }

    /**
     * Get pass/fail rates
     */
    private function getPassFailRates($academicYearId = null, $semesterId = null)
    {
        $query = Grade::query();

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        $totalGrades = $grades->count();
        $passingGrades = $grades->where('percentage', '>=', 75)->count();
        $failingGrades = $totalGrades - $passingGrades;

        return [
            'pass_rate' => $totalGrades > 0 ? round(($passingGrades / $totalGrades) * 100, 2) : 0,
            'fail_rate' => $totalGrades > 0 ? round(($failingGrades / $totalGrades) * 100, 2) : 0,
            'total_grades' => $totalGrades,
            'passing_grades' => $passingGrades,
            'failing_grades' => $failingGrades
        ];
    }

    /**
     * Get school subject performance
     */
    private function getSchoolSubjectPerformance($academicYearId = null, $semesterId = null)
    {
        $query = Grade::with(['subject']);

        if ($academicYearId) $query->where('academic_year_id', $academicYearId);
        if ($semesterId) $query->where('semester_id', $semesterId);

        $grades = $query->get();

        $subjectPerformance = [];
        foreach ($grades->groupBy('subject_id') as $subjectId => $subjectGrades) {
            $subject = $subjectGrades->first()->subject;
            $averageScore = $subjectGrades->avg('percentage');
            
            $subjectPerformance[] = [
                'subject_name' => $subject->subject_name,
                'average_score' => round($averageScore, 2),
                'students_count' => $subjectGrades->groupBy('student_id')->count(),
                'assignments_count' => $subjectGrades->count()
            ];
        }

        return $subjectPerformance;
    }

    /**
     * Helper methods
     */
    private function calculateOverallAttendancePercentage($attendance)
    {
        $totalDays = $attendance->count();
        $presentDays = $attendance->where('status', 'present')->count();
        
        return $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
    }

    private function getPerformanceLevel($score)
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 80) return 'Good';
        if ($score >= 70) return 'Average';
        if ($score >= 60) return 'Below Average';
        return 'Poor';
    }

    private function calculateGPA($grades)
    {
        if ($grades->isEmpty()) return 0;
        
        $totalPoints = 0;
        $totalCredits = 0;
        
        foreach ($grades as $grade) {
            $points = $this->getGradePoints($grade->percentage);
            $totalPoints += $points;
            $totalCredits += 1; // Assuming 1 credit per assignment
        }
        
        return $totalCredits > 0 ? $totalPoints / $totalCredits : 0;
    }

    private function getGradePoints($percentage)
    {
        if ($percentage >= 90) return 4.0;
        if ($percentage >= 85) return 3.5;
        if ($percentage >= 80) return 3.0;
        if ($percentage >= 75) return 2.5;
        if ($percentage >= 70) return 2.0;
        if ($percentage >= 65) return 1.5;
        if ($percentage >= 60) return 1.0;
        return 0.0;
    }

    private function calculateOverallGPA($grades)
    {
        return $this->calculateGPA($grades);
    }

    private function calculatePassRate($grades)
    {
        if ($grades->isEmpty()) return 0;
        
        $passingGrades = $grades->where('percentage', '>=', 75)->count();
        return ($passingGrades / $grades->count()) * 100;
    }

    private function generateStudentAlerts($grades, $studentId)
    {
        $alerts = [];
        
        $lowGrades = $grades->where('percentage', '<', 75);
        if ($lowGrades->count() >= 3) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Multiple low grades detected. Consider seeking academic support.'
            ];
        }
        
        $recentLowGrades = $grades->where('percentage', '<', 75)
            ->where('created_at', '>=', Carbon::now()->subDays(30));
        if ($recentLowGrades->count() > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Recent low grades detected. Immediate attention required.'
            ];
        }
        
        return $alerts;
    }

    private function getStudentRecentActivities($studentId)
    {
        return ActivitySubmission::where('student_id', $studentId)
            ->with(['activity.lesson'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }
} 