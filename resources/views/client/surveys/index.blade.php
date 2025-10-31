@extends('layouts.app')

@section('title', 'My Surveys')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">My Surveys</h2>
            <p class="pageheader-text">Your survey applications and reports</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">My Survey Applications</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Property Address</th>
                                <th>Level</th>
                                <th>Status</th>
                                <th>Surveyor</th>
                                <th>Scheduled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surveys as $survey)
                                <tr>
                                    <td>{{ $survey->id }}</td>
                                    <td>{{ Str::limit($survey->property_address_full, 40) }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $survey->level ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $survey->status_badge }}">
                                            {{ $survey->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ $survey->surveyor ? $survey->surveyor->name : 'Pending' }}</td>
                                    <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('M d, Y') : 'TBD' }}</td>
                                    <td>
                                        <a href="{{ route('client.surveys.show', $survey->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        No surveys found. Submit a survey application from our website.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $surveys->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
