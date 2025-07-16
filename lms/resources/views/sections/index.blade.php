@extends('layouts.master')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Sections</h3>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Grade Level</th>
                            <th>Adviser</th>
                            <th>Capacity</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sections as $section)
                        <tr>
                            <td>{{ $section->name }}</td>
                            <td>{{ $section->grade_level }}</td>
                            <td>{{ $section->adviser ? $section->adviser->full_name : '-' }}</td>
                            <td>{{ $section->capacity }}</td>
                            <td>{{ $section->description }}</td>
                            <td>
                                <a href="{{ route('sections.assignStudentsForm', $section->id) }}" class="btn btn-sm btn-info">Assign Students</a>
                                <a href="{{ route('sections.edit', $section->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('sections.destroy', $section->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{ route('sections.create') }}" class="btn btn-primary mt-3">Add Section</a>
            </div>
        </div>
    </div>
</div>
@endsection 