@extends('layouts.survey-detail')

@section('title', 'Documents')

@section('content')
<div class="survey-detail-screen">
    <section class="survey-detail-section survey-detail-section--headline">
        <div class="survey-detail-headline">
            <div class="survey-detail-location">
                <i class="fas fa-chevron-left survey-detail-location-icon"></i>
                <span>Document Centre</span>
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
                <h3>Uploaded Files</h3>
            </header>
            <div class="survey-detail-card-body">
                <div class="survey-detail-documents">
                    @foreach ($documents as $document)
                        <div class="survey-detail-document">
                            <div class="survey-detail-document-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="survey-detail-document-meta">
                                <span class="survey-detail-document-name">{{ $document['name'] }}</span>
                                <span class="survey-detail-document-info">Uploaded {{ $document['uploaded_at'] }} · {{ $document['size'] }}</span>
                            </div>
                            <button type="button" class="survey-detail-document-action">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="survey-detail-upload-btn">
                    <i class="fas fa-upload"></i>
                    <span>Upload Document</span>
                </button>
            </div>
        </article>
    </section>
</div>
@endsection
