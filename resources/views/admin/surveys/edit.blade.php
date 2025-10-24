@extends('layouts.app')

@section('title', 'Edit Survey')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Edit Survey #{{ $survey->id }}</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item active">Edit #{{ $survey->id }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Survey Management</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.surveys.update', $survey->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="alert alert-info">
                        <strong>Client:</strong> {{ $survey->client_name }}<br>
                        <strong>Property:</strong> {{ $survey->property_address_full }}
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="surveyor_id">Assign Surveyor</label>
                                <select class="form-control" id="surveyor_id" name="surveyor_id">
                                    <option value="">Unassigned</option>
                                    @foreach($surveyors as $surveyor)
                                        <option value="{{ $surveyor->id }}" {{ old('surveyor_id', $survey->surveyor_id) == $surveyor->id ? 'selected' : '' }}>
                                            {{ $surveyor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="scheduled_date">Scheduled Date</label>
                                <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" 
                                       value="{{ old('scheduled_date', $survey->scheduled_date ? $survey->scheduled_date->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="pending" {{ old('status', $survey->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="assigned" {{ old('status', $survey->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="in_progress" {{ old('status', $survey->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ old('status', $survey->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $survey->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_status">Payment Status</label>
                                <select class="form-control" id="payment_status" name="payment_status">
                                    <option value="pending" {{ old('payment_status', $survey->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('payment_status', $survey->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="refunded" {{ old('payment_status', $survey->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="admin_notes">Admin Notes</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3">{{ old('admin_notes', $survey->admin_notes) }}</textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Survey
                        </button>
                        <a href="{{ route('admin.surveys.show', $survey->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
