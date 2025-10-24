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
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surveys.index') }}">Surveys</a></li>
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
                <h5 class="mb-0">Client Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="250">Client Name:</th>
                        <td>{{ $survey->client_name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $survey->client_email }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $survey->inf_field_Phone1 ?? $survey->telephone_number }}</td>
                    </tr>
                    <tr>
                        <th>Property Address:</th>
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
                        <th>Property Type:</th>
                        <td>{{ $survey->house_or_flat }}</td>
                    </tr>
                    <tr>
                        <th>Bedrooms:</th>
                        <td>{{ $survey->number_of_bedrooms }}</td>
                    </tr>
                    <tr>
                        <th>Submitted:</th>
                        <td>{{ $survey->is_submitted === 'true' ? 'Yes' : 'No' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Assignment & Status</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label><strong>Surveyor:</strong></label>
                    <div>{{ $survey->surveyor ? $survey->surveyor->name : 'Not Assigned' }}</div>
                </div>
                <div class="mb-3">
                    <label><strong>Status:</strong></label>
                    <div>
                        <span class="badge {{ $survey->status_badge }} p-2">
                            {{ ucfirst($survey->status) }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label><strong>Payment:</strong></label>
                    <div>
                        @if($survey->payment_status === 'paid')
                            <span class="badge badge-success p-2">Paid</span>
                        @elseif($survey->payment_status === 'pending')
                            <span class="badge badge-warning p-2">Pending</span>
                        @else
                            <span class="badge badge-danger p-2">Refunded</span>
                        @endif
                    </div>
                </div>
                <div class="mb-3">
                    <label><strong>Scheduled Date:</strong></label>
                    <div>{{ $survey->scheduled_date ? $survey->scheduled_date->format('M d, Y') : 'Not Scheduled' }}</div>
                </div>
                
                <hr>
                
                @if(!$survey->surveyor_id)
                <a href="{{ route('admin.surveys.edit', $survey->id) }}" class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-user-plus"></i> Assign Survey to Surveyor
                </a>
                @elseif($survey->surveyor)
                <a href="{{ route('admin.survey.sections', $survey) }}" class="btn btn-success btn-block mb-2">
                    <i class="fas fa-clipboard-list"></i> View Survey Sections
                </a>
                @endif
                
                <a href="{{ route('admin.surveys.edit', $survey->id) }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit"></i> Edit Survey
                </a>
                <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
