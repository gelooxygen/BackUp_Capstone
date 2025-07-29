<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CalendarEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'month');
        $subjectId = $request->get('subject_id');
        $teacherId = $request->get('teacher_id');
        $startDate = $request->get('start');
        $endDate = $request->get('end');

        $events = CalendarEvent::with(['subject', 'teacher', 'room'])
            ->when($subjectId, function ($query) use ($subjectId) {
                return $query->bySubject($subjectId);
            })
            ->when($teacherId, function ($query) use ($teacherId) {
                return $query->byTeacher($teacherId);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->inDateRange($startDate, $endDate);
            })
            ->orderBy('start_time')
            ->get();

        if ($request->ajax()) {
            return response()->json($events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_time->toISOString(),
                    'end' => $event->end_time->toISOString(),
                    'color' => $event->event_color,
                    'extendedProps' => [
                        'description' => $event->description,
                        'event_type' => $event->event_type,
                        'subject' => $event->subject?->subject_name,
                        'teacher' => $event->teacher?->full_name,
                        'room' => $event->room?->room_name,
                        'is_all_day' => $event->is_all_day
                    ]
                ];
            }));
        }

        $subjects = Subject::all();
        $teachers = Teacher::all();
        $rooms = Room::active()->get();

        return view('calendar.index', compact('events', 'subjects', 'teachers', 'rooms', 'view'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $rooms = Room::active()->get();
        $eventTypes = ['exam', 'activity', 'meeting', 'deadline', 'holiday', 'other'];

        return view('calendar.create', compact('subjects', 'teachers', 'rooms', 'eventTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:exam,activity,meeting,deadline,holiday,other',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room_id' => 'nullable|exists:rooms,id',
            'is_all_day' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|in:daily,weekly,monthly',
            'recurrence_end_date' => 'nullable|date|after:start_time'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check for scheduling conflicts
        $conflicts = CalendarEvent::checkConflicts(
            $request->start_time,
            $request->end_time,
            $request->teacher_id,
            $request->room_id
        );

        if (!empty($conflicts)) {
            $conflictMessage = 'Scheduling conflicts detected: ';
            if (isset($conflicts['teacher'])) {
                $conflictMessage .= 'Teacher has conflicting events. ';
            }
            if (isset($conflicts['room'])) {
                $conflictMessage .= 'Room is already booked. ';
            }

            if ($request->ajax()) {
                return response()->json([
                    'error' => $conflictMessage,
                    'conflicts' => $conflicts,
                    'available_slots' => CalendarEvent::getAvailableTimeSlots(
                        $request->start_time,
                        $request->teacher_id,
                        $request->room_id
                    )
                ], 409);
            }

            return redirect()->back()
                ->with('error', $conflictMessage)
                ->withInput();
        }

        $event = CalendarEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_type' => $request->event_type,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'room_id' => $request->room_id,
            'created_by' => Auth::id(),
            'is_all_day' => $request->boolean('is_all_day'),
            'is_recurring' => $request->boolean('is_recurring'),
            'recurrence_pattern' => $request->recurrence_pattern,
            'recurrence_end_date' => $request->recurrence_end_date
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event created successfully',
                'event' => $event->load(['subject', 'teacher', 'room'])
            ]);
        }

        return redirect()->route('calendar.index')->with('success', 'Event created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEvent $calendarEvent)
    {
        $calendarEvent->load(['subject', 'teacher', 'room', 'createdBy']);
        return view('calendar.show', compact('calendarEvent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarEvent $calendarEvent)
    {
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $rooms = Room::active()->get();
        $eventTypes = ['exam', 'activity', 'meeting', 'deadline', 'holiday', 'other'];

        return view('calendar.edit', compact('calendarEvent', 'subjects', 'teachers', 'rooms', 'eventTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:exam,activity,meeting,deadline,holiday,other',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room_id' => 'nullable|exists:rooms,id',
            'is_all_day' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|in:daily,weekly,monthly',
            'recurrence_end_date' => 'nullable|date|after:start_time'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check for scheduling conflicts (excluding current event)
        $conflicts = CalendarEvent::checkConflicts(
            $request->start_time,
            $request->end_time,
            $request->teacher_id,
            $request->room_id,
            $calendarEvent->id
        );

        if (!empty($conflicts)) {
            $conflictMessage = 'Scheduling conflicts detected: ';
            if (isset($conflicts['teacher'])) {
                $conflictMessage .= 'Teacher has conflicting events. ';
            }
            if (isset($conflicts['room'])) {
                $conflictMessage .= 'Room is already booked. ';
            }

            if ($request->ajax()) {
                return response()->json([
                    'error' => $conflictMessage,
                    'conflicts' => $conflicts,
                    'available_slots' => CalendarEvent::getAvailableTimeSlots(
                        $request->start_time,
                        $request->teacher_id,
                        $request->room_id
                    )
                ], 409);
            }

            return redirect()->back()
                ->with('error', $conflictMessage)
                ->withInput();
        }

        $calendarEvent->update([
            'title' => $request->title,
            'description' => $request->description,
            'event_type' => $request->event_type,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'room_id' => $request->room_id,
            'is_all_day' => $request->boolean('is_all_day'),
            'is_recurring' => $request->boolean('is_recurring'),
            'recurrence_pattern' => $request->recurrence_pattern,
            'recurrence_end_date' => $request->recurrence_end_date
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully',
                'event' => $calendarEvent->load(['subject', 'teacher', 'room'])
            ]);
        }

        return redirect()->route('calendar.index')->with('success', 'Event updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEvent $calendarEvent, Request $request)
    {
        $calendarEvent->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully'
            ]);
        }

        return redirect()->route('calendar.index')->with('success', 'Event deleted successfully');
    }

    /**
     * Get available time slots for a given date
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room_id' => 'nullable|exists:rooms,id',
            'duration' => 'nullable|integer|min:15|max:480'
        ]);

        $slots = CalendarEvent::getAvailableTimeSlots(
            $request->date,
            $request->teacher_id,
            $request->room_id,
            $request->duration ?? 60
        );

        return response()->json($slots);
    }

    /**
     * Check for conflicts
     */
    public function checkConflicts(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room_id' => 'nullable|exists:rooms,id',
            'exclude_event_id' => 'nullable|exists:calendar_events,id'
        ]);

        $conflicts = CalendarEvent::checkConflicts(
            $request->start_time,
            $request->end_time,
            $request->teacher_id,
            $request->room_id,
            $request->exclude_event_id
        );

        return response()->json([
            'has_conflicts' => !empty($conflicts),
            'conflicts' => $conflicts,
            'available_slots' => CalendarEvent::getAvailableTimeSlots(
                $request->start_time,
                $request->teacher_id,
                $request->room_id
            )
        ]);
    }
}
