<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\SubjectComponent;
use Carbon\Carbon;

class AnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Analytics Data...');

        // Get or create academic year
        $academicYear = AcademicYear::firstOrCreate([
            'name' => '2024-2025'
        ], [
            'start_date' => '2024-06-01',
            'end_date' => '2025-05-31'
        ]);

        // Get or create semester
        $semester = Semester::firstOrCreate([
            'name' => 'First Semester',
            'academic_year_id' => $academicYear->id
        ]);

        // Get existing data
        $students = Student::all();
        $teachers = Teacher::all();
        $subjects = Subject::all();
        $sections = Section::all();

        if ($students->isEmpty() || $teachers->isEmpty() || $subjects->isEmpty() || $sections->isEmpty()) {
            $this->command->error('Please run other seeders first to create basic data.');
            return;
        }

        // Create subject components
        $this->createSubjectComponents($subjects);

        // Create sample grades
        $this->createSampleGrades($students, $subjects, $academicYear, $semester);

        // Create sample attendance
        $this->createSampleAttendance($students, $sections, $academicYear, $semester);

        $this->command->info('Analytics data seeded successfully!');
    }

    /**
     * Create subject components
     */
    private function createSubjectComponents($subjects)
    {
        $components = ['Quiz', 'Assignment', 'Exam', 'Project', 'Participation'];

        foreach ($subjects as $subject) {
            foreach ($components as $componentName) {
                SubjectComponent::firstOrCreate([
                    'name' => $componentName,
                    'subject_id' => $subject->id
                ], [
                    'weight' => rand(10, 30),
                    'description' => $componentName . ' for ' . $subject->subject_name,
                    'is_active' => true
                ]);
            }
        }
    }

    /**
     * Create sample grades
     */
    private function createSampleGrades($students, $subjects, $academicYear, $semester)
    {
        $components = SubjectComponent::all();

        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                $subjectComponents = $components->where('subject_id', $subject->id);
                
                foreach ($subjectComponents as $component) {
                    // Create 3-5 grades per component
                    $numGrades = rand(3, 5);
                    
                    for ($i = 0; $i < $numGrades; $i++) {
                        $score = $this->generateRealisticScore();
                        
                        Grade::firstOrCreate([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'component_id' => $component->id,
                            'academic_year_id' => $academicYear->id,
                            'semester_id' => $semester->id,
                            'score' => $score,
                            'max_score' => 100,
                            'percentage' => $score,
                            'created_at' => Carbon::now()->subDays(rand(1, 90))
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Create sample attendance
     */
    private function createSampleAttendance($students, $sections, $academicYear, $semester)
    {
        $statuses = ['present', 'absent', 'late'];
        $weights = [0.8, 0.15, 0.05]; // 80% present, 15% absent, 5% late

        foreach ($students as $student) {
            $section = $sections->random();
            
            // Create attendance for the last 60 school days
            for ($i = 60; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                $status = $this->weightedRandomChoice($statuses, $weights);
                
                Attendance::firstOrCreate([
                    'student_id' => $student->id,
                    'section_id' => $section->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'created_at' => $date
                ]);
            }
        }
    }

    /**
     * Generate realistic score based on performance distribution
     */
    private function generateRealisticScore()
    {
        $rand = rand(1, 100);
        
        if ($rand <= 20) {
            // 20% chance of excellent scores (90-100)
            return rand(90, 100);
        } elseif ($rand <= 50) {
            // 30% chance of good scores (80-89)
            return rand(80, 89);
        } elseif ($rand <= 75) {
            // 25% chance of average scores (70-79)
            return rand(70, 79);
        } elseif ($rand <= 90) {
            // 15% chance of below average scores (60-69)
            return rand(60, 69);
        } else {
            // 10% chance of low scores (50-59)
            return rand(50, 59);
        }
    }

    /**
     * Weighted random choice
     */
    private function weightedRandomChoice($items, $weights)
    {
        $rand = rand(1, 100);
        $cumulativeWeight = 0;
        
        for ($i = 0; $i < count($items); $i++) {
            $cumulativeWeight += $weights[$i] * 100;
            if ($rand <= $cumulativeWeight) {
                return $items[$i];
            }
        }
        
        return $items[0]; // Fallback
    }
} 