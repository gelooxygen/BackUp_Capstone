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
                <a href="{{ route('curriculum.create') }}" class="btn btn-primary mb-3">
                    <i class="fas fa-plus"></i> Add Curriculum
                </a>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Grade Level</th>
                            <th>Description</th>
                            <th>Subjects</th>
                            <th width="200">Actions</th>
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
                                <div class="btn-group" role="group">
                                    <a href="{{ route('curriculum.show', $curriculum->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('curriculum.edit', $curriculum->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('curriculum.assignSubjectsForm', $curriculum->id) }}" class="btn btn-sm btn-primary" title="Assign Subjects">
                                        <i class="fas fa-link"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" title="Delete" 
                                            data-id="{{ $curriculum->id }}" data-grade="{{ $curriculum->grade_level }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Hidden form for delete -->
                                <form id="delete-form-{{ $curriculum->id }}" action="{{ route('curriculum.destroy', $curriculum->id) }}" method="POST" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button clicks
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var grade = this.getAttribute('data-grade');
            
            if (confirm('Are you sure you want to delete the curriculum "' + grade + '"? This action cannot be undone.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endsection 