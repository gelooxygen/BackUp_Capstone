@extends('layouts.app')

@section('title', 'Weight Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Grading Weight Settings</h4>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('teacher.grading.weight-settings') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="subject_id">Subject</label>
                                    <select name="subject_id" id="subject_id" class="form-control" required>
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ $selectedSubject == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->subject_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="academic_year_id">Academic Year</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-control" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="semester_id">Semester</label>
                                    <select name="semester_id" id="semester_id" class="form-control" required>
                                        <option value="">Select Semester</option>
                                        @foreach($semesters as $semester)
                                            <option value="{{ $semester->id }}" {{ $selectedSemester == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Load Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($selectedSubject)
                        <!-- Weight Settings Form -->
                        <form method="POST" action="{{ route('teacher.grading.store-weight-settings') }}">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $selectedSubject }}">
                            <input type="hidden" name="academic_year_id" value="{{ $selectedAcademicYear }}">
                            <input type="hidden" name="semester_id" value="{{ $selectedSemester }}">

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        Weight Configuration for {{ $subjects->find($selectedSubject)->subject_name }}
                                        ({{ $academicYears->find($selectedAcademicYear)->name ?? 'N/A' }} - {{ $semesters->find($selectedSemester)->name ?? 'N/A' }})
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Configure the weight percentage for each grading component. Total weight should equal 100%.
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Component</th>
                                                    <th>Description</th>
                                                    <th>Weight (%)</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($components as $component)
                                                    @php
                                                        $existingWeight = $weightSettings->where('component_id', $component->id)->first();
                                                        $weight = $existingWeight ? $existingWeight->weight : $component->weight;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $component->name }}</strong>
                                                            <input type="hidden" name="weights[{{ $loop->index }}][component_id]" value="{{ $component->id }}">
                                                        </td>
                                                        <td>{{ $component->description ?? 'No description' }}</td>
                                                        <td>
                                                            <input type="number" 
                                                                   name="weights[{{ $loop->index }}][weight]" 
                                                                   class="form-control weight-input" 
                                                                   value="{{ $weight }}" 
                                                                   min="0" 
                                                                   max="100" 
                                                                   step="0.01" 
                                                                   required>
                                                        </td>
                                                        <td>
                                                            @if($existingWeight)
                                                                <span class="badge badge-success">Configured</span>
                                                            @else
                                                                <span class="badge badge-warning">Default</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-info">
                                                <tr>
                                                    <td colspan="2"><strong>Total Weight</strong></td>
                                                    <td>
                                                        <span id="total-weight" class="font-weight-bold">0%</span>
                                                    </td>
                                                    <td>
                                                        <span id="weight-status" class="badge badge-danger">Invalid</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-success" id="save-btn" disabled>
                                                <i class="fas fa-save"></i> Save Weight Settings
                                            </button>
                                            <a href="{{ route('teacher.grading.grade-entry') }}" class="btn btn-info">
                                                <i class="fas fa-arrow-left"></i> Back to Grade Entry
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Current Weight Settings Display -->
                        @if($weightSettings->count() > 0)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title">Current Weight Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($weightSettings as $setting)
                                            <div class="col-md-3 mb-3">
                                                <div class="card">
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title">{{ $setting->component->name }}</h6>
                                                        <h3 class="text-primary">{{ $setting->weight }}%</h3>
                                                        <small class="text-muted">{{ $setting->component->description }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate total weight
    function calculateTotalWeight() {
        let total = 0;
        $('.weight-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        
        $('#total-weight').text(total.toFixed(2) + '%');
        
        if (Math.abs(total - 100) < 0.01) {
            $('#weight-status').removeClass('badge-danger badge-warning').addClass('badge-success').text('Valid');
            $('#save-btn').prop('disabled', false);
        } else if (total > 100) {
            $('#weight-status').removeClass('badge-success badge-warning').addClass('badge-danger').text('Over 100%');
            $('#save-btn').prop('disabled', true);
        } else {
            $('#weight-status').removeClass('badge-success badge-danger').addClass('badge-warning').text('Under 100%');
            $('#save-btn').prop('disabled', true);
        }
    }

    // Calculate total on input change
    $('.weight-input').on('input', calculateTotalWeight);
    
    // Initial calculation
    calculateTotalWeight();
});
</script>
@endpush
@endsection 