@extends('layouts.survey-detail')

@section('title', 'Site Transcript')

@section('content')
<div class="survey-detail-screen">
    <section class="survey-detail-section survey-detail-section--headline">
        <div class="survey-detail-headline">
            <div class="survey-detail-location">
                <i class="fas fa-chevron-left survey-detail-location-icon"></i>
                <span>On-site Transcript</span>
            </div>
            <div class="survey-detail-jobref">
                <span class="survey-detail-jobref-label">Job Reference</span>
                <span class="survey-detail-jobref-value">12SE39DT-SH</span>
            </div>
        </div>
    </section>

    <section class="survey-detail-section">
        <article class="survey-detail-card">
            <header class="survey-detail-card-header">
                <h3>Recorded Notes</h3>
            </header>
            <div class="survey-detail-card-body">
                <ul class="survey-detail-timeline">
                    @foreach ($transcript as $entry)
                        <li>
                            <span class="survey-detail-timeline-date">{{ $entry['time'] }} &middot; {{ $entry['speaker'] }}</span>
                            <p>{{ $entry['text'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </article>
    </section>
</div>
@endsection

