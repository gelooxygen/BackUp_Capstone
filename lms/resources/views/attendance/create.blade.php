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
                <label for="section_id" class="form-label">Section</label>
                <select name="section_id" id="section_id" class="form-select @error('section_id') is-invalid @enderror" required>
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id', $sectionId ?? '') == $section->id ? 'selected' : '' }}>
                            {{ $section->name }} (Grade {{ $section->grade_level }})
                        </option>
                    @endforeach
                </select>
                @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
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
                        <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                    @forelse($students as $student)
                            <tr>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="text-center">
                                <input type="radio" name="attendance[{{ $student->id }}][status]" value="present" 
                                    {{ (old('attendance.' . $student->id . '.status', $existing[$student->id]['status'] ?? '') == 'present') ? 'checked' : '' }} required>
                            </td>
                            <td class="text-center">
                                <input type="radio" name="attendance[{{ $student->id }}][status]" value="absent" 
                                    {{ (old('attendance.' . $student->id . '.status', $existing[$student->id]['status'] ?? '') == 'absent') ? 'checked' : '' }} required>
                            </td>
                            <td>
                                <input type="text" name="attendance[{{ $student->id }}][remarks]" class="form-control form-control-sm"
                                    value="{{ old('attendance.' . $student->id . '.remarks', $existing[$student->id]['remarks'] ?? '') }}"
                                    placeholder="Optional remarks">
                                </td>
                            </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No students found for this section.</td>
                        </tr>
                    @endforelse
                        </tbody>
                    </table>
        </div>
                    <button type="submit" class="btn btn-success">Save Attendance</button>
        </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sectionSelect = document.getElementById('section_id');
    const subjectSelect = document.getElementById('subject_id');
    
    // When section changes, update available subjects
    sectionSelect.addEventListener('change', function() {
        const sectionId = this.value;
        if (!sectionId) {
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            return;
        }
        
        // Fetch subjects for the selected section
        fetch(`/api/sections/${sectionId}/subjects`)
            .then(response => response.json())
            .then(subjects => {
                subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                subjects.forEach(subject => {
                    subjectSelect.innerHTML += `<option value="${subject.id}">${subject.subject_name}</option>`;
                });
            });
    });
});
</script>
@endpush
@endsection 