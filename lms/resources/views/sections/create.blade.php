@extends('layouts.master')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Add Section</h3>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('sections.store') }}">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Section Name</label>
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label for="grade_level">Grade Level</label>
                        <input type="text" name="grade_level" id="grade_level" class="form-control" required value="{{ old('grade_level') }}">
                    </div>
                    <div class="form-group">
                        <label for="adviser_id">Adviser</label>
                        <select name="adviser_id" id="adviser_id" class="form-control">
                            <option value="">-- Select Adviser --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('adviser_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Capacity</label>
                        <input type="number" name="capacity" id="capacity" class="form-control" value="{{ old('capacity') }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Section</button>
                    <a href="{{ route('sections.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 