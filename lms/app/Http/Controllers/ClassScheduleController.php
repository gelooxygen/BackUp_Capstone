<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $studentId = $request->get('student_id');
        
        // If no student_id provided, try to get current user's student record
        if (!$studentId && Auth::user()->role_name === 'Student') {
            $student = Auth::user()->student;
            if ($student) {
                $studentId = $student->id;
            }
        }

        if (!$studentId) {
            return redirect()->back()->with('error', 'Student not found');
        }

        $view = $request->get('view', 'week');
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now();

        $student = Student::with('sections')->find($studentId);
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        if ($student->sections->isEmpty()) {
            return redirect()->back()->with('error', 'Student is not assigned to any section');
        }

        if ($request->ajax()) {
            return $this->getScheduleData($studentId, $view, $startDate);
        }

        $weeklySchedule = ClassSchedule::getWeeklySchedule($studentId, $startDate);
        $todaySchedule = ClassSchedule::getTodaySchedule($studentId);
        $nextDaysSchedule = ClassSchedule::getNextDaysSchedule($studentId, 7);

        return view('schedule.index', compact('student', 'weeklySchedule', 'todaySchedule', 'nextDaysSchedule', 'view', 'startDate'));
    }

    /**
     * Get schedule data for AJAX requests
     */
    private function getScheduleData($studentId, $view, $startDate)
    {
        switch ($view) {
            case 'week':
                $schedules = ClassSchedule::getWeeklySchedule($studentId, $startDate);
                return response()->json($this->formatWeeklyData($schedules, $startDate));
            
            case 'today':
                $schedules = ClassSchedule::getTodaySchedule($studentId);
                return response()->json($this->formatTodayData($schedules));
            
            case 'next_days':
                $schedules = ClassSchedule::getNextDaysSchedule($studentId, 7);
                return response()->json($this->formatNextDaysData($schedules));
            
            default:
                $schedules = ClassSchedule::getWeeklySchedule($studentId, $startDate);
                return response()->json($this->formatWeeklyData($schedules, $startDate));
        }
    }

    /**
     * Format weekly schedule data for FullCalendar
     */
    private function formatWeeklyData($schedules, $startDate)
    {
        $events = [];
        $weekStart = $startDate->copy()->startOfWeek();

        foreach ($schedules as $day => $daySchedules) {
            $dayDate = $weekStart->copy();
            
            // Find the correct day of the week
            $dayMap = ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 0];
            $targetDay = $dayMap[$day] ?? 1;
            
            while ($dayDate->dayOfWeek !== $targetDay) {
                $dayDate->addDay();
            }

            foreach ($daySchedules as $schedule) {
                $startDateTime = $dayDate->copy()->setTimeFrom($schedule->start_time);
                $endDateTime = $dayDate->copy()->setTimeFrom($schedule->end_time);

                $events[] = [
                    'id' => $schedule->id,
                    'title' => $schedule->subject->subject_name,
                    'start' => $startDateTime->toISOString(),
                    'end' => $endDateTime->toISOString(),
                    'color' => $schedule->subject_color,
                    'extendedProps' => [
                        'teacher' => $schedule->teacher->full_name,
                        'room' => $schedule->room ? $schedule->room->room_name : 'TBD',
                        'subject' => $schedule->subject->subject_name,
                        'time_range' => $schedule->time_range,
                        'duration' => $schedule->duration . ' min',
                        'notes' => $schedule->notes
                    ]
                ];
            }
        }

        return $events;
    }

    /**
     * Format today's schedule data
     */
    private function formatTodayData($schedules)
    {
        return $schedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'subject' => $schedule->subject->subject_name,
                'teacher' => $schedule->teacher->full_name,
                'room' => $schedule->room ? $schedule->room->room_name : 'TBD',
                'time_range' => $schedule->time_range,
                'duration' => $schedule->duration . ' min',
                'color' => $schedule->subject_color,
                'notes' => $schedule->notes
            ];
        });
    }

    /**
     * Format next days schedule data
     */
    private function formatNextDaysData($schedules)
    {
        return $schedules->map(function ($dayData) {
            return [
                'date' => $dayData['date'],
                'day_name' => $dayData['day_name'],
                'schedules' => $dayData['schedules']->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'subject' => $schedule->subject->subject_name,
                        'teacher' => $schedule->teacher->full_name,
                        'room' => $schedule->room ? $schedule->room->room_name : 'TBD',
                        'time_range' => $schedule->time_range,
                        'duration' => $schedule->duration . ' min',
                        'color' => $schedule->subject_color,
                        'notes' => $schedule->notes
                    ];
                })
            ];
        });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This would be for admin/teacher to create schedules
        // Not needed for student view
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // This would be for admin/teacher to create schedules
        // Not needed for student view
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // This would show a specific schedule detail
        // Not needed for student view
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // This would be for admin/teacher to edit schedules
        // Not needed for student view
        return redirect()->back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // This would be for admin/teacher to update schedules
        // Not needed for student view
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // This would be for admin/teacher to delete schedules
        // Not needed for student view
        return redirect()->back();
    }

    /**
     * Get student's dashboard schedule widget data
     */
    public function getDashboardSchedule($studentId = null)
    {
        if (!$studentId && Auth::user()->role_name === 'Student') {
            $student = Auth::user()->student;
            if ($student) {
                $studentId = $student->id;
            }
        }

        if (!$studentId) {
            return response()->json([]);
        }

        $nextDaysSchedule = ClassSchedule::getNextDaysSchedule($studentId, 5);
        
        return response()->json([
            'next_days' => $nextDaysSchedule,
            'today_schedule' => ClassSchedule::getTodaySchedule($studentId)
        ]);
    }

    /**
     * Display the student's personal schedule page
     */
    public function mySchedule()
    {
        if (Auth::user()->role_name !== 'Student') {
            return redirect()->back()->with('error', 'Access denied. This page is for students only.');
        }

        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $weeklySchedule = ClassSchedule::getWeeklySchedule($student->id);
        $todaySchedule = ClassSchedule::getTodaySchedule($student->id);
        $nextDaysSchedule = ClassSchedule::getNextDaysSchedule($student->id, 7);

        return view('schedule.my-schedule', compact('student', 'weeklySchedule', 'todaySchedule', 'nextDaysSchedule'));
    }
}
