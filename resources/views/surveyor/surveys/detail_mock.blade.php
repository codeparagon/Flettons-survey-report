@extends('layouts.survey')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Survey Detail Mock')

@section('content')
<form id="survey-detail-form" class="survey-detail-mock" autocomplete="off">
    <div class="survey-detail-mock__header">
        <div class="survey-detail-mock__address">{{ $detail['address'] }}</div>
        <div class="survey-detail-mock__job-ref">
            <span>Job Reference</span>
            <strong>{{ $detail['job_reference'] }}</strong>
        </div>
    </div>

    <div class="survey-detail-mock__grid">
        <section class="survey-detail-card">
            <div class="survey-detail-card__header">
                <h2>Client Information</h2>
            </div>
            <div class="survey-detail-card__body survey-detail-card__body--grid">
                <label class="survey-detail-field">
                    <span>Full Name</span>
                    <input type="text" name="client[full_name]" value="{{ $detail['client']['full_name'] }}">
                </label>
                <label class="survey-detail-field">
                    <span>Email</span>
                    <input type="email" name="client[email]" value="{{ $detail['client']['email'] }}">
                </label>
                <label class="survey-detail-field">
                    <span>Phone</span>
                    <input type="tel" name="client[phone]" value="{{ $detail['client']['phone'] }}">
                </label>
                <label class="survey-detail-field survey-detail-field--span">
                    <span>Home Address</span>
                    <input type="text" name="client[home_address]" value="{{ $detail['client']['home_address'] }}">
                </label>
            </div>
        </section>

        <section class="survey-detail-card">
            <div class="survey-detail-card__header">
                <h2>Property Information</h2>
            </div>
            <div class="survey-detail-card__body survey-detail-card__body--grid">
                <label class="survey-detail-field survey-detail-field--span">
                    <span>Full Address</span>
                    <input type="text" name="property[full_address]" value="{{ $detail['property']['full_address'] }}">
                </label>
                <label class="survey-detail-field">
                    <span>Postcode</span>
                    <input type="text" name="property[postcode]" value="{{ $detail['property']['postcode'] }}">
                </label>
                <label class="survey-detail-field">
                    <span>Access Contact</span>
                    <input type="text" name="property[access_contact]" value="{{ $detail['property']['access_contact'] }}">
                </label>
                <label class="survey-detail-field">
                    <span>Access Role</span>
                    <select name="property[access_role]" class="survey-detail-select">
                        @foreach($detail['property']['access_role_options'] as $role)
                            <option value="{{ $role }}" {{ $role === $detail['property']['access_role'] ? 'selected' : '' }}>{{ $role }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <section class="survey-detail-card">
            <div class="survey-detail-card__header survey-detail-card__header--with-select">
                <h2>Property Type</h2>
                <select class="survey-detail-select" name="property[type]">
                    @foreach($detail['property']['type_options'] as $type)
                        <option value="{{ $type }}" {{ $type === $detail['property']['type'] ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="survey-detail-card__body">
                <ul class="survey-detail-pill-grid">
                    @foreach($detail['property']['stats'] as $stat)
                        @php
                            $fieldName = $stat['name'] ?? Str::slug($stat['label'], '_');
                            $fieldType = $stat['type'] ?? 'text';
                        @endphp
                        <li class="survey-detail-pill">
                            <span class="survey-detail-pill__label">{{ $stat['label'] }}</span>
                            @if($fieldType === 'select')
                                <select name="property_stats[{{ $fieldName }}]" class="survey-detail-select survey-detail-select--pill">
                                    @foreach($stat['options'] as $value => $text)
                                        <option value="{{ $value }}" {{ $value == $stat['value'] ? 'selected' : '' }}>{{ $text }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="{{ in_array($fieldType, ['number', 'text']) ? $fieldType : 'text' }}"
                                    name="property_stats[{{ $fieldName }}]"
                                    value="{{ $stat['value'] }}"
                                    @if(isset($stat['min'])) min="{{ $stat['min'] }}" @endif
                                    class="survey-detail-pill__input"
                                >
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="survey-detail-card survey-detail-card--notes">
            <div class="survey-detail-card__header">
                <h2>Case Notes</h2>
                <button type="button" class="survey-detail-icon-btn" data-add-note title="Add Note">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div class="survey-detail-card__body" data-notes>
                @foreach($detail['case_notes'] as $index => $note)
                    <article class="survey-detail-note" data-note>
                        <div class="survey-detail-note__header">
                            <span class="survey-detail-note__timestamp">{{ $note['timestamp'] }}</span>
                            <button type="button" class="survey-detail-icon-btn survey-detail-icon-btn--ghost" data-remove-note title="Remove note">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <textarea name="case_notes[{{ $index }}]" rows="3">{{ $note['body'] }}</textarea>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="survey-detail-card survey-detail-card--concerns">
            <div class="survey-detail-card__header">
                <h2>Client Concerns</h2>
            </div>
            <div class="survey-detail-card__body">
                <textarea name="client[concerns]" rows="4">{{ $detail['client']['concerns'] }}</textarea>
            </div>
        </section>
    </div>

    <div class="survey-detail-actions">
        <button type="button" class="survey-detail-btn survey-detail-btn--secondary" id="survey-detail-reset">Discard Changes</button>
        <button type="submit" class="survey-detail-btn survey-detail-btn--primary" id="survey-detail-save">Save Changes</button>
    </div>
</form>

<template id="survey-detail-note-template">
    <article class="survey-detail-note" data-note>
        <div class="survey-detail-note__header">
            <span class="survey-detail-note__timestamp">New Note</span>
            <button type="button" class="survey-detail-icon-btn survey-detail-icon-btn--ghost" data-remove-note title="Remove note">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <textarea name="case_notes_new[]" rows="3" placeholder="Enter note..."></textarea>
    </article>
</template>
@endsection

@push('styles')
<style>
    .survey-detail-mock {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .survey-detail-mock__header {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #111827, #1E293B);
        color: #F8FAFC;
        padding: 1.25rem 1.75rem;
        border-radius: 18px;
        box-shadow: 0 20px 50px -28px rgba(15, 23, 42, 0.7);
    }

    .survey-detail-mock__address {
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .survey-detail-mock__job-ref {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        text-align: right;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .survey-detail-mock__job-ref strong {
        font-size: 1.2rem;
    }

    .survey-detail-mock__grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1.75rem;
    }

    .survey-detail-card {
        grid-column: span 6;
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.75);
        box-shadow: 0 28px 60px -40px rgba(15, 23, 42, 0.35);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-width: 0;
    }

    .survey-detail-card__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.35rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.7);
    }

    .survey-detail-card__header h2 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0F172A;
        letter-spacing: 0.01em;
    }

    .survey-detail-card__body {
        padding: 1.35rem;
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
    }

    .survey-detail-card__body--grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.15rem 1.35rem;
    }

    .survey-detail-field {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        font-size: 0.9rem;
        color: #1F2937;
    }

    .survey-detail-field--span {
        grid-column: span 2;
    }

    .survey-detail-field input,
    .survey-detail-card textarea,
    .survey-detail-select {
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.45);
        padding: 0.7rem 0.9rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #0F172A;
        background: #F9FAFB;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .survey-detail-field input:focus,
    .survey-detail-card textarea:focus,
    .survey-detail-select:focus {
        outline: none;
        border-color: rgba(193, 236, 74, 0.85);
        box-shadow: 0 0 0 3px rgba(193, 236, 74, 0.18);
        background: #FFFFFF;
    }

    .survey-detail-select {
        background: #FFFFFF;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24'%3E%3Cpath fill='%230f172a' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        padding-right: 2.25rem;
    }

    .survey-detail-pill-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 1rem;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .survey-detail-pill {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        align-items: stretch;
        padding: 0.9rem;
        border-radius: 14px;
        background: #F1F5F9;
        border: 1px solid rgba(148, 163, 184, 0.35);
    }

    .survey-detail-pill__label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #475569;
        text-align: center;
    }

    .survey-detail-pill__input,
    .survey-detail-select--pill {
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.45);
        padding: 0.55rem 0.75rem;
        font-size: 1.05rem;
        font-weight: 700;
        text-align: center;
        color: #0F172A;
        background: #FFFFFF;
    }

    .survey-detail-select--pill {
        background-position: right 0.65rem center;
        padding-right: 2rem;
    }

    .survey-detail-card--notes .survey-detail-card__body {
        gap: 1.25rem;
    }

    .survey-detail-note {
        background: #F8FAFC;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 1rem 1.15rem 1.1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .survey-detail-note__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .survey-detail-note__timestamp {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1F2937;
    }

    .survey-detail-note textarea {
        min-height: 110px;
        resize: vertical;
    }

    .survey-detail-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.4);
        background: #FFFFFF;
        color: #111827;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .survey-detail-icon-btn:hover {
        background: rgba(193, 236, 74, 0.3);
        border-color: rgba(193, 236, 74, 0.85);
    }

    .survey-detail-icon-btn--ghost {
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: transparent;
        color: #EF4444;
    }

    .survey-detail-icon-btn--ghost:hover {
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.5);
    }

    .survey-detail-card textarea {
        background: #FFFFFF;
        min-height: 130px;
    }

    .survey-detail-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-right: 0.5rem;
        flex-wrap: wrap;
    }

    .survey-detail-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.85rem 1.65rem;
        border-radius: 12px;
        border: none;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .survey-detail-btn--secondary {
        background: #F1F5F9;
        color: #1F2937;
        border: 1px solid rgba(148, 163, 184, 0.4);
    }

    .survey-detail-btn--secondary:hover {
        background: #E2E8F0;
    }

    .survey-detail-btn--primary {
        background: #111827;
        color: #F8FAFC;
        border: 1px solid transparent;
        box-shadow: 0 20px 32px -24px rgba(15, 23, 42, 0.6);
    }

    .survey-detail-btn--primary:hover {
        background: #1E293B;
    }

    @media (max-width: 1200px) {
        .survey-detail-card {
            grid-column: span 12;
        }

        .survey-detail-card__body--grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .survey-detail-field--span {
            grid-column: span 2;
        }
    }

    @media (max-width: 900px) {
        .survey-detail-card__body--grid {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .survey-detail-field--span {
            grid-column: span 1;
        }

        .survey-detail-mock__grid {
            gap: 1.5rem;
        }
    }

    @media (max-width: 640px) {
        .survey-detail-mock__header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1.25rem;
        }

        .survey-detail-mock__job-ref {
            text-align: left;
        }

        .survey-detail-card__header {
            flex-direction: column;
            align-items: flex-start;
        }

        .survey-detail-select {
            width: 100%;
        }

        .survey-detail-pill-grid {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        }

        .survey-detail-actions {
            flex-direction: column;
            align-items: stretch;
            padding: 0;
        }

        .survey-detail-btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('survey-detail-form');
        const addNoteBtn = document.querySelector('[data-add-note]');
        const notesContainer = document.querySelector('[data-notes]');
        const template = document.getElementById('survey-detail-note-template');
        const resetBtn = document.getElementById('survey-detail-reset');
        const originalNotesMarkup = notesContainer ? notesContainer.innerHTML : '';

        const timestampFormatter = new Intl.DateTimeFormat('en-GB', {
            dateStyle: 'short',
            timeStyle: 'short'
        });

        const bindRemoveHandler = (noteEl) => {
            const removeBtn = noteEl.querySelector('[data-remove-note]');
            const textarea = noteEl.querySelector('textarea');
            if (!removeBtn) return;
            removeBtn.addEventListener('click', () => {
                if (textarea && textarea.value.trim().length > 0) {
                    const confirmed = window.confirm('Remove this note? Unsaved text will be lost.');
                    if (!confirmed) {
                        return;
                    }
                }
                noteEl.remove();
            });
        };

        const refreshRemoveHandlers = () => {
            notesContainer.querySelectorAll('[data-note]').forEach(bindRemoveHandler);
        };

        const addNote = () => {
            if (!template || !notesContainer) return;
            const clone = template.content.cloneNode(true);
            const noteEl = clone.querySelector('[data-note]');
            const timestampEl = clone.querySelector('.survey-detail-note__timestamp');
            const textarea = clone.querySelector('textarea');
            if (timestampEl) {
                timestampEl.textContent = `New Note · ${timestampFormatter.format(new Date())}`;
            }
            if (textarea) {
                textarea.focus();
            }
            notesContainer.appendChild(clone);
            const appended = notesContainer.lastElementChild;
            if (appended) {
                bindRemoveHandler(appended);
            }
        };

        if (addNoteBtn) {
            addNoteBtn.addEventListener('click', addNote);
        }

        if (resetBtn && form && notesContainer) {
            resetBtn.addEventListener('click', () => {
                form.reset();
                notesContainer.innerHTML = originalNotesMarkup;
                refreshRemoveHandlers();
            });
        }

        if (form) {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                window.alert('Mock save only – no changes persisted.');
            });
        }

        if (notesContainer) {
            refreshRemoveHandlers();
        }
    });
</script>
@endpush
