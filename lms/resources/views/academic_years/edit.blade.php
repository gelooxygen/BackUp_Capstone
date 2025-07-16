@extends('layouts.master')
@section('content')
<div class="container mt-4">
    <h2>Edit Academic Year</h2>
    <form action="{{ route('academic_years.update', $academicYear) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $academicYear->name) }}">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $academicYear->start_date) }}">
            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $academicYear->end_date) }}">
            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('academic_years.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 