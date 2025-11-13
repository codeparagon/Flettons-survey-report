@extends('layouts.survey-mock')

@section('title', 'Desk Study')

@section('content')
<div class="survey-detail-screen">
    <section class="survey-detail-section survey-detail-section--headline">
        <div class="survey-detail-headline">
            <div class="survey-detail-location">
                <i class="fas fa-chevron-left survey-detail-location-icon"></i>
                <span>{{ $deskStudy['address'] }}</span>
            </div>
            <div class="survey-detail-jobref">
                <span class="survey-detail-jobref-label">Job Reference</span>
                <span class="survey-detail-jobref-value">{{ $deskStudy['job_reference'] }}</span>
            </div>
        </div>
    </section>

    <section class="survey-detail-section">
        <div class="survey-detail-grid survey-detail-grid--two">
            <article class="survey-detail-card">
                <header class="survey-detail-card-header">
                    <h3>Location Overview</h3>
                </header>
                <div class="survey-detail-card-body">
                    <img src="{{ $deskStudy['map']['image'] }}" alt="Map preview" class="img-fluid rounded" />
                    <dl class="survey-detail-datalist">
                        <div><dt>Longitude</dt><dd>{{ $deskStudy['map']['longitude'] }}</dd></div>
                        <div><dt>Latitude</dt><dd>{{ $deskStudy['map']['latitude'] }}</dd></div>
                    </dl>
                </div>
            </article>

            <article class="survey-detail-card">
                <header class="survey-detail-card-header">
                    <h3>Flood Risk Summary</h3>
                </header>
                <div class="survey-detail-card-body">
                    <ul class="survey-detail-list">
                        @foreach ($deskStudy['flood_risks'] as $risk)
                            <li><strong>{{ $risk['label'] }}:</strong> {{ $risk['value'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </article>
        </div>
    </section>

    <section class="survey-detail-section" id="desk-study-planning">
        <article class="survey-detail-card">
            <header class="survey-detail-card-header">
                <h3>Planning & Compliance</h3>
            </header>
            <div class="survey-detail-card-body">
                <div class="survey-detail-datalist">
                    @foreach ($deskStudy['planning'] as $item)
                        <div>
                            <dt>{{ $item['label'] }}</dt>
                            <dd>{{ $item['value'] }}</dd>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>
</div>
@endsection

