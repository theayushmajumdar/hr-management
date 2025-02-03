@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Leave Taken</h5>
                        <h3>{{ $leaveStats['taken'] }} Days</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Leave Applied</h5>
                        <h3>{{ $leaveStats['applied'] }} Days</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Leave Balance</h5>
                        <h3>{{ $leaveStats['balance'] }} Days</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection