@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">My Schedule</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active">My Schedule</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Information Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <i class="fas fa-calendar-week fa-2x text-primary mb-2"></i>
                                        <h5>Weekly Schedule</h5>
                                        <p class="text-muted">View your complete class schedule</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <i class="fas fa-clock fa-2x text-success mb-2"></i>
                                        <h5>Time Slots</h5>
                                        <p class="text-muted">6:00 AM - 8:00 PM</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <i class="fas fa-chalkboard-teacher fa-2x text-info mb-2"></i>
                                        <h5>Class Types</h5>
                                        <p class="text-muted">Lecture & Laboratory</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <i class="fas fa-map-marker-alt fa-2x text-warning mb-2"></i>
                                        <h5>Room Info</h5>
                                        <p class="text-muted">Classroom locations</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Schedule Grid -->
            <div class="row">
                <div class="col-12">
                    @include('components.my-schedule')
                </div>
            </div>

            <!-- Schedule Legend -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Schedule Legend</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Class Types:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-circle text-primary"></i> <strong>Lecture:</strong> Regular classroom instruction</li>
                                        <li><i class="fas fa-circle text-success"></i> <strong>Laboratory:</strong> Hands-on practical sessions</li>
                                        <li><i class="fas fa-circle text-info"></i> <strong>Tutorial:</strong> Small group learning</li>
                                        <li><i class="fas fa-circle text-warning"></i> <strong>Exam:</strong> Assessment periods</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Tips:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-info-circle text-info"></i> Hover over schedule blocks for details</li>
                                        <li><i class="fas fa-mobile-alt text-primary"></i> Scroll horizontally on mobile devices</li>
                                        <li><i class="fas fa-clock text-success"></i> Check "Today's Classes" on dashboard for current day</li>
                                        <li><i class="fas fa-exclamation-triangle text-warning"></i> Arrive 5 minutes before class starts</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 