@extends('layouts.survey-mock')

@section('title', 'Media Files')

@section('content')
<div class="survey-detail-screen">
    <section class="survey-detail-section survey-detail-section--headline">
        <div class="survey-detail-headline">
            <div class="survey-detail-location">
                <i class="fas fa-chevron-left survey-detail-location-icon"></i>
                <span>Survey Media Library</span>
            </div>
            <div class="survey-detail-jobref">
                <span class="survey-detail-jobref-label">Job Reference</span>
                <span class="survey-detail-jobref-value">12SE39DT-SH</span>
            </div>
        </div>
    </section>

    <section class="survey-detail-section" id="media-upload">
        @include('surveyor.surveys.tabs.media')
    </section>
</div>
@endsection

