@extends('layouts.master')
@section('content')
<div class="container py-4">
    <h2>Mark Attendance</h2>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('attendance.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="subject_id" class="form-label">Subject</label>
                <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $subjectId ?? '') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $date ?? now()->toDateString()) }}" required>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Present</th>
                        <th>Absent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="text-center">
                                <input type="radio" name="attendance[{{ $student->id }}]" value="present" {{ (old('attendance.' . $student->id, $existing[$student->id] ?? '') == 'present') ? 'checked' : '' }} required>
                            </td>
                            <td class="text-center">
                                <input type="radio" name="attendance[{{ $student->id }}]" value="absent" {{ (old('attendance.' . $student->id, $existing[$student->id] ?? '') == 'absent') ? 'checked' : '' }} required>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No students found for this subject.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-success">Save Attendance</button>
    </form>
</div>
@endsection 