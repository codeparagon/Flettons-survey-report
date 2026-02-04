@extends('layouts.app')

@section('title', 'Survey Report')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
<style>
    .report-page {
        --report-bg: #f8fafc;
        --report-surface: #ffffff;
        --report-primary: #1a202c;
        --report-accent: #c1ec4a;
        --report-muted: #64748b;
        --report-border: #e2e8f0;
        --report-success: #10b981;
        --report-warning: #f59e0b;
        --report-danger: #ef4444;
        --report-info: #3b82f6;
        font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--report-bg);
        color: var(--report-primary);
        padding-bottom: 3rem;
    }

    .report-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1.25rem 2rem;
    }

    /* Hero header */
    .report-hero {
        background: linear-gradient(135deg, var(--report-primary) 0%, #2d3748 100%);
        color: #fff;
        border-radius: 16px;
        padding: 2rem 2.25rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .report-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(193, 236, 74, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .report-hero-inner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        position: relative;
        z-index: 1;
    }
    .report-hero h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.02em;
        color: white!important;
    }
    .report-hero-title-accent {
        color: var(--report-accent);
    }
    .report-breadcrumb {
        font-size: 0.8125rem;
        opacity: 0.85;
        margin: 0;
    }
    .report-breadcrumb a {
        color: rgba(255,255,255,0.9);
        text-decoration: none;
    }
    .report-breadcrumb a:hover {
        color: var(--report-accent);
    }
    .report-breadcrumb .separator {
        opacity: 0.5;
        margin: 0 0.35rem;
    }
    .report-hero-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .report-btn-pdf {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--report-accent);
        color: var(--report-primary);
        font-weight: 600;
        font-size: 0.9375rem;
        border: none;
        border-radius: 10px;
        text-decoration: none;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .report-btn-pdf:hover {
        background: #a8d83a;
        color: var(--report-primary);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Section cards */
    .report-section {
        background: var(--report-surface);
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        border: 1px solid var(--report-border);
        margin-bottom: 1.75rem;
        overflow: hidden;
    }
    .report-section-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid var(--report-border);
        background: linear-gradient(180deg, #fafbfc 0%, #f1f5f9 100%);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .report-section-header h2 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
        color: var(--report-primary);
    }
    .report-section-header .icon-wrap {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--report-primary);
        color: var(--report-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .report-section-body {
        padding: 1.75rem;
    }

    /* Survey details grid */
    .report-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.5rem;
    }
    .report-detail-item {
        padding: 0;
        border: none;
        background: transparent;
    }
    .report-detail-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--report-muted);
        margin-bottom: 0.35rem;
    }
    .report-detail-value {
        font-size: 0.9375rem;
        font-weight: 500;
        color: var(--report-primary);
    }
    .report-badge {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .report-badge--info { background: #dbeafe; color: #1d4ed8; }
    .report-badge--success { background: #d1fae5; color: #047857; }
    .report-badge--warning { background: #fef3c7; color: #b45309; }
    .report-badge--danger { background: #fee2e2; color: #b91c1c; }
    .report-badge--neutral { background: #f1f5f9; color: #475569; }

    /* Desk study */
    .report-desk-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }
    @media (max-width: 768px) {
        .report-desk-grid { grid-template-columns: 1fr; }
    }
    .report-desk-map-wrap {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--report-border);
        background: #f8fafc;
    }
    .report-desk-map-wrap img {
        width: 100%;
        height: auto;
        display: block;
    }
    .report-desk-coords {
        padding: 1rem 1.25rem;
        display: flex;
        gap: 1.5rem;
        font-size: 0.875rem;
        color: var(--report-muted);
    }
    .report-desk-coords span { font-weight: 500; color: var(--report-primary); }
    .report-desk-heading {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--report-muted);
        margin: 0 0 1rem 0;
    }
    .report-risk-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .report-risk-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.6rem 0;
        border-bottom: 1px solid var(--report-border);
        font-size: 0.9375rem;
    }
    .report-risk-list li:last-child { border-bottom: none; }
    .report-risk-list .risk-badge {
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #f1f5f9;
        color: var(--report-primary);
    }
    .report-planning-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .report-planning-item {
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid var(--report-border);
    }
    .report-planning-item strong {
        display: block;
        font-size: 0.75rem;
        color: var(--report-muted);
        margin-bottom: 0.25rem;
    }
    .report-planning-item span { font-size: 0.875rem; }

    /* Survey data – categories */
    .report-category {
        margin-bottom: 2.5rem;
    }
    .report-category:last-child { margin-bottom: 0; }
    .report-category-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--report-primary);
        margin: 0 0 1.25rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--report-primary);
        letter-spacing: -0.01em;
    }
    .report-subcategory-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--report-muted);
        margin: 1.5rem 0 1rem 0;
    }
    .report-subcategory-title:first-of-type { margin-top: 0; }
    .report-cards-row {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
    }
    .report-card {
        background: var(--report-surface);
        border: 1px solid var(--report-border);
        border-radius: 12px;
        overflow: hidden;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .report-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }
    .report-card-header {
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--report-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .report-card-title {
        font-size: 0.9375rem;
        font-weight: 600;
        margin: 0;
        color: var(--report-primary);
    }
    .report-card-body {
        padding: 1.25rem;
        font-size: 0.875rem;
    }
    .report-card-meta {
        color: var(--report-muted);
        font-size: 0.8125rem;
        margin-bottom: 1rem;
    }
    .report-card-meta i { margin-right: 0.35rem; }
    .report-card-row {
        margin-bottom: 0.6rem;
        line-height: 1.45;
    }
    .report-card-row:last-child { margin-bottom: 0; }
    .report-card-row strong {
        display: inline-block;
        min-width: 6rem;
        color: var(--report-muted);
        font-weight: 500;
    }
    .report-card ul {
        margin: 0.5rem 0 0 0;
        padding-left: 1.25rem;
        color: var(--report-primary);
    }
    .report-card ul li { margin-bottom: 0.25rem; }
    .report-content-block {
        font-size: 0.875rem;
        line-height: 1.6;
        color: var(--report-primary);
    }
    .report-content-block p { margin-bottom: 0.75rem; }
    .report-content-block p:last-child { margin-bottom: 0; }

    /* Divider for accommodation / standalone */
    .report-divider {
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid var(--report-border);
    }
    .report-divider .report-category-title { margin-top: 0; }

    /* Empty state */
    .report-empty {
        text-align: center;
        padding: 2rem;
        color: var(--report-muted);
        font-size: 0.9375rem;
    }

    /* Footer actions */
    .report-footer-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
    }
    .report-footer-actions a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.25rem;
        font-size: 0.9375rem;
        font-weight: 500;
        border-radius: 10px;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }
    .report-btn-back {
        background: var(--report-primary);
        color: #fff;
    }
    .report-btn-back:hover {
        background: #2d3748;
        color: #fff;
    }
    .report-btn-list {
        background: var(--report-surface);
        color: var(--report-primary);
        border: 1px solid var(--report-border);
    }
    .report-btn-list:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: var(--report-primary);
    }
