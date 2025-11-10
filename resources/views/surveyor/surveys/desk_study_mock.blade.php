@extends('layouts.survey')

@section('title', 'Desk Study Mock')

@section('content')
<div class="desk-study">
    <header class="desk-study__header">
        <a href="{{ url()->previous() }}" class="desk-study__back" title="Back to Surveys">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="desk-study__address">{{ $deskStudy['address'] }}</div>
        <a href="{{ url('surveyor/surveys/detail-mock') }}" class="desk-study__nav">Survey Detail</a>
    </header>

    <div class="desk-study__grid">
        <section class="desk-card desk-card--map">
            <div class="desk-card__header">
                <h2>Map of Location</h2>
            </div>
            <div class="desk-card__body desk-card__body--map">
                <div class="desk-map" style="background-image: url('{{ $deskStudy['map']['image'] }}');"></div>
                <div class="desk-map__coords">
                    <div>
                        <span>Longitude</span>
                        <strong>{{ $deskStudy['map']['longitude'] }}</strong>
                    </div>
                    <div>
                        <span>Latitude</span>
                        <strong>{{ $deskStudy['map']['latitude'] }}</strong>
                    </div>
                </div>
            </div>
            <p class="desk-card__hint">The data on this page can be sourced from Ordnance Survey and OGL API</p>
        </section>

        <section class="desk-card desk-card--flood" data-category>
            <div class="desk-card__header">
                <h2>Flood Risks</h2>
            </div>
            <div class="desk-card__body desk-card__body--stack">
                @foreach($deskStudy['flood_risks'] as $risk)
                    <div class="desk-segment" data-segment>
                        <span class="desk-segment__label">{{ $risk['title'] }}</span>
                        <div class="desk-segment__options" role="group" aria-label="{{ $risk['title'] }}">
                            @foreach($risk['options'] as $option)
                                @php
                                    $active = $option === $risk['value'];
                                    $type = Str::contains($option, ['High', 'Medium', 'Low']) ? 'level' : 'binary';
                                @endphp
                                <button
                                    type="button"
                                    class="desk-pill desk-pill--{{ $type }} {{ $active ? 'is-active' : '' }}"
                                    data-pill
                                    data-value="{{ $option }}"
                                >
                                    {{ $option }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="desk-card desk-card--split">
            <div class="desk-card__row">
                <div class="desk-card__column">
                    <h3>Council Tax</h3>
                    <div class="desk-pill-row" data-segment>
                        @foreach($deskStudy['council_tax']['options'] as $option)
                            <button type="button" class="desk-pill desk-pill--alpha {{ $option === $deskStudy['council_tax']['value'] ? 'is-active' : '' }}" data-pill data-value="{{ $option }}">
                                {{ $option }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="desk-card__column">
                    <h3>Planning Matters</h3>
                    <button type="button" class="desk-card__icon" data-add-planning title="Add planning matter">
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="desk-planning" data-planning-list>
                        @foreach($deskStudy['planning']['items'] as $item)
                            <div class="desk-planning__row" data-planning-row>
                                <span class="desk-planning__label">{{ $item['label'] }}</span>
                                <div class="desk-planning__options" data-segment>
                                    @foreach($item['options'] as $option)
                                        <button type="button" class="desk-pill desk-pill--binary {{ $option === $item['value'] ? 'is-active' : '' }}" data-pill data-value="{{ $option }}">
                                            {{ $option }}
                                        </button>
                                    @endforeach
                                </div>
                                <button type="button" class="desk-card__icon desk-card__icon--ghost" data-remove-planning title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="desk-card__row">
                <div class="desk-card__column">
                    <h3>EPC Rating</h3>
                    <div class="desk-pill-row" data-segment>
                        @foreach($deskStudy['epc_rating']['options'] as $option)
                            <button type="button" class="desk-pill desk-pill--alpha {{ $option === $deskStudy['epc_rating']['value'] ? 'is-active' : '' }}" data-pill data-value="{{ $option }}">
                                {{ $option }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="desk-card__column desk-card__column--soil">
                    <div class="desk-soil">
                        <div>
                            <span>Soil Type</span>
                            <strong>{{ $deskStudy['soil']['value'] }}</strong>
                        </div>
                        <span class="desk-badge desk-badge--risk">Risk: {{ Str::upper($deskStudy['soil']['risk']) }}</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<template id="desk-planning-template">
    <div class="desk-planning__row" data-planning-row>
        <span class="desk-planning__label">New Planning Matter</span>
        <div class="desk-planning__options" data-segment>
            <button type="button" class="desk-pill desk-pill--binary is-active" data-pill data-value="Y">Y</button>
            <button type="button" class="desk-pill desk-pill--binary" data-pill data-value="N">N</button>
        </div>
        <button type="button" class="desk-card__icon desk-card__icon--ghost" data-remove-planning title="Remove">
            <i class="fas fa-times"></i>
        </button>
    </div>
</template>
@endsection

@push('styles')
<style>
    .desk-study {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .desk-study__header {
        display: flex;
        align-items: center;
        gap: 1rem;
        justify-content: space-between;
        background: linear-gradient(135deg, #111827, #1E293B);
        color: #E2E8F0;
        padding: 1.25rem 1.75rem;
        border-radius: 18px;
        box-shadow: 0 25px 50px -30px rgba(15, 23, 42, 0.75);
    }

    .desk-study__back,
    .desk-study__nav {
        color: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        padding: 0.55rem 1rem;
        border-radius: 10px;
        border: 1px solid rgba(226, 232, 240, 0.35);
        transition: all 0.2s ease;
    }

    .desk-study__back:hover,
    .desk-study__nav:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #F8FAFC;
    }

    .desk-study__address {
        flex: 1;
        font-size: 1.2rem;
        font-weight: 700;
        text-align: center;
    }

    .desk-study__grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1.75rem;
    }

    .desk-card {
        background: #FFFFFF;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.75);
        box-shadow: 0 25px 60px -45px rgba(15, 23, 42, 0.4);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .desk-card--map {
        grid-column: span 6;
        position: relative;
    }

    .desk-card--flood {
        grid-column: span 6;
    }

    .desk-card--split {
        grid-column: span 12;
        gap: 1.25rem;
    }

    .desk-card__header {
        padding: 1.1rem 1.35rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.7);
    }

    .desk-card__header h2 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #0F172A;
    }

    .desk-card__body {
        padding: 1.35rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .desk-card__body--map {
        gap: 1.5rem;
    }

    .desk-map {
        width: 100%;
        padding-top: 65%;
        background-size: cover;
        background-position: center;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.5);
    }

    .desk-map__coords {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        font-size: 0.95rem;
        font-weight: 600;
        color: #0F172A;
    }

    .desk-map__coords span {
        display: block;
        font-size: 0.78rem;
        font-weight: 500;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.25rem;
    }

    .desk-card__hint {
        margin: 0;
        padding: 0 1.35rem 1.35rem;
        font-size: 0.85rem;
        color: #94A3B8;
    }

    .desk-card__body--stack {
        gap: 1rem;
    }

    .desk-segment {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
    }

    .desk-segment__label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1F2937;
    }

    .desk-segment__options {
        display: flex;
        gap: 0.45rem;
        flex-wrap: wrap;
    }

    .desk-card__row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.25rem;
        padding: 0 1.35rem 1.35rem;
    }

    .desk-card__column {
        flex: 1 1 280px;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .desk-card__column h3 {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 700;
        color: #0F172A;
    }

    .desk-pill-row {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .desk-pill {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: #FFFFFF;
        color: #111827;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.4rem 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .desk-pill--alpha {
        min-width: 42px;
        justify-content: center;
        display: inline-flex;
        text-transform: uppercase;
    }

    .desk-pill--level.is-active {
        background: #DCFCE7;
        border-color: #16A34A;
        color: #14532D;
    }

    .desk-pill--level[data-value="High"],
    .desk-pill--level[data-value="High"].is-active {
        background: #FEE2E2;
        border-color: #DC2626;
        color: #991B1B;
    }

    .desk-pill--level[data-value="Medium"].is-active {
        background: #FEF9C3;
        border-color: #CA8A04;
        color: #854D0E;
    }

    .desk-pill--level[data-value="Low"].is-active,
    .desk-pill--level[data-value="Very Low"].is-active {
        background: #E0F2FE;
        border-color: #0284C7;
        color: #0C4A6E;
    }

    .desk-pill--binary.is-active,
    .desk-pill--alpha.is-active {
        background: #C1EC4A;
        border-color: #A3DA37;
        color: #111827;
        box-shadow: 0 12px 22px -18px rgba(193, 236, 74, 0.7);
    }

    .desk-card__icon {
        align-self: flex-start;
        border-radius: 10px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        background: #FFFFFF;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .desk-card__icon:hover {
        background: rgba(193, 236, 74, 0.3);
        border-color: rgba(193, 236, 74, 0.8);
    }

    .desk-card__icon--ghost {
        border: 1px solid rgba(239, 68, 68, 0.5);
        background: transparent;
        color: #EF4444;
    }

    .desk-card__icon--ghost:hover {
        background: rgba(239, 68, 68, 0.12);
    }

    .desk-planning {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .desk-planning__row {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 0.65rem 1rem;
        align-items: center;
        padding: 0.75rem 0.85rem;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: #F8FAFC;
    }

    .desk-planning__label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1F2937;
    }

    .desk-planning__options {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .desk-soil {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.1rem;
        background: #F1F5F9;
        border-radius: 14px;
        border: 1px solid rgba(148, 163, 184, 0.3);
    }

    .desk-soil span {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        display: block;
        margin-bottom: 0.25rem;
    }

    .desk-soil strong {
        font-size: 1.05rem;
        color: #0F172A;
    }

    .desk-badge--risk {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.95rem;
        border-radius: 999px;
        background: #DC2626;
        color: #FFFFFF;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.05em;
    }

    @media (max-width: 1100px) {
        .desk-card--map,
        .desk-card--flood {
            grid-column: span 12;
        }
    }

    @media (max-width: 768px) {
        .desk-study__header {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .desk-study__address {
            text-align: center;
        }

        .desk-study__grid {
            gap: 1.25rem;
        }

        .desk-card__row {
            padding: 0 1.1rem 1.1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pillGroups = document.querySelectorAll('[data-segment]');

        pillGroups.forEach(group => {
            group.addEventListener('click', function (event) {
                const target = event.target.closest('[data-pill]');
                if (!target) return;

                const value = target.dataset.value;
                const options = Array.from(group.querySelectorAll('[data-pill]'));

                if (value === 'Y' || value === 'N' || options.every(btn => btn.dataset.value.length === 1)) {
                    options.forEach(btn => btn.classList.remove('is-active'));
                    target.classList.add('is-active');
                } else {
                    options.forEach(btn => btn.classList.remove('is-active'));
                    target.classList.add('is-active');
                }
            });
        });

        const planningTemplate = document.getElementById('desk-planning-template');
        const planningList = document.querySelector('[data-planning-list]');
        const addPlanningBtn = document.querySelector('[data-add-planning]');

        const bindPlanningRow = (row) => {
            const removeBtn = row.querySelector('[data-remove-planning]');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    row.remove();
                });
            }
        };

        if (planningList) {
            planningList.querySelectorAll('[data-planning-row]').forEach(bindPlanningRow);
        }

        if (addPlanningBtn && planningTemplate && planningList) {
            addPlanningBtn.addEventListener('click', () => {
                const clone = planningTemplate.content.cloneNode(true);
                const row = clone.querySelector('[data-planning-row]');
                planningList.appendChild(clone);
                bindPlanningRow(planningList.lastElementChild);
            });
        }
    });
</script>
@endpush
