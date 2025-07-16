@extends('layouts.master')
@section('content')
<div class="container mt-4">
    <h2>Academic Years</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('academic_years.create') }}" class="btn btn-primary mb-3">Add Academic Year</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($academicYears as $year)
                <tr>
                    <td>{{ $year->name }}</td>
                    <td>{{ $year->start_date }}</td>
                    <td>{{ $year->end_date }}</td>
                    <td>
                        <a href="{{ route('academic_years.edit', $year) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('academic_years.destroy', $year) }}" method="POST" style="display:inline-block;">
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