</style>
@endpush

@section('content')
<div class="report-page">
    <div class="report-container">
        {{-- Hero header --}}
        <header class="report-hero">
            <div class="report-hero-inner">
                <div>
                    <h1>Final Assessment <span class="report-hero-title-accent">Report</span></h1>
                    <p class="report-breadcrumb">
                        <a href="{{ route('client.dashboard') }}">Dashboard</a>
                        <span class="separator">/</span>
                        <a href="{{ route('client.surveys.index') }}">My Surveys</a>
                        <span class="separator">/</span>
                        <a href="{{ route('client.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a>
                        <span class="separator">/</span>
                        <span>Report</span>
                    </p>
                </div>
                <div class="report-hero-actions">
                    <a href="{{ route('client.surveys.download-pdf', $survey) }}" class="report-btn-pdf">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                </div>
            </div>
        </header>

        {{-- Survey details --}}
        <section class="report-section">
            <div class="report-section-header">
                <div class="icon-wrap"><i class="fas fa-info-circle"></i></div>
                <h2>Survey Details</h2>
            </div>
            <div class="report-section-body">
                <div class="report-details-grid">
                    <div class="report-detail-item">
                        <div class="report-detail-label">Property Address</div>
                        <div class="report-detail-value">{{ $survey->property_address_full ?? $survey->full_address ?? 'N/A' }}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Job Reference</div>
                        <div class="report-detail-value">{{ $survey->job_reference ?? 'N/A' }}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Survey Level</div>
                        <div class="report-detail-value">
                            <span class="report-badge report-badge--info">{{ $survey->level ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Status</div>
                        <div class="report-detail-value">
                            @php
                                $statusMap = ['badge-success' => 'success', 'badge-warning' => 'warning', 'badge-danger' => 'danger', 'badge-info' => 'info'];
                                $statusBadgeClass = $statusMap[$survey->status_badge] ?? 'neutral';
                            @endphp
                            <span class="report-badge report-badge--{{ $statusBadgeClass }}">{{ ucfirst($survey->status) }}</span>
                        </div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Surveyor</div>
                        <div class="report-detail-value">{{ $survey->surveyor ? $survey->surveyor->name : 'Not Assigned' }}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Scheduled Date</div>
                        <div class="report-detail-value">{{ $survey->scheduled_date ? $survey->scheduled_date->format('M d, Y') : 'Not Scheduled' }}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Submitted</div>
                        <div class="report-detail-value">{{ $survey->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="report-detail-item">
                        <div class="report-detail-label">Last Updated</div>
                        <div class="report-detail-value">{{ $survey->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Desk study --}}
        <section class="report-section">
            <div class="report-section-header">
                <div class="icon-wrap"><i class="fas fa-map-marked-alt"></i></div>
                <h2>Desk Study</h2>
            </div>
            <div class="report-section-body">
                <div class="report-desk-grid">
                    <div>
                        <h3 class="report-desk-heading">Location Overview</h3>
                        <div class="report-desk-map-wrap">
                            <img src="{{ $deskStudy['map']['image'] }}" alt="Location map" />
                            <div class="report-desk-coords">
                                <span>Longitude: {{ $deskStudy['map']['longitude'] }}</span>
                                <span>Latitude: {{ $deskStudy['map']['latitude'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="report-desk-heading">Flood Risk Summary</h3>
                        <ul class="report-risk-list">
                            @foreach ($deskStudy['flood_risks'] as $risk)
                                <li>
                                    <span>{{ $risk['label'] }}</span>
                                    <span class="risk-badge">{{ $risk['value'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <h3 class="report-desk-heading" style="margin-top: 1.5rem;">Planning & Compliance</h3>
                        <div class="report-planning-grid">
                            @foreach ($deskStudy['planning'] as $item)
                                <div class="report-planning-item">
                                    <strong>{{ $item['label'] }}</strong>
                                    <span>{{ $item['value'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Survey data --}}
        <section class="report-section">
            <div class="report-section-header">
                <div class="icon-wrap"><i class="fas fa-clipboard-list"></i></div>
                <h2>Survey Data</h2>
            </div>
            <div class="report-section-body">
                @if(count($categories) > 0)
                    @foreach($categories as $categoryName => $subCategories)
                        <div class="report-category">
                            <h3 class="report-category-title">{{ $categoryName }}</h3>
                            @foreach($subCategories as $subCategoryName => $sections)
                                <h4 class="report-subcategory-title">{{ $subCategoryName }}</h4>
                                <div class="report-cards-row">
                                    @foreach($sections as $section)
                                        <div class="report-card">
                                            <div class="report-card-header">
                                                <h5 class="report-card-title">{{ $section['name'] }}</h5>
                                                @php
                                                    $rating = $section['condition_rating'] ?? 'NI';
                                                    $badgeClass = $rating == '1' ? 'success' : ($rating == '2' ? 'warning' : ($rating == '3' ? 'danger' : 'neutral'));
                                                @endphp
                                                <span class="report-badge report-badge--{{ $badgeClass }}">{{ strtoupper($rating) }}</span>
                                            </div>
                                            <div class="report-card-body">
                                                <div class="report-card-meta">
                                                    <i class="fas fa-check-circle"></i>
                                                    Completion: {{ $section['completion'] }}/{{ $section['total'] }}
                                                </div>
                                                @if(!empty($section['selected_section']))
                                                    <div class="report-card-row"><strong>Section type</strong> {{ $section['selected_section'] }}</div>
                                                @endif
                                                @if(!empty($section['location']))
                                                    <div class="report-card-row"><strong>Location</strong> {{ $section['location'] }}</div>
                                                @endif
                                                @if(!empty($section['structure']))
                                                    <div class="report-card-row"><strong>Structure</strong> {{ $section['structure'] }}</div>
                                                @endif
                                                @if(!empty($section['material']))
                                                    <div class="report-card-row"><strong>Material</strong> {{ $section['material'] }}</div>
                                                @endif
                                                @if(!empty($section['remaining_life']))
                                                    <div class="report-card-row"><strong>Remaining life</strong> {{ $section['remaining_life'] }}</div>
                                                @endif
                                                @if(!empty($section['defects']) && count($section['defects']) > 0)
                                                    <div class="report-card-row">
                                                        <strong>Defects</strong>
                                                        <ul>
                                                            @foreach($section['defects'] as $defect)
                                                                <li>{{ $defect }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                @if(!empty($section['costs']) && count($section['costs']) > 0)
                                                    <div class="report-card-row">
                                                        <strong>Costs</strong>
                                                        <ul>
                                                            @foreach($section['costs'] as $cost)
                                                                <li>
                                                                    {{ $cost['category'] ?? '' }} – {{ $cost['description'] ?? '' }} – £{{ $cost['cost'] ?? '0.00' }}
                                                                    @if(!empty($cost['due']))
                                                                        (Due: {{ $cost['due'] }})
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                @if(!empty($section['notes']))
                                                    <div class="report-card-row"><strong>Notes</strong> <span class="text-muted" style="font-size: 0.8125rem;">{{ $section['notes'] }}</span></div>
                                                @endif
                                                @if(!empty($section['photos']) && count($section['photos']) > 0)
                                                    <div class="report-card-row"><strong>Photos</strong> <span class="report-badge report-badge--info">{{ count($section['photos']) }} photo(s)</span></div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if(isset($contentSections['by_subcategory'][$categoryName][$subCategoryName]))
                                    @foreach($contentSections['by_subcategory'][$categoryName][$subCategoryName] as $contentSection)
                                        <div class="report-card" style="margin-top: 1rem;">
                                            <div class="report-card-header">
                                                <h5 class="report-card-title">{{ $contentSection->title }}</h5>
                                            </div>
                                            <div class="report-card-body">
                                                <div class="report-content-block">{!! $contentSection->content !!}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                            @if(isset($contentSections['by_category'][$categoryName]) && count($contentSections['by_category'][$categoryName]) > 0)
                                <h4 class="report-subcategory-title">Content Sections</h4>
                                @foreach($contentSections['by_category'][$categoryName] as $contentSection)
                                    <div class="report-card">
                                        <div class="report-card-header">
                                            <h5 class="report-card-title">{{ $contentSection->title }}</h5>
                                        </div>
                                        <div class="report-card-body">
                                            <div class="report-content-block">{!! $contentSection->content !!}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="report-empty">No survey data available yet.</p>
                @endif

                {{-- Accommodation --}}
                @if(isset($hasAccommodationTypesWithComponents) && $hasAccommodationTypesWithComponents && !empty($accommodationSections))
                    <div class="report-divider">
                        <h3 class="report-category-title">Configuration of Accommodation</h3>
                        <div class="report-cards-row">
                            @foreach($accommodationSections as $accommodation)
                                <div class="report-card">
                                    <div class="report-card-header">
                                        <h5 class="report-card-title">{{ $accommodation['accommodation_type_name'] ?? $accommodation['name'] }}</h5>
                                        @php
                                            $accRating = $accommodation['condition_rating'] ?? 'NI';
                                            $accBadge = $accRating == '1' ? 'success' : ($accRating == '2' ? 'warning' : ($accRating == '3' ? 'danger' : 'neutral'));
                                        @endphp
                                        <span class="report-badge report-badge--{{ $accBadge }}">{{ strtoupper($accRating) }}</span>
                                    </div>
                                    <div class="report-card-body">
                                        <div class="report-card-meta">
                                            <i class="fas fa-check-circle"></i>
                                            Components: {{ $accommodation['completed_components'] ?? 0 }}/{{ $accommodation['total_components'] ?? 0 }}
                                        </div>
                                        @if(!empty($accommodation['notes']))
                                            <div class="report-card-row"><strong>Notes</strong> {{ $accommodation['notes'] }}</div>
                                        @endif
                                        @if(!empty($accommodation['photos']) && count($accommodation['photos']) > 0)
                                            <div class="report-card-row"><strong>Photos</strong> <span class="report-badge report-badge--info">{{ count($accommodation['photos']) }} photo(s)</span></div>
                                        @endif
                                        @if(!empty($accommodation['components']) && count($accommodation['components']) > 0)
                                            <div class="report-card-row">
                                                <strong>Components</strong>
                                                <ul>
                                                    @foreach($accommodation['components'] as $component)
                                                        <li>
                                                            {{ $component['component_name'] ?? 'Component' }}
                                                            @if(!empty($component['material']))
                                                                – {{ $component['material'] }}
                                                            @endif
                                                            @if(!empty($component['defects']) && count($component['defects']) > 0)
                                                                – {{ implode(', ', $component['defects']) }}
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Standalone content --}}
                @if(isset($contentSections['standalone']) && count($contentSections['standalone']) > 0)
                    <div class="report-divider">
                        <h3 class="report-category-title">Additional Content</h3>
                        @foreach($contentSections['standalone'] as $contentSection)
                            <div class="report-card">
                                <div class="report-card-header">
                                    <h5 class="report-card-title">{{ $contentSection->title }}</h5>
                                </div>
                                <div class="report-card-body">
                                    <div class="report-content-block">{!! $contentSection->content !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Footer actions --}}
        <div class="report-footer-actions">
            <a href="{{ route('client.surveys.show', $survey) }}" class="report-btn-back">
                <i class="fas fa-arrow-left"></i> Back to Survey Details
            </a>
            <a href="{{ route('client.surveys.index') }}" class="report-btn-list">
                <i class="fas fa-list"></i> View All Surveys
            </a>
        </div>
    </div>
</div>
@endsection
