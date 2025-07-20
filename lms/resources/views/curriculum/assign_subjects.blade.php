@extends('layouts.master')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Assign Subjects to Curriculum: {{ $curriculum->grade_level }}</h3>
                </div>
            </div>
        </div>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('curriculum.assignSubjects', $curriculum->id) }}">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Select Subjects <span class="text-danger">*</span></label>
                        <small class="form-text text-muted">Check the subjects to include in this curriculum.</small>
                        <div class="row">
                            @forelse($subjects as $subject)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input @error('subject_ids') is-invalid @enderror" type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" id="subject{{ $subject->id }}" {{ in_array($subject->id, $assigned) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="subject{{ $subject->id }}">
                                            {{ $subject->subject_name }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning">No subjects available to assign.</div>
                                </div>
                            @endforelse
                        </div>
                        @error('subject_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Assign Subjects</button>
                    <a href="{{ route('curriculum.show', $curriculum->id) }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 