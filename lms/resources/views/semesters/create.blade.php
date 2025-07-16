@extends('layouts.master')
@section('content')
<div class="container mt-4">
    <h2>Add Semester</h2>
    <form action="{{ route('semesters.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="academic_year_id" class="form-label">Academic Year</label>
            <select class="form-control @error('academic_year_id') is-invalid @enderror" id="academic_year_id" name="academic_year_id">
                <option value="">Select Academic Year</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                @endforeach
            </select>
            @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route('semesters.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 