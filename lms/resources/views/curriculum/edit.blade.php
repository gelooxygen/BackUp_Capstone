@extends('layouts.master')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Edit Curriculum</h3>
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
        <form method="POST" action="{{ route('curriculum.update', $curriculum->id) }}">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="grade_level">Grade Level <span class="text-danger">*</span></label>
                        <input type="text" name="grade_level" id="grade_level" class="form-control @error('grade_level') is-invalid @enderror" required value="{{ old('grade_level', $curriculum->grade_level) }}" aria-describedby="gradeLevelHelp">
                        <small id="gradeLevelHelp" class="form-text text-muted">E.g., Grade 1, Grade 2, etc.</small>
                        @error('grade_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $curriculum->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('curriculum.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 