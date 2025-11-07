@extends('layouts.survey-detail')

@section('title', 'Survey Details')

@php
    $currentTab = request()->get('tab', 'client-property');
    $currentSection = request()->get('section', '');
@endphp

@section('content')
<!-- Tab Content Area -->
<div class="survey-detail-tab-content">
    @if($currentTab === 'client-property')
        @include('surveyor.surveys.tabs.client-property', ['survey' => $survey])
    @elseif($currentTab === 'media')
        @include('surveyor.surveys.tabs.media', ['survey' => $survey])
    @elseif($currentTab === 'input')
        @include('surveyor.surveys.tabs.input', ['survey' => $survey])
    @elseif($currentTab === 'output')
        @php
            $outputSection = request()->get('section', 'executive-summary');
        @endphp
        @include('surveyor.surveys.tabs.output.content', ['survey' => $survey, 'section' => $outputSection])
    @elseif($currentTab === 'configuration')
        @include('surveyor.surveys.tabs.configuration', ['survey' => $survey])
    @endif
</div>
@endsection

@push('footer')
<!-- Footer Navigation -->
<div class="survey-detail-footer">
    <div class="survey-detail-footer-content">
        <div class="survey-detail-footer-left">
            @if($currentTab === 'client-property')
                <span class="survey-detail-footer-text">Next: Media</span>
            @elseif($currentTab === 'media')
                <span class="survey-detail-footer-text">Next: Input</span>
            @elseif($currentTab === 'input')
                <span class="survey-detail-footer-text">Next: Output</span>
            @elseif($currentTab === 'output')
                <span class="survey-detail-footer-text">Next: Configuration</span>
            @elseif($currentTab === 'configuration')
                <span class="survey-detail-footer-text">Complete</span>
            @endif
        </div>
        <div class="survey-detail-footer-right">
            @if($currentTab !== 'configuration')
                @php
                    $nextTab = match($currentTab) {
                        'client-property' => 'media',
                        'media' => 'input',
                        'input' => 'output',
                        'output' => 'configuration',
                        default => 'media'
                    };
                @endphp
                <a href="{{ route('surveyor.surveys.detail', ['survey' => $survey->id, 'tab' => $nextTab]) }}" 
                   class="survey-detail-footer-next">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @endif
        </div>
    </div>
</div>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom/survey-detail.css') }}">
<style>
    .survey-detail-footer {
        position: sticky;
        bottom: 0;
        background: var(--survey-detail-dark);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem 1.5rem;
        z-index: 100;
        flex-shrink: 0;
        margin-top: auto;
    }
    
    .survey-detail-footer-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .survey-detail-footer-text {
        color: #FFFFFF;
        font-size: 0.9375rem;
        font-weight: 500;
    }
    
    .survey-detail-footer-next {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--survey-detail-green);
        border-radius: 50%;
        color: var(--survey-detail-dark);
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 1.125rem;
    }
    
    .survey-detail-footer-next:hover {
        background: #B0D93F;
        transform: scale(1.05);
        text-decoration: none;
        color: var(--survey-detail-dark);
    }
</style>
@endpush
