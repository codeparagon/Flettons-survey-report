@php
    $cover = $coverPage ?? [];
    $companyName = $cover['company_name'] ?? 'Flettons';
    $levelTitle = $cover['level_title'] ?? 'BUILDING SURVEY REPORT';
    $addressLines = $cover['address_lines'] ?? ['Property Address Not Provided'];
    $clientName = $cover['client_name'] ?? 'N/A';
    $surveyDate = $cover['survey_date'] ?? now()->format('l jS F Y');
    $reference = $cover['reference'] ?? 'N/A';
    $heroPath = $cover['hero_image_path']
        ?? (isset($pdfService) ? $pdfService->defaultCoverHeroImageAbsolutePath() : public_path('images/pdf-cover-default.jpg'));
    $disclaimer = $cover['disclaimer'] ?? 'We are acting on your written instructions as confirmed by our Building Survey Terms and Conditions';
@endphp
<div class="pdf-cover-page">
    <div class="pdf-cover-header">
        <span class="pdf-cover-brand">{{ $companyName }}</span>
    </div>

    <img src="{{ $heroPath }}" alt="Property" class="pdf-cover-hero">

    <table class="pdf-cover-details" cellpadding="0" cellspacing="0">
        <tr>
            <td class="pdf-cover-details-left">
                <div class="pdf-cover-level-title">{{ $levelTitle }}</div>
                <div class="pdf-cover-address">
                    @foreach($addressLines as $line)
                        <div class="pdf-cover-address-line">{{ $line }}</div>
                    @endforeach
                </div>
            </td>
            <td class="pdf-cover-details-right">
                <div class="pdf-cover-meta-block">
                    <div class="pdf-cover-meta-label">PREPARED ON BEHALF OF:</div>
                    <div class="pdf-cover-meta-value">{{ $clientName }}</div>
                </div>
                <div class="pdf-cover-meta-block">
                    <div class="pdf-cover-meta-label">SURVEY DATE:</div>
                    <div class="pdf-cover-meta-value">{{ $surveyDate }}</div>
                </div>
                <div class="pdf-cover-meta-block">
                    <div class="pdf-cover-meta-label">REF:</div>
                    <div class="pdf-cover-meta-value">{{ $reference }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="pdf-cover-bottom">
        <div class="pdf-cover-rics">
            <span class="pdf-cover-rics-mark">RICS</span>
            <span class="pdf-cover-rics-text">Regulated by RICS</span>
        </div>
        <div class="pdf-cover-disclaimer">{{ $disclaimer }}</div>
    </div>
</div>
