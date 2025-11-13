@extends('layouts.survey-mock')

@section('title', 'Survey Details')

@section('content')
<div class="survey-detail-mock-content">
    <!-- Integrated Header Bar -->
    <div class="survey-detail-mock-header-bar">
        <div class="survey-detail-mock-header-left">
            <a href="{{ route('surveyor.surveys.index') }}" class="survey-detail-mock-back">
                <i class="fas fa-chevron-left"></i>
                <span>{{ $survey->full_address ?? '123, Sample Street, Kent DA9 9ZT' }}</span>
            </a>
        </div>
        <div class="survey-detail-mock-header-right">
            <div class="survey-detail-mock-jobref">
                <span class="survey-detail-mock-jobref-label">Job Reference</span>
                <span class="survey-detail-mock-jobref-value">{{ $survey->job_reference ?? '12SE39DT-SH' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="survey-detail-mock-body">
        <section class="survey-detail-mock-section">
            <div class="survey-detail-mock-grid">
                <article class="survey-detail-mock-card">
                    <header class="survey-detail-mock-card-header">
                        <h3>Client Information</h3>
                    </header>
                    <div class="survey-detail-mock-card-body">
                        <dl class="survey-detail-mock-datalist">
                            <div class="survey-detail-mock-field-row">
                                <dt>Full Name</dt>
                                <dd class="survey-detail-mock-editable" data-field="client_name" data-original="{{ $survey->client_name ?? 'Anthony' }}">
                                    <span class="survey-detail-mock-value">{{ $survey->client_name ?? 'Anthony' }}</span>
                                    <input type="text" class="survey-detail-mock-input" value="{{ $survey->client_name ?? 'Anthony' }}" style="display: none;">
                                    <button type="button" class="survey-detail-mock-edit-btn" title="Edit" style="display: inline-flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </dd>
                            </div>
                            <div class="survey-detail-mock-field-row">
                                <dt>Email</dt>
                                <dd class="survey-detail-mock-editable" data-field="client_email" data-original="{{ $survey->client_email ?? 'Anthony@hotmail.com' }}">
                                    <span class="survey-detail-mock-value">{{ $survey->client_email ?? 'Anthony@hotmail.com' }}</span>
                                    <input type="email" class="survey-detail-mock-input" value="{{ $survey->client_email ?? 'Anthony@hotmail.com' }}" style="display: none;">
                                    <button type="button" class="survey-detail-mock-edit-btn" title="Edit" style="display: inline-flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </dd>
                            </div>
                            <div class="survey-detail-mock-field-row">
                                <dt>Phone</dt>
                                <dd class="survey-detail-mock-editable" data-field="phone" data-original="{{ $survey->inf_field_Phone1 ?? $survey->telephone_number ?? '07901333164' }}">
                                    <span class="survey-detail-mock-value">{{ $survey->inf_field_Phone1 ?? $survey->telephone_number ?? '07901333164' }}</span>
                                    <input type="tel" class="survey-detail-mock-input" value="{{ $survey->inf_field_Phone1 ?? $survey->telephone_number ?? '07901333164' }}" style="display: none;">
                                    <button type="button" class="survey-detail-mock-edit-btn" title="Edit" style="display: inline-flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </dd>
                            </div>
                            <div class="survey-detail-mock-field-row">
                                <dt>Home Address</dt>
                                <dd class="survey-detail-mock-editable" data-field="home_address" data-original="{{ $survey->inf_field_Address2Street1 ?? ($survey->inf_field_Address1Street1 ?? '66 Home Road, Kent') }}">
                                    <span class="survey-detail-mock-value">{{ $survey->inf_field_Address2Street1 ?? ($survey->inf_field_Address1Street1 ?? '66 Home Road, Kent') }}</span>
                                    <input type="text" class="survey-detail-mock-input" value="{{ $survey->inf_field_Address2Street1 ?? ($survey->inf_field_Address1Street1 ?? '66 Home Road, Kent') }}" style="display: none;">
                                    <button type="button" class="survey-detail-mock-edit-btn" title="Edit" style="display: inline-flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </article>

                <article class="survey-detail-mock-card">
                    <header class="survey-detail-mock-card-header">
                        <h3>Property Information</h3>
                    </header>
                    <div class="survey-detail-mock-card-body">
                        <dl class="survey-detail-mock-datalist">
                            <div class="survey-detail-mock-field-row">
                                <dt>Full Address</dt>
                                <dd class="survey-detail-mock-editable" data-field="full_address" data-original="{{ $survey->full_address ?? '66 Sample Street, kent' }}">
                                    <span class="survey-detail-mock-value">{{ $survey->full_address ?? '66 Sample Street, kent' }}</span>
                                    <input type="text" class="survey-detail-mock-input" value="{{ $survey->full_address ?? '66 Sample Street, kent' }}" style="display: none;">
                                    <button type="button" class="survey-detail-mock-edit-btn" title="Edit" style="display: inline-flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </dd>
                            </div>
                            <div class="survey-detail-mock-field-row">
                                <dt>Postcode</dt>
                                <dd class="survey-detail-mock-editable" data-field="postcode" data-original="{{ $survey->postcode ?? $survey->inf_field_PostalCode ?? 'DA9 9XZ' }}">
                                    <span class="survey-detail-mock-value">{{ $survey->postcode ?? $survey->inf_field_PostalCode ?? 'DA9 9XZ' }}</span>
                                    <input type="text" class="survey-detail-mock-input" value="{{ $survey->postcode ?? $survey->inf_field_PostalCode ?? 'DA9 9XZ' }}" style="display: none;">
                                    <button type="button" class="survey-detail-mock-edit-btn" title="Edit" style="display: inline-flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </dd>
                            </div>
                            <div class="survey-detail-mock-field-row">
                                <dt>Access Contact</dt>
                                <dd class="survey-detail-mock-editable" data-field="access_contact" data-original="{{ $survey->client_name ?? 'Anthony' }}">
                                    <span class="survey-detail-mock-value">{{ $survey->client_name ?? 'Anthony' }}</span>
                                    <input type="text" class="survey-detail-mock-input" value="{{ $survey->client_name ?? 'Anthony' }}" style="display: none;">
                                    <button type="button" class="survey-detail-mock-edit-btn" title="Edit" style="display: inline-flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </dd>
                            </div>
                            <div class="survey-detail-mock-field-row">
                                <dt>Access Role</dt>
                                <dd>
                                    <select class="survey-detail-mock-select">
                                        <option {{ ($survey->access_role ?? 'Vendor') === 'Vendor' ? 'selected' : '' }}>Vendor</option>
                                        <option {{ ($survey->access_role ?? '') === 'Agent' ? 'selected' : '' }}>Agent</option>
                                        <option {{ ($survey->access_role ?? '') === 'Tenant' ? 'selected' : '' }}>Tenant</option>
                                    </select>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </article>
            </div>
        </section>

        <section class="survey-detail-mock-section">
            <div class="survey-detail-mock-grid">
                <article class="survey-detail-mock-card">
                    <header class="survey-detail-mock-card-header">
                        <h3>Property Type</h3>
                    </header>
                    <div class="survey-detail-mock-card-body">
                        <div class="survey-detail-mock-field">
                            <label>Property Type</label>
                            <select class="survey-detail-mock-select">
                                <option {{ ($survey->house_or_flat ?? 'House') === 'House' ? 'selected' : '' }}>House</option>
                                <option {{ ($survey->house_or_flat ?? '') === 'Flat' ? 'selected' : '' }}>Flat</option>
                                <option {{ ($survey->house_or_flat ?? '') === 'Bungalow' ? 'selected' : '' }}>Bungalow</option>
                            </select>
                        </div>
                        <div class="survey-detail-mock-counts">
                            <div class="survey-detail-mock-count">
                                <span class="survey-detail-mock-count-label">Beds</span>
                                <span class="survey-detail-mock-count-value editable-count" data-field="beds" data-original="{{ $survey->number_of_bedrooms ?? 2 }}">
                                    <span class="count-display">{{ $survey->number_of_bedrooms ?? 2 }}</span>
                                    <input type="number" class="count-input" value="{{ $survey->number_of_bedrooms ?? 2 }}" style="display: none;" min="0" max="20">
                                    <button type="button" class="survey-detail-mock-edit-btn count-edit-btn" title="Edit" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="survey-detail-mock-count">
                                <span class="survey-detail-mock-count-label">Baths</span>
                                <span class="survey-detail-mock-count-value editable-count" data-field="baths" data-original="{{ $survey->bathrooms ?? 2 }}">
                                    <span class="count-display">{{ $survey->bathrooms ?? 2 }}</span>
                                    <input type="number" class="count-input" value="{{ $survey->bathrooms ?? 2 }}" style="display: none;" min="0" max="20">
                                    <button type="button" class="survey-detail-mock-edit-btn count-edit-btn" title="Edit" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="survey-detail-mock-count">
                                <span class="survey-detail-mock-count-label">Receptions</span>
                                <span class="survey-detail-mock-count-value editable-count" data-field="receptions" data-original="{{ $survey->receptions ?? 1 }}">
                                    <span class="count-display">{{ $survey->receptions ?? 1 }}</span>
                                    <input type="number" class="count-input" value="{{ $survey->receptions ?? 1 }}" style="display: none;" min="0" max="20">
                                    <button type="button" class="survey-detail-mock-edit-btn count-edit-btn" title="Edit" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="survey-detail-mock-count">
                                <span class="survey-detail-mock-count-label">Garage</span>
                                <span class="survey-detail-mock-count-value editable-count" data-field="garage" data-original="-">
                                    <span class="count-display">-</span>
                                    <input type="number" class="count-input" value="" style="display: none;" min="0" max="20" placeholder="-">
                                    <button type="button" class="survey-detail-mock-edit-btn count-edit-btn" title="Edit" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="survey-detail-mock-count">
                                <span class="survey-detail-mock-count-label">WC</span>
                                <span class="survey-detail-mock-count-value editable-count" data-field="wc" data-original="-">
                                    <span class="count-display">-</span>
                                    <input type="number" class="count-input" value="" style="display: none;" min="0" max="20" placeholder="-">
                                    <button type="button" class="survey-detail-mock-edit-btn count-edit-btn" title="Edit" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="survey-detail-mock-count">
                                <span class="survey-detail-mock-count-label">Utility</span>
                                <span class="survey-detail-mock-count-value editable-count" data-field="utility" data-original="-">
                                    <span class="count-display">-</span>
                                    <input type="number" class="count-input" value="" style="display: none;" min="0" max="20" placeholder="-">
                                    <button type="button" class="survey-detail-mock-edit-btn count-edit-btn" title="Edit" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="survey-detail-mock-count">
                                <span class="survey-detail-mock-count-label">Garden</span>
                                <span class="survey-detail-mock-count-value editable-count" data-field="garden" data-original="-">
                                    <span class="count-display">-</span>
                                    <input type="text" class="count-input" value="" style="display: none;" maxlength="10" placeholder="-">
                                    <button type="button" class="survey-detail-mock-edit-btn count-edit-btn" title="Edit" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="survey-detail-mock-card">
                    <header class="survey-detail-mock-card-header survey-detail-mock-card-header--inline">
                        <h3>Case Notes</h3>
                        <button type="button" class="survey-detail-mock-card-action" id="add-case-note-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                    </header>
                    <div class="survey-detail-mock-card-body">
                        <ul class="survey-detail-mock-timeline" id="case-notes-list">
                            <li class="survey-detail-mock-note-item" data-note-id="1">
                                <div class="survey-detail-mock-note-header">
                                    <span class="survey-detail-mock-timeline-date">10/10/2025: 5:00pm</span>
                                    <button type="button" class="survey-detail-mock-note-edit-btn" title="Edit note" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </div>
                                <p class="survey-detail-mock-note-text">Spoke to the customer and advised that the property has damp and will require extensive work</p>
                                <textarea class="survey-detail-mock-note-input" style="display: none;">Spoke to the customer and advised that the property has damp and will require extensive work</textarea>
                            </li>
                            <li class="survey-detail-mock-note-item" data-note-id="2">
                                <div class="survey-detail-mock-note-header">
                                    <span class="survey-detail-mock-timeline-date">15/10/2025: 5:00pm</span>
                                    <button type="button" class="survey-detail-mock-note-edit-btn" title="Edit note" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                                </div>
                                <p class="survey-detail-mock-note-text">Spoke to the customer and advised that the property has damp and will require extensive</p>
                                <textarea class="survey-detail-mock-note-input" style="display: none;">Spoke to the customer and advised that the property has damp and will require extensive</textarea>
                            </li>
                        </ul>
                        <!-- Add Note Form (Hidden by default) -->
                        <div class="survey-detail-mock-add-note-form" id="add-note-form" style="display: none;">
                            <div class="survey-detail-mock-add-note-field">
                                <label>Date & Time</label>
                                <input type="datetime-local" class="survey-detail-mock-input" id="new-note-datetime" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="survey-detail-mock-add-note-field">
                                <label>Note</label>
                                <textarea class="survey-detail-mock-input" id="new-note-text" rows="3" placeholder="Enter case note..."></textarea>
                            </div>
                            <div class="survey-detail-mock-add-note-actions">
                                <button type="button" class="survey-detail-mock-btn survey-detail-mock-btn-cancel" id="cancel-add-note">Cancel</button>
                                <button type="button" class="survey-detail-mock-btn survey-detail-mock-btn-save" id="save-add-note">Add Note</button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="survey-detail-mock-section">
            <article class="survey-detail-mock-card">
                <header class="survey-detail-mock-card-header survey-detail-mock-card-header--inline">
                    <h3>Client Concerns</h3>
                        <button type="button" class="survey-detail-mock-card-action" id="add-concern-btn">
                            <i class="fas fa-plus"></i>
                        </button>
                </header>
                <div class="survey-detail-mock-card-body">
                    <div class="survey-detail-mock-concerns-editable" data-field="client_concerns" data-original="{{ $survey->client_concerns ?? 'I want the surveyor to check the roof and' }}">
                        <p class="survey-detail-mock-concerns-text">{{ $survey->client_concerns ?? 'I want the surveyor to check the roof and' }}</p>
                        <textarea class="survey-detail-mock-concerns-input" style="display: none;" rows="4">{{ $survey->client_concerns ?? 'I want the surveyor to check the roof and' }}</textarea>
                        <button type="button" class="survey-detail-mock-edit-btn" title="Edit concerns" style="display: inline-flex !important; visibility: visible !important;">
                            <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                        </button>
                    </div>
                </div>
            </article>
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Poppins Font Family Throughout - Exclude Font Awesome icons */
    .survey-detail-mock-content {
        font-family: 'Poppins', sans-serif;
    }

    /* Ensure Font Awesome icons use correct font and are visible - MUST come after Poppins rule */
    .survey-detail-mock-content i,
    .survey-detail-mock-content i.fas,
    .survey-detail-mock-content i.far,
    .survey-detail-mock-content i.fab,
    .survey-detail-mock-content i[class*="fa-"],
    .survey-detail-mock-content [class*="fas fa-"],
    .survey-detail-mock-content [class*="far fa-"],
    .survey-detail-mock-content [class*="fab fa-"] {
        font-family: "Font Awesome 5 Free", "Font Awesome 5 Pro", "Font Awesome 6 Free", "Font Awesome 6 Pro", "FontAwesome" !important;
        font-weight: 900 !important;
        display: inline-block !important;
        font-style: normal !important;
        font-variant: normal !important;
        text-rendering: auto !important;
        line-height: 1 !important;
        speak: none !important;
        -webkit-font-smoothing: antialiased !important;
        -moz-osx-font-smoothing: grayscale !important;
    }
    
    .survey-detail-mock-card-header h3,
    .survey-detail-mock-datalist dt,
    .survey-detail-mock-count-label,
    .survey-detail-mock-field label,
    .survey-detail-mock-count-value,
    .survey-detail-mock-datalist dd,
    .survey-detail-mock-back,
    .survey-detail-mock-jobref-label,
    .survey-detail-mock-jobref-value {
        font-family: 'Poppins', sans-serif;
    }

    .survey-detail-mock-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
        padding: 0;
        gap: 0;
    }

    /* Integrated Header Bar */
    .survey-detail-mock-header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1.5rem;
        background: rgba(148, 163, 184, 0.08);
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
    }

    .survey-detail-mock-header-left {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .survey-detail-mock-back {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #C1EC4A;
        text-decoration: none;
        font-size: 16px;
        font-weight: 600;
    }

    .survey-detail-mock-back:hover {
        color: #A8D043;
    }

    .survey-detail-mock-back i {
        font-size: 14px;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-detail-mock-header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .survey-detail-mock-jobref {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.2rem;
    }

    .survey-detail-mock-jobref-label {
        font-size: 13px;
        color: #64748B;
        font-weight: 500;
    }

    .survey-detail-mock-jobref-value {
        font-size: 17px;
        color: #1A202C;
        font-weight: 700;
    }

    .survey-detail-mock-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        overflow-y: auto;
        padding: 1.25rem 1.5rem;
    }

    .survey-detail-mock-section {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .survey-detail-mock-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.25rem;
    }

    /* Cards - No Shadows, Minimal Border Radius */
    .survey-detail-mock-card {
        background: #FFFFFF;
        border-radius: 6px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .survey-detail-mock-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #FAFBFC;
    }

    .survey-detail-mock-card-header--inline {
        padding: 1rem 1.5rem;
    }

    .survey-detail-mock-card-header h3 {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        color: #1A202C;
    }

    .survey-detail-mock-card-action {
        background: none;
        border: none;
        color: #64748B;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .survey-detail-mock-card-action i {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-detail-mock-card-action:hover {
        background: rgba(148, 163, 184, 0.1);
        color: #1A202C;
    }

    .survey-detail-mock-card-body {
        padding: 1.25rem;
        flex: 1;
        overflow-y: auto;
    }

    .survey-detail-mock-datalist {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin: 0;
    }

    .survey-detail-mock-field-row {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        position: relative;
    }

    .survey-detail-mock-datalist dt {
        font-size: 13px;
        color: #64748B;
        font-weight: 600;
        text-transform: uppercase;
    }

    .survey-detail-mock-datalist dd {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
    }

    .survey-detail-mock-datalist dd .survey-detail-mock-edit-btn {
        flex-shrink: 0;
    }

    .survey-detail-mock-editable {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
    }

    .survey-detail-mock-value {
        font-size: 16px;
        color: #1A202C;
        font-weight: 600;
        flex: 1;
        min-height: 20px;
    }

    .survey-detail-mock-edit-btn {
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #64748B;
        cursor: pointer;
        padding: 0.4rem;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 13px;
        opacity: 1;
        flex-shrink: 0;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        position: relative;
    }

    .survey-detail-mock-edit-btn:hover {
        background: rgba(148, 163, 184, 0.2);
        border-color: rgba(148, 163, 184, 0.3);
        color: #475569;
    }

    .survey-detail-mock-edit-btn i.fa-pencil-alt {
        font-size: 13px !important;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }



    .survey-detail-mock-editable.editing .survey-detail-mock-edit-btn {
        display: none;
    }

    .survey-detail-mock-editable.editing .survey-detail-mock-value {
        display: none;
    }

    .survey-detail-mock-editable.editing .survey-detail-mock-input {
        display: block !important;
        flex: 1;
    }

    /* Inputs - No Shadows, Minimal Border Radius, Clean Styling */
    .survey-detail-mock-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 16px;
        font-weight: 600;
        color: #1A202C;
        background: #FFFFFF;
        transition: border-color 0.2s ease;
    }

    .survey-detail-mock-input:focus {
        outline: none;
        border-color: #C1EC4A;
        background: #FFFFFF;
    }

    .survey-detail-mock-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 16px;
        font-weight: 600;
        color: #1A202C;
        background: #FFFFFF;
        cursor: pointer;
        transition: border-color 0.2s ease;
    }

    .survey-detail-mock-select:focus {
        outline: none;
        border-color: #C1EC4A;
    }

    .survey-detail-mock-field {
        margin-bottom: 1.25rem;
    }

    .survey-detail-mock-field label {
        display: block;
        font-size: 13px;
        color: #64748B;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .survey-detail-mock-counts {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .survey-detail-mock-count {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
        min-width: 70px;
    }

    .survey-detail-mock-count-label {
        font-size: 13px;
        color: #64748B;
        font-weight: 600;
        text-transform: uppercase;
    }

    .survey-detail-mock-count-value {
        font-size: 20px;
        font-weight: 700;
        color: #1A202C;
        padding: 0.625rem 1rem;
        background: rgba(193, 236, 74, 0.12);
        border: 1px solid rgba(193, 236, 74, 0.3);
        border-radius: 4px;
        min-width: 60px;
        text-align: center;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .survey-detail-mock-count-value .count-display {
        display: block;
    }

    .survey-detail-mock-count-value.editing .count-display {
        display: none;
    }

    .survey-detail-mock-count-value.editing .count-edit-btn {
        display: none;
    }

    .survey-detail-mock-count-value.editing .count-input {
        display: block !important;
        width: 80px;
        text-align: center;
        padding: 0.625rem 0.5rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 18px;
        font-weight: 700;
        background: #FFFFFF;
    }

    .survey-detail-mock-count-value .count-input:focus {
        outline: none;
        border-color: #C1EC4A;
    }

    .survey-detail-mock-count-value .count-edit-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #FFFFFF;
        border: 1px solid rgba(148, 163, 184, 0.3);
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex !important;
        align-items: center;
        justify-content: center;
        padding: 0;
        font-size: 11px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .survey-detail-mock-count-value .count-edit-btn:hover {
        background: #F9FAFB;
        border-color: rgba(148, 163, 184, 0.5);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .survey-detail-mock-count-value .count-edit-btn i.fa-pencil-alt {
        font-size: 11px !important;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-detail-mock-timeline {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .survey-detail-mock-timeline li {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .survey-detail-mock-note-item {
        position: relative;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        margin-bottom: 1rem;
    }

    .survey-detail-mock-note-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .survey-detail-mock-note-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
    }

    .survey-detail-mock-note-edit-btn {
        background: rgba(148, 163, 184, 0.1);
        border: 1px solid rgba(148, 163, 184, 0.2);
        color: #64748B;
        cursor: pointer;
        padding: 0.35rem;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 12px;
        width: 26px;
        height: 26px;
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    .survey-detail-mock-note-edit-btn:hover {
        background: rgba(148, 163, 184, 0.2);
        border-color: rgba(148, 163, 184, 0.3);
        color: #475569;
    }

    .survey-detail-mock-note-edit-btn i.fa-pencil-alt {
        font-size: 12px !important;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .survey-detail-mock-note-text {
        margin: 0;
        font-size: 16px;
        color: #1A202C;
        line-height: 1.6;
        font-weight: 400;
    }

    .survey-detail-mock-note-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 16px;
        font-weight: 400;
        color: #1A202C;
        background: #FFFFFF;
        transition: border-color 0.2s ease;
        font-family: 'Poppins', sans-serif;
        resize: vertical;
        min-height: 80px;
    }

    .survey-detail-mock-note-input:focus {
        outline: none;
        border-color: #C1EC4A;
    }

    .survey-detail-mock-note-item.editing .survey-detail-mock-note-text {
        display: none;
    }

    .survey-detail-mock-note-item.editing .survey-detail-mock-note-edit-btn {
        display: none;
    }

    .survey-detail-mock-note-item.editing .survey-detail-mock-note-input {
        display: block !important;
    }

    .survey-detail-mock-add-note-form {
        padding-top: 1rem;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
        margin-top: 1rem;
    }

    .survey-detail-mock-add-note-field {
        margin-bottom: 1rem;
    }

    .survey-detail-mock-add-note-field label {
        display: block;
        font-size: 13px;
        color: #64748B;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .survey-detail-mock-add-note-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .survey-detail-mock-btn {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        border: 1px solid rgba(148, 163, 184, 0.4);
        background: #FFFFFF;
        color: #1A202C;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .survey-detail-mock-btn-cancel:hover {
        background: rgba(148, 163, 184, 0.1);
        border-color: rgba(148, 163, 184, 0.5);
    }

    .survey-detail-mock-btn-save {
        background: #C1EC4A;
        border-color: #C1EC4A;
        color: #1A202C;
    }

    .survey-detail-mock-btn-save:hover {
        background: #B0D93F;
        border-color: #B0D93F;
    }

    .survey-detail-mock-timeline-date {
        font-size: 13px;
        color: #64748B;
        font-weight: 600;
    }

    .survey-detail-mock-timeline p {
        margin: 0;
        font-size: 16px;
        color: #1A202C;
        line-height: 1.6;
        font-weight: 400;
    }

    .survey-detail-mock-concerns-editable {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .survey-detail-mock-concerns-text {
        margin: 0;
        font-size: 16px;
        color: #1A202C;
        line-height: 1.6;
        font-weight: 400;
    }

    .survey-detail-mock-concerns-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
        border-radius: 4px;
        font-size: 16px;
        font-weight: 400;
        color: #1A202C;
        background: #FFFFFF;
        transition: border-color 0.2s ease;
        font-family: 'Poppins', sans-serif;
        resize: vertical;
        min-height: 100px;
    }

    .survey-detail-mock-concerns-input:focus {
        outline: none;
        border-color: #C1EC4A;
    }

    .survey-detail-mock-concerns-editable .survey-detail-mock-edit-btn {
        align-self: flex-start;
    }

    .survey-detail-mock-concerns-editable.editing .survey-detail-mock-concerns-text {
        display: none;
    }

    .survey-detail-mock-concerns-editable.editing .survey-detail-mock-edit-btn {
        display: none;
    }

    .survey-detail-mock-concerns-editable.editing .survey-detail-mock-concerns-input {
        display: block !important;
    }

    .survey-detail-mock-text {
        margin: 0;
        font-size: 16px;
        color: #1A202C;
        line-height: 1.6;
        font-weight: 400;
    }

    @media (max-width: 1024px) {
        .survey-detail-mock-grid {
            grid-template-columns: 1fr;
        }
        
        .survey-detail-mock-body {
            padding: 1.5rem 1rem;
        }
        
        .survey-detail-mock-header-bar {
            padding: 0.75rem 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Inline editing for text fields
    $('.survey-detail-mock-editable').each(function() {
        const $editable = $(this);
        const $value = $editable.find('.survey-detail-mock-value');
        const $input = $editable.find('.survey-detail-mock-input');
        const $editBtn = $editable.find('.survey-detail-mock-edit-btn');
        const originalValue = $editable.data('original');

        $editBtn.on('click', function(e) {
            e.stopPropagation();
            enterEditMode($editable, $value, $input);
        });

        $input.on('blur', function() {
            saveField($editable, $value, $input, originalValue);
        });

        $input.on('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                saveField($editable, $value, $input, originalValue);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEdit($editable, $value, $input, originalValue);
            }
        });
    });

    // Inline editing for count values
    $('.editable-count').each(function() {
        const $count = $(this);
        const $display = $count.find('.count-display');
        const $input = $count.find('.count-input');
        const $editBtn = $count.find('.count-edit-btn');
        const originalValue = $count.data('original');

        $editBtn.on('click', function(e) {
            e.stopPropagation();
            enterEditModeCount($count, $display, $input);
        });

        $input.on('blur', function() {
            saveCountField($count, $display, $input, originalValue);
        });

        $input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveCountField($count, $display, $input, originalValue);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEditCount($count, $display, $input, originalValue);
            }
        });
    });

    // Case Notes editing
    $('.survey-detail-mock-note-item').each(function() {
        const $noteItem = $(this);
        const $noteText = $noteItem.find('.survey-detail-mock-note-text');
        const $noteInput = $noteItem.find('.survey-detail-mock-note-input');
        const $editBtn = $noteItem.find('.survey-detail-mock-note-edit-btn');
        const originalText = $noteInput.val();

        $editBtn.on('click', function(e) {
            e.stopPropagation();
            $noteItem.addClass('editing');
            $noteInput.focus();
            $noteInput.select();
        });

        $noteInput.on('blur', function() {
            saveNote($noteItem, $noteText, $noteInput);
        });

        $noteInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                cancelNoteEdit($noteItem, $noteText, $noteInput, originalText);
            }
        });
    });

    // Client Concerns editing
    $('.survey-detail-mock-concerns-editable').each(function() {
        const $concerns = $(this);
        const $concernsText = $concerns.find('.survey-detail-mock-concerns-text');
        const $concernsInput = $concerns.find('.survey-detail-mock-concerns-input');
        const $editBtn = $concerns.find('.survey-detail-mock-edit-btn');
        const originalText = $concerns.data('original');

        $editBtn.on('click', function(e) {
            e.stopPropagation();
            $concerns.addClass('editing');
            $concernsInput.focus();
            $concernsInput.select();
        });

        $concernsInput.on('blur', function() {
            saveConcerns($concerns, $concernsText, $concernsInput);
        });

        $concernsInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                cancelConcernsEdit($concerns, $concernsText, $concernsInput, originalText);
            }
        });
    });

    // Add Case Note functionality
    const $addNoteBtn = $('#add-case-note-btn');
    const $addNoteForm = $('#add-note-form');
    const $cancelAddNote = $('#cancel-add-note');
    const $saveAddNote = $('#save-add-note');
    const $caseNotesList = $('#case-notes-list');

    $addNoteBtn.on('click', function() {
        $addNoteForm.slideDown(200);
        $('#new-note-datetime').focus();
    });

    $cancelAddNote.on('click', function() {
        $addNoteForm.slideUp(200);
        $('#new-note-datetime').val('{{ now()->format('Y-m-d\TH:i') }}');
        $('#new-note-text').val('');
    });

    $saveAddNote.on('click', function() {
        const datetime = $('#new-note-datetime').val();
        const noteText = $('#new-note-text').val().trim();
        
        if (!noteText) {
            alert('Please enter a note');
            return;
        }

        // Format datetime
        const dateObj = new Date(datetime);
        const formattedDate = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const formattedTime = dateObj.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });

        // Create new note item
        const noteId = Date.now();
        const $newNote = $(`
            <li class="survey-detail-mock-note-item" data-note-id="${noteId}">
                <div class="survey-detail-mock-note-header">
                    <span class="survey-detail-mock-timeline-date">${formattedDate}: ${formattedTime}</span>
                                    <button type="button" class="survey-detail-mock-note-edit-btn" title="Edit note" style="display: flex !important; visibility: visible !important;">
                                        <i class="fas fa-pencil-alt" style="display: inline-block !important; visibility: visible !important;"></i>
                                    </button>
                </div>
                <p class="survey-detail-mock-note-text">${noteText}</p>
                <textarea class="survey-detail-mock-note-input" style="display: none;">${noteText}</textarea>
            </li>
        `);

        // Add event handlers to new note
        const $noteText = $newNote.find('.survey-detail-mock-note-text');
        const $noteInput = $newNote.find('.survey-detail-mock-note-input');
        const $editBtn = $newNote.find('.survey-detail-mock-note-edit-btn');
        const originalText = noteText;

        $editBtn.on('click', function(e) {
            e.stopPropagation();
            $newNote.addClass('editing');
            $noteInput.focus();
            $noteInput.select();
        });

        $noteInput.on('blur', function() {
            saveNote($newNote, $noteText, $noteInput);
        });

        $noteInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                cancelNoteEdit($newNote, $noteText, $noteInput, originalText);
            }
        });

        // Prepend to list
        $caseNotesList.prepend($newNote);
        
        // Clear form and hide
        $('#new-note-datetime').val('{{ now()->format('Y-m-d\TH:i') }}');
        $('#new-note-text').val('');
        $addNoteForm.slideUp(200);
        
        console.log('Added new case note:', noteText);
    });

    function enterEditMode($editable, $value, $input) {
        $editable.addClass('editing');
        $input.focus();
        $input.select();
    }

    function enterEditModeCount($count, $display, $input) {
        $count.addClass('editing');
        $input.focus();
        $input.select();
    }

    function saveField($editable, $value, $input, originalValue) {
        const newValue = $input.val().trim();
        if (newValue !== '') {
            $value.text(newValue);
            $editable.data('original', newValue);
            // Here you would typically send to server via AJAX
            console.log('Saving field:', $editable.data('field'), '=', newValue);
        }
        $editable.removeClass('editing');
    }

    function saveCountField($count, $display, $input, originalValue) {
        let newValue = $input.val().trim();
        if (newValue === '' || newValue === null) {
            newValue = '-';
        }
        $display.text(newValue);
        $count.data('original', newValue);
        // Here you would typically send to server via AJAX
        console.log('Saving count field:', $count.data('field'), '=', newValue);
        $count.removeClass('editing');
    }

    function cancelEdit($editable, $value, $input, originalValue) {
        $input.val($editable.data('original'));
        $editable.removeClass('editing');
    }

    function cancelEditCount($count, $display, $input, originalValue) {
        const origValue = $count.data('original');
        $input.val(origValue === '-' ? '' : origValue);
        $count.removeClass('editing');
    }

    function saveNote($noteItem, $noteText, $noteInput) {
        const newText = $noteInput.val().trim();
        if (newText) {
            $noteText.text(newText);
            // Here you would typically send to server via AJAX
            console.log('Saving note:', $noteItem.data('note-id'), '=', newText);
        }
        $noteItem.removeClass('editing');
    }

    function cancelNoteEdit($noteItem, $noteText, $noteInput, originalText) {
        $noteInput.val($noteText.text());
        $noteItem.removeClass('editing');
    }

    function saveConcerns($concerns, $concernsText, $concernsInput) {
        const newText = $concernsInput.val().trim();
        if (newText) {
            $concernsText.text(newText);
            $concerns.data('original', newText);
            // Here you would typically send to server via AJAX
            console.log('Saving client concerns:', newText);
        }
        $concerns.removeClass('editing');
    }

    function cancelConcernsEdit($concerns, $concernsText, $concernsInput, originalText) {
        $concernsInput.val($concernsText.text());
        $concerns.removeClass('editing');
    }

    // Prevent editing multiple fields at once
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.survey-detail-mock-editable, .editable-count, .survey-detail-mock-note-item, .survey-detail-mock-concerns-editable, .survey-detail-mock-add-note-form').length) {
            $('.survey-detail-mock-editable.editing').each(function() {
                const $editable = $(this);
                const $value = $editable.find('.survey-detail-mock-value');
                const $input = $editable.find('.survey-detail-mock-input');
                const originalValue = $editable.data('original');
                saveField($editable, $value, $input, originalValue);
            });
            $('.editable-count.editing').each(function() {
                const $count = $(this);
                const $display = $count.find('.count-display');
                const $input = $count.find('.count-input');
                const originalValue = $count.data('original');
                saveCountField($count, $display, $input, originalValue);
            });
            $('.survey-detail-mock-note-item.editing').each(function() {
                const $noteItem = $(this);
                const $noteText = $noteItem.find('.survey-detail-mock-note-text');
                const $noteInput = $noteItem.find('.survey-detail-mock-note-input');
                saveNote($noteItem, $noteText, $noteInput);
            });
            $('.survey-detail-mock-concerns-editable.editing').each(function() {
                const $concerns = $(this);
                const $concernsText = $concerns.find('.survey-detail-mock-concerns-text');
                const $concernsInput = $concerns.find('.survey-detail-mock-concerns-input');
                saveConcerns($concerns, $concernsText, $concernsInput);
            });
        }
    });
});
</script>
@endpush
