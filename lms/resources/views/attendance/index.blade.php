@extends('layouts.master')
@section('content')
<div class="container py-4">
    <h2>Attendance Summary</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="GET" action="{{ route('attendance.index') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-md-4">
            <label for="subject_id" class="form-label">Subject</label>
            <select name="subject_id" id="subject_id" class="form-select">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->subject_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="month" class="form-label">Month</label>
            <input type="month" name="month" id="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
        <div class="col-md-3 text-end">
            <a href="{{ route('attendance.export', array_merge(request()->all(), ['format' => 'excel'])) }}" class="btn btn-success me-2">Export Excel</a>
            <a href="{{ route('attendance.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="btn btn-danger">Export PDF</a>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Student</th>
                    @foreach($days as $day)
                        <th class="text-center">{{ $day }}</th>
                    @endforeach
                    <th class="text-center">Present</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                        @foreach($days as $day)
                            <td class="text-center">
                                @php $status = $attendanceMap[$student->id][$day] ?? null; @endphp
                                @if($status === 'present')
                                    <span class="badge bg-success">P</span>
                                @elseif($status === 'absent')
                                    <span class="badge bg-danger">A</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="text-center">{{ $summary[$student->id]['present'] ?? 0 }}</td>
                        <td class="text-center">{{ $summary[$student->id]['total'] ?? 0 }}</td>
                        <td class="text-center">{{ $summary[$student->id]['percentage'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($days) + 4 }}" class="text-center text-muted">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 