@extends('layouts.survey-mock')

@section('title', 'Survey Data')

@section('content')
<div class="survey-detail-screen">
    <section class="survey-detail-section survey-detail-section--headline">
        <div class="survey-detail-headline">
            <div class="survey-detail-location">
                <i class="fas fa-chevron-left survey-detail-location-icon"></i>
                <span>Survey Data Capture</span>
            </div>
            <div class="survey-detail-jobref">
                <span class="survey-detail-jobref-label">Workspace</span>
                <span class="survey-detail-jobref-value">Roofs Assessment</span>
            </div>
        </div>
    </section>

    <section class="survey-detail-section survey-detail-section--split">
        @include('surveyor.surveys.tabs.input')
    </section>
</div>
@endsection

