@extends('layouts.master')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Curriculum Management</h3>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                <a href="{{ route('curriculum.create') }}" class="btn btn-primary mb-3">Add Curriculum</a>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Grade Level</th>
                            <th>Description</th>
                            <th>Subjects</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($curricula as $curriculum)
                        <tr>
                            <td>{{ $curriculum->grade_level }}</td>
                            <td>{{ $curriculum->description }}</td>
                            <td>
                                @foreach($curriculum->subjects as $subject)
                                    <span class="badge bg-info">{{ $subject->subject_name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('curriculum.show', $curriculum->id) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('curriculum.edit', $curriculum->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('curriculum.destroy', $curriculum->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                                <a href="{{ route('curriculum.assignSubjectsForm', $curriculum->id) }}" class="btn btn-sm btn-primary">Assign Subjects</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 