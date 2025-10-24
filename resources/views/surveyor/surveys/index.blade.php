@extends('layouts.app')

@section('title', 'My Surveys')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">My Surveys</h2>
            <p class="pageheader-text">Assigned surveys and available surveys to claim</p>
        </div>
    </div>
</div>

@if($unassignedSurveys->count() > 0)
<div class="row">
    <div class="col-xl-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Available Surveys to Claim</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Property Address</th>
                                <th>Level</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($unassignedSurveys as $survey)
                                <tr>
                                    <td>{{ $survey->id }}</td>
                                    <td>{{ $survey->client_name }}</td>
                                    <td>{{ Str::limit($survey->property_address_full, 40) }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $survey->level ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $survey->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('surveyor.surveys.show', $survey->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-hand-paper"></i> Claim
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">My Assigned Surveys</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Property Address</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>Scheduled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignedSurveys as $survey)
                                <tr>
                                    <td>{{ $survey->id }}</td>
                                    <td>{{ $survey->client_name }}</td>
                                    <td>{{ Str::limit($survey->property_address_full, 40) }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $survey->level ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $survey->status_badge }}">
                                            {{ ucfirst($survey->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('M d, Y') : 'TBD' }}</td>
                                    <td>
                                        <a href="{{ route('surveyor.surveys.show', $survey->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        No surveys assigned to you yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

