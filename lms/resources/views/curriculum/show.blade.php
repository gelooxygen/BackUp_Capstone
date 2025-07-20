@extends('layouts.master')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Curriculum Details: {{ $curriculum->grade_level }}</h3>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                <h5>Description</h5>
                <p>{{ $curriculum->description }}</p>
                <h5>Subjects</h5>
                <ul>
                    @foreach($curriculum->subjects as $subject)
                        <li>{{ $subject->subject_name }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('curriculum.assignSubjectsForm', $curriculum->id) }}" class="btn btn-primary">Assign/Unassign Subjects</a>
                <a href="{{ route('curriculum.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection 