@extends('layouts.app')

@section('title', 'Survey Report')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">Final Assessment Report</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('client.surveys.index') }}">My Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('client.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
                        <li class="breadcrumb-item active">Report</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- PDF Download Button -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body text-center">
                <a href="{{ route('client.surveys.download-pdf', $survey) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-file-pdf"></i> Download PDF Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Survey Details Section -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Survey Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Property Address:</th>
                                <td>{{ $survey->property_address_full ?? $survey->full_address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Job Reference:</th>
                                <td><strong>{{ $survey->job_reference ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <th>Survey Level:</th>
                                <td><span class="badge badge-info">{{ $survey->level ?? 'N/A' }}</span></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td><span class="badge {{ $survey->status_badge }}">{{ ucfirst($survey->status) }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Surveyor:</th>
                                <td>{{ $survey->surveyor ? $survey->surveyor->name : 'Not Assigned' }}</td>
                            </tr>
                            <tr>
                                <th>Scheduled Date:</th>
                                <td>{{ $survey->scheduled_date ? $survey->scheduled_date->format('M d, Y') : 'Not Scheduled' }}</td>
                            </tr>
                            <tr>
                                <th>Submitted:</th>
                                <td>{{ $survey->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $survey->updated_at->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Desk Study Section -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map-marked-alt"></i> Desk Study</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Location Overview -->
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold mb-3">Location Overview</h6>
                        <div class="mb-3">
                            <img src="{{ $deskStudy['map']['image'] }}" alt="Map preview" class="img-fluid rounded" />
                        </div>
                        <dl class="row">
                            <dt class="col-sm-4">Longitude:</dt>
                            <dd class="col-sm-8">{{ $deskStudy['map']['longitude'] }}</dd>
                            <dt class="col-sm-4">Latitude:</dt>
                            <dd class="col-sm-8">{{ $deskStudy['map']['latitude'] }}</dd>
                        </dl>
                    </div>

                    <!-- Flood Risk Summary -->
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold mb-3">Flood Risk Summary</h6>
                        <ul class="list-unstyled">
                            @foreach ($deskStudy['flood_risks'] as $risk)
                                <li class="mb-2">
                                    <strong>{{ $risk['label'] }}:</strong> 
                                    <span class="badge badge-secondary">{{ $risk['value'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Planning & Compliance -->
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="font-weight-bold mb-3">Planning & Compliance</h6>
                        <div class="row">
                            @foreach ($deskStudy['planning'] as $item)
                                <div class="col-md-4 mb-2">
                                    <strong>{{ $item['label'] }}:</strong> {{ $item['value'] }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Survey Data Section -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Survey Data</h5>
            </div>
            <div class="card-body">
                @if(count($categories) > 0)
                    @foreach($categories as $categoryName => $subCategories)
                        <div class="mb-4">
                            <h4 class="mb-3" style="color: #1E3A5F; border-bottom: 2px solid #1E3A5F; padding-bottom: 10px;">
                                {{ $categoryName }}
                            </h4>
                            
                            @foreach($subCategories as $subCategoryName => $sections)
                                <div class="mb-4">
                                    <h5 class="mb-3" style="color: #4A5568;">
                                        {{ $subCategoryName }}
                                    </h5>
                                    
                                    <div class="row">
                                        @foreach($sections as $section)
                                            <div class="col-md-6 mb-3">
                                                <div class="card border">
                                                    <div class="card-header bg-light">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0">{{ $section['name'] }}</h6>
                                                            <span class="badge badge-{{ $section['condition_rating'] == '1' ? 'success' : ($section['condition_rating'] == '2' ? 'warning' : ($section['condition_rating'] == '3' ? 'danger' : 'secondary')) }}">
                                                                {{ strtoupper($section['condition_rating'] ?? 'NI') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-check-circle"></i> 
                                                                Completion: {{ $section['completion'] }}/{{ $section['total'] }}
                                                            </small>
                                                        </div>
                                                        
                                                        @if(!empty($section['selected_section']))
                                                            <div class="mb-1">
                                                                <strong>Section Type:</strong> {{ $section['selected_section'] }}
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($section['location']))
                                                            <div class="mb-1">
                                                                <strong>Location:</strong> {{ $section['location'] }}
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($section['structure']))
                                                            <div class="mb-1">
                                                                <strong>Structure:</strong> {{ $section['structure'] }}
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($section['material']))
                                                            <div class="mb-1">
                                                                <strong>Material:</strong> {{ $section['material'] }}
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($section['remaining_life']))
                                                            <div class="mb-1">
                                                                <strong>Remaining Life:</strong> {{ $section['remaining_life'] }}
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($section['defects']) && count($section['defects']) > 0)
                                                            <div class="mb-2">
                                                                <strong>Defects:</strong>
                                                                <ul class="mb-0 pl-3">
                                                                    @foreach($section['defects'] as $defect)
                                                                        <li>{{ $defect }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($section['costs']) && count($section['costs']) > 0)
                                                            <div class="mb-2">
                                                                <strong>Costs:</strong>
                                                                <ul class="mb-0 pl-3">
                                                                    @foreach($section['costs'] as $cost)
                                                                        <li>
                                                                            {{ $cost['category'] ?? '' }} - 
                                                                            {{ $cost['description'] ?? '' }} - 
                                                                            £{{ $cost['cost'] ?? '0.00' }}
                                                                            @if(!empty($cost['due']))
                                                                                (Due: {{ $cost['due'] }})
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($section['notes']))
                                                            <div class="mb-2">
                                                                <strong>Notes:</strong>
                                                                <p class="mb-0 text-muted small">{{ $section['notes'] }}</p>
                                                            </div>
                                                        @endif
                                                        
                                                        @if(!empty($section['photos']) && count($section['photos']) > 0)
                                                            <div class="mb-2">
                                                                <strong>Photos:</strong> 
                                                                <span class="badge badge-info">{{ count($section['photos']) }} photo(s)</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    {{-- Content sections linked to this subcategory --}}
                                    @if(isset($contentSections['by_subcategory'][$categoryName][$subCategoryName]))
                                        @foreach($contentSections['by_subcategory'][$categoryName][$subCategoryName] as $contentSection)
                                            <div class="col-md-12 mb-3">
                                                <div class="card border">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">{{ $contentSection->title }}</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="content-section-body">
                                                            {!! $contentSection->content !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                            
                            {{-- Content sections linked to this category (not subcategory) --}}
                            @if(isset($contentSections['by_category'][$categoryName]) && count($contentSections['by_category'][$categoryName]) > 0)
                                <div class="mb-4">
                                    <h5 class="mb-3" style="color: #4A5568;">Content Sections</h5>
                                    @foreach($contentSections['by_category'][$categoryName] as $contentSection)
                                        <div class="col-md-12 mb-3">
                                            <div class="card border">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0">{{ $contentSection->title }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="content-section-body">
                                                        {!! $contentSection->content !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No survey data available yet.</p>
                @endif

                <!-- Accommodation Configuration Section -->
                @if(isset($hasAccommodationTypesWithComponents) && $hasAccommodationTypesWithComponents && !empty($accommodationSections))
                    <div class="mb-4 mt-4" style="border-top: 2px solid #E2E8F0; padding-top: 20px;">
                        <h4 class="mb-3" style="color: #1E3A5F; border-bottom: 2px solid #1E3A5F; padding-bottom: 10px;">
                            Configuration of Accommodation
                        </h4>
                        
                        <div class="row">
                            @foreach($accommodationSections as $accommodation)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">{{ $accommodation['accommodation_type_name'] ?? $accommodation['name'] }}</h6>
                                                <span class="badge badge-{{ $accommodation['condition_rating'] == '1' ? 'success' : ($accommodation['condition_rating'] == '2' ? 'warning' : ($accommodation['condition_rating'] == '3' ? 'danger' : 'secondary')) }}">
                                                    {{ strtoupper($accommodation['condition_rating'] ?? 'NI') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-check-circle"></i> 
                                                    Components: {{ $accommodation['completed_components'] ?? 0 }}/{{ $accommodation['total_components'] ?? 0 }}
                                                </small>
                                            </div>
                                            
                                            @if(!empty($accommodation['notes']))
                                                <div class="mb-2">
                                                    <strong>Notes:</strong>
                                                    <p class="mb-0 text-muted small">{{ $accommodation['notes'] }}</p>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($accommodation['photos']) && count($accommodation['photos']) > 0)
                                                <div class="mb-2">
                                                    <strong>Photos:</strong> 
                                                    <span class="badge badge-info">{{ count($accommodation['photos']) }} photo(s)</span>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($accommodation['components']) && count($accommodation['components']) > 0)
                                                <div class="mb-2">
                                                    <strong>Components:</strong>
                                                    <ul class="mb-0 pl-3">
                                                        @foreach($accommodation['components'] as $component)
                                                            <li>
                                                                {{ $component['component_name'] ?? 'Component' }}
                                                                @if(!empty($component['material']))
                                                                    - Material: {{ $component['material'] }}
                                                                @endif
                                                                @if(!empty($component['defects']) && count($component['defects']) > 0)
                                                                    - Defects: {{ implode(', ', $component['defects']) }}
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Standalone Content Sections -->
                @if(isset($contentSections['standalone']) && count($contentSections['standalone']) > 0)
                    <div class="mb-4 mt-4" style="border-top: 2px solid #E2E8F0; padding-top: 20px;">
                        <h4 class="mb-3" style="color: #1E3A5F; border-bottom: 2px solid #1E3A5F; padding-bottom: 10px;">
                            Additional Content
                        </h4>
                        
                        @foreach($contentSections['standalone'] as $contentSection)
                            <div class="col-md-12 mb-3">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ $contentSection->title }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="content-section-body">
                                            {!! $contentSection->content !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Back Button -->
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="text-center">
            <a href="{{ route('client.surveys.show', $survey) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Survey Details
            </a>
            <a href="{{ route('client.surveys.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> View All Surveys
            </a>
        </div>
    </div>
</div>
@endsection

