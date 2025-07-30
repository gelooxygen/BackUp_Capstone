@php
    $studentId = $studentId ?? null;
    if (!$studentId && Auth::user()->role_name === 'Student') {
        $student = Auth::user()->student;
        if ($student) {
            $studentId = $student->id;
        }
    }
    
    if ($studentId) {
        $weeklySchedule = App\Models\ClassSchedule::getWeeklySchedule($studentId);
    } else {
        $weeklySchedule = [];
    }
    
    // Generate time slots from 6:00 AM to 8:00 PM in 30-minute increments
    $timeSlots = [];
    $startTime = \Carbon\Carbon::createFromTime(6, 0, 0);
    $endTime = \Carbon\Carbon::createFromTime(20, 0, 0);
    
    while ($startTime <= $endTime) {
        $timeSlots[] = $startTime->format('g:i A');
        $startTime->addMinutes(30);
    }
    
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $dayNames = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
@endphp

<div class="card flex-fill comman-shadow">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title">My Schedule</h5>
    </div>
    <div class="card-body">
        <div class="schedule-container">
            <div class="schedule-grid">
                <!-- Time column -->
                <div class="time-column">
                    <div class="time-header">Time</div>
                    @foreach($timeSlots as $timeSlot)
                        <div class="time-slot">{{ $timeSlot }}</div>
                    @endforeach
                </div>
                
                <!-- Day columns -->
                @foreach($days as $index => $day)
                    <div class="day-column">
                        <div class="day-header">{{ $dayNames[$index] }}</div>
                        @foreach($timeSlots as $timeSlot)
                            @php
                                $time = \Carbon\Carbon::createFromFormat('g:i A', $timeSlot);
                                $timeStr = $time->format('H:i:s');
                                $daySchedules = $weeklySchedule[$day] ?? collect();
                                $schedule = $daySchedules->first(function($s) use ($timeStr) {
                                    $start = \Carbon\Carbon::parse($s->start_time);
                                    $end = \Carbon\Carbon::parse($s->end_time);
                                    $current = \Carbon\Carbon::parse($timeStr);
                                    return $current->between($start, $end);
                                });
                            @endphp
                            
                            @if($schedule)
                                @php
                                    $duration = $schedule->duration;
                                    $rowSpan = max(1, ceil($duration / 30));
                                @endphp
                                <div class="schedule-block" 
                                     style="background-color: {{ $schedule->subject_color }}; grid-row: span {{ $rowSpan }};"
                                     data-bs-toggle="tooltip" 
                                     data-bs-placement="top" 
                                     title="{{ $schedule->subject->subject_name }} - {{ $schedule->teacher->full_name ?? 'TBA' }} - {{ $schedule->room ? $schedule->room->room_name : 'TBD' }}">
                                    <div class="schedule-content">
                                        <div class="subject-code">{{ $schedule->subject->subject_id ?? 'N/A' }}</div>
                                        <div class="subject-name">{{ $schedule->subject->subject_name }}</div>
                                        <div class="class-type">({{ $schedule->class_type_display }})</div>
                                        <div class="teacher-name">{{ $schedule->teacher->full_name ?? 'TBA' }}</div>
                                        <div class="room-name">{{ $schedule->room ? $schedule->room->room_name : 'TBD' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="empty-slot"></div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.schedule-container {
    overflow-x: auto;
    overflow-y: auto;
    max-height: 600px;
}

.schedule-grid {
    display: grid;
    grid-template-columns: 120px repeat(7, 1fr);
    grid-auto-rows: 30px;
    min-width: 1000px;
    border: 1px solid #e0e0e0;
    background: white;
}

.time-column, .day-column {
    border-right: 1px solid #e0e0e0;
}

.time-column {
    background-color: #f8f9fa;
    position: sticky;
    left: 0;
    z-index: 10;
}

.time-header, .day-header {
    background-color: #3d5ee1;
    color: white;
    padding: 8px 4px;
    text-align: center;
    font-weight: 600;
    font-size: 0.8rem;
    border-bottom: 1px solid #e0e0e0;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.time-slot {
    padding: 4px 8px;
    font-size: 0.75rem;
    color: #666;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 30px;
}

.empty-slot {
    border-bottom: 1px solid #f0f0f0;
    height: 30px;
}

.schedule-block {
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    margin: 1px;
    padding: 2px;
    color: white;
    font-size: 0.7rem;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
}

.schedule-block:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    z-index: 5;
}

.schedule-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: center;
    padding: 2px;
}

.subject-code {
    font-weight: 700;
    font-size: 0.65rem;
    line-height: 1;
    margin-bottom: 1px;
}

.subject-name {
    font-weight: 600;
    font-size: 0.6rem;
    line-height: 1;
    margin-bottom: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.class-type {
    font-size: 0.55rem;
    opacity: 0.9;
    line-height: 1;
    margin-bottom: 1px;
}

.teacher-name {
    font-size: 0.55rem;
    opacity: 0.8;
    line-height: 1;
    margin-bottom: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.room-name {
    font-size: 0.55rem;
    opacity: 0.8;
    line-height: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media (max-width: 768px) {
    .schedule-grid {
        grid-template-columns: 80px repeat(7, 1fr);
        min-width: 800px;
    }
    
    .time-header, .day-header {
        font-size: 0.7rem;
        padding: 6px 2px;
    }
    
    .time-slot {
        font-size: 0.65rem;
        padding: 2px 4px;
    }
    
    .schedule-content {
        padding: 1px;
    }
    
    .subject-code {
        font-size: 0.6rem;
    }
    
    .subject-name {
        font-size: 0.55rem;
    }
    
    .class-type, .teacher-name, .room-name {
        font-size: 0.5rem;
    }
}

.schedule-container::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.schedule-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.schedule-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.schedule-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    document.querySelectorAll('.schedule-block').forEach(function(block) {
        block.addEventListener('click', function() {
            console.log('Schedule block clicked:', this.getAttribute('title'));
        });
    });
});
</script> 