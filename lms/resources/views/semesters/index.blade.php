@extends('layouts.master')
@section('content')
<div class="container mt-4">
    <h2>Semesters</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('semesters.create') }}" class="btn btn-primary mb-3">Add Semester</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Academic Year</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($semesters as $semester)
                <tr>
                    <td>{{ $semester->name }}</td>
                    <td>{{ $semester->academicYear->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('semesters.edit', $semester) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('semesters.destroy', $semester) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 