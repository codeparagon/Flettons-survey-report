@extends('layouts.survey-detail')

@section('title', 'Survey Details')

@php
    $currentTab = request()->get('tab', 'client-property');
    $currentSection = request()->get('section', '');
@endphp

@section('content')
<div class="survey-detail-screen">
    <section id="survey-overview" class="survey-detail-section survey-detail-section--headline">
        <div class="survey-detail-headline">
            <div class="survey-detail-location">
                <i class="fas fa-chevron-left survey-detail-location-icon"></i>
                <span>{{ $survey->full_address ?? '123, Sample Street, Kent DA9 9ZT' }}</span>
            </div>
            <div class="survey-detail-jobref">
                <span class="survey-detail-jobref-label">Job Reference</span>
                <span class="survey-detail-jobref-value">{{ $survey->job_reference ?? '12SE39DT-SH' }}</span>
            </div>
        </div>
    </section>

    <section class="survey-detail-section">
        <div class="survey-detail-grid survey-detail-grid--two">
            <article class="survey-detail-card">
                <header class="survey-detail-card-header">
                    <h3>Client Information</h3>
                </header>
                <div class="survey-detail-card-body">
                    <dl class="survey-detail-datalist">
                        <div><dt>Full Name</dt><dd>{{ $survey->client_name ?? 'Anthony' }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $survey->client_email ?? 'Anthony@hotmail.com' }}</dd></div>
                        <div><dt>Phone</dt><dd>{{ $survey->client_phone ?? '07901333164' }}</dd></div>
                        <div><dt>Home Address</dt><dd>{{ $survey->client_address ?? '66 Home Road, Kent' }}</dd></div>
                    </dl>
                </div>
            </article>

            <article class="survey-detail-card">
                <header class="survey-detail-card-header">
                    <h3>Property Information</h3>
                </header>
                <div class="survey-detail-card-body">
                    <dl class="survey-detail-datalist">
                        <div><dt>Full Address</dt><dd>{{ $survey->full_address ?? '66 Sample Street, Kent' }}</dd></div>
                        <div><dt>Postcode</dt><dd>{{ $survey->postcode ?? 'DA9 9XZ' }}</dd></div>
                        <div><dt>Access Contact</dt><dd>{{ $survey->access_contact ?? 'Anthony' }}</dd></div>
                        <div><dt>Access Role</dt><dd>{{ $survey->access_role ?? 'Vendor' }}</dd></div>
                    </dl>
                </div>
            </article>
        </div>
    </section>

    <section class="survey-detail-section">
        <div class="survey-detail-grid survey-detail-grid--two">
            <article class="survey-detail-card survey-detail-card--profile">
                <header class="survey-detail-card-header">
                    <h3>Property Profile</h3>
                    <div class="survey-detail-card-subtitle">Snapshot of the dwelling</div>
                </header>
                <div class="survey-detail-card-body">
                    <div class="survey-detail-profile-row">
                        <div class="survey-detail-profile-field">
                            <label>Property Type</label>
                            <div class="survey-detail-profile-value">{{ $survey->property_type ?? 'House' }}</div>
                        </div>
                        <div class="survey-detail-profile-field">
                            <label>Estate Holding</label>
                            <div class="survey-detail-profile-value">{{ $survey->estate_holding ?? 'Freehold' }}</div>
                        </div>
                    </div>
                    <div class="survey-detail-counts">
                        <div class="survey-detail-count">
                            <span class="survey-detail-count-label">Beds</span>
                            <span class="survey-detail-count-pill">{{ $survey->beds ?? 2 }}</span>
                        </div>
                        <div class="survey-detail-count">
                            <span class="survey-detail-count-label">Baths</span>
                            <span class="survey-detail-count-pill">{{ $survey->baths ?? 2 }}</span>
                        </div>
                        <div class="survey-detail-count">
                            <span class="survey-detail-count-label">Receptions</span>
                            <span class="survey-detail-count-pill">{{ $survey->receptions ?? 1 }}</span>
                        </div>
                        <div class="survey-detail-count">
                            <span class="survey-detail-count-label">Garage</span>
                            <span class="survey-detail-count-pill">{{ $survey->garage ?? 2 }}</span>
                        </div>
                        <div class="survey-detail-count">
                            <span class="survey-detail-count-label">WC</span>
                            <span class="survey-detail-count-pill">{{ $survey->wc ?? 0 }}</span>
                        </div>
                        <div class="survey-detail-count">
                            <span class="survey-detail-count-label">Utility</span>
                            <span class="survey-detail-count-pill">{{ $survey->utility ?? 2 }}</span>
                        </div>
                        <div class="survey-detail-count">
                            <span class="survey-detail-count-label">Garden</span>
                            <span class="survey-detail-count-pill">{{ $survey->garden ?? 'Y' }}</span>
                        </div>
                    </div>
                </div>
            </article>

            <article class="survey-detail-card" id="survey-desk-study">
                <header class="survey-detail-card-header survey-detail-card-header--inline">
                    <h3>Case Notes</h3>
                    <button type="button" class="survey-detail-card-action">
                        <i class="fas fa-plus"></i>
                    </button>
                </header>
                <div class="survey-detail-card-body">
                    <ul class="survey-detail-timeline">
                        <li>
                            <span class="survey-detail-timeline-date">10/10/2025 &middot; 5:00pm</span>
                            <p>Spoke to the customer and advised that the property has damp and will require extensive work.</p>
                        </li>
                        <li>
                            <span class="survey-detail-timeline-date">15/10/2025 &middot; 5:00pm</span>
                            <p>Spoke to the customer and advised that the property has damp and will require extensive work.</p>
                        </li>
                    </ul>
                </div>
            </article>
        </div>
    </section>

    <section class="survey-detail-section survey-detail-section--split" id="survey-data">
        <article class="survey-detail-card">
            <header class="survey-detail-card-header">
                <h3>Client Concerns</h3>
            </header>
            <div class="survey-detail-card-body">
                <p class="survey-detail-body-text">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris dapibus, lacus sed efficitur
                    suscipit, nisl metus maximus erat, eget tincidunt felis arcu nec odio. Pellentesque habitant morbi.
                </p>
            </div>
        </article>
        <article class="survey-detail-card" id="survey-media">
            <header class="survey-detail-card-header">
                <h3>Next Steps</h3>
            </header>
            <div class="survey-detail-card-body">
                <ul class="survey-detail-list">
                    <li>Arrange access for roof inspection</li>
                    <li>Confirm damp remedial strategy with contractor</li>
                    <li>Prepare draft report for QA by 20/10/2025</li>
                </ul>
            </div>
        </article>
    </section>

    <section class="survey-detail-section" id="survey-transcript">
        <article class="survey-detail-card">
            <header class="survey-detail-card-header">
                <h3>Transcript</h3>
            </header>
            <div class="survey-detail-card-body">
                <p class="survey-detail-body-text">No transcript uploaded yet.</p>
            </div>
        </article>
    </section>

    <section class="survey-detail-section" id="survey-documents">
        <article class="survey-detail-card">
            <header class="survey-detail-card-header">
                <h3>Documents</h3>
            </header>
            <div class="survey-detail-card-body">
                <button type="button" class="survey-detail-upload-btn">
                    <i class="fas fa-upload"></i>
                    <span>Upload Supporting Document</span>
                </button>
                <p class="survey-detail-body-text">Drag and drop files, or browse to upload project documents.</p>
            </div>
        </article>
    </section>
</div>
@endsection
