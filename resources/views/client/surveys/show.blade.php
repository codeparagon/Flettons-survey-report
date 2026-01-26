@extends('layouts.app')

@section('title', 'Survey Details')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Survey #{{ $survey->id }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('client.surveys.index') }}">My Surveys</a></li>
                        <li class="breadcrumb-item active">Survey #{{ $survey->id }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Survey Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="200">Property Address:</th>
                        <td>{{ $survey->property_address_full }}</td>
                    </tr>
                    <tr>
                        <th>Postcode:</th>
                        <td>{{ $survey->inf_field_PostalCode2 ?? $survey->postcode }}</td>
                    </tr>
                    <tr>
                        <th>Survey Level:</th>
                        <td><span class="badge badge-info">{{ $survey->level ?? 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <th>Surveyor:</th>
                        <td>{{ $survey->surveyor ? $survey->surveyor->name : 'Not Assigned Yet' }}</td>
                    </tr>
                    <tr>
                        <th>Scheduled Date:</th>
                        <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('M d, Y') : 'To Be Scheduled' }}</td>
                    </tr>
                    <tr>
                        <th>Submitted:</th>
                        <td>{{ $survey->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label><strong>Survey Status:</strong></label>
                    <div>
                        <span class="badge {{ $survey->status_badge }} p-2">
                            {{ ucfirst($survey->status) }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label><strong>Payment Status:</strong></label>
                    <div>
                        @if($survey->payment_status === 'paid')
                            <span class="badge badge-success p-2">Paid</span>
                        @else
                            <span class="badge badge-warning p-2">Pending</span>
                        @endif
                    </div>
                </div>
                
                <hr>
                
                <a href="{{ route('client.surveys.report', $survey) }}" class="btn btn-primary btn-block mb-2">
                    <i class="fas fa-file-alt"></i> View Full Report
                </a>
                
                <a href="{{ route('client.surveys.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
