<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Survey Report - {{ $survey->job_reference ?? 'N/A' }}</title>
    <style>
        @page {
            margin-top: 20mm;
            margin-bottom: 24mm;
            margin-left: 25mm;
            margin-right: 20mm;
        }

        @page :first {
            margin-top: 0;
            margin-bottom: 0;
            margin-left: 0;
            margin-right: 0;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10.5pt;
            line-height: 1.5;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        /* Cover page (Flettons Level 3 layout) */
        .pdf-cover-page {
            page-break-after: always;
            width: 210mm;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
            font-family: "DejaVu Sans", sans-serif;
            color: #000000;
        }

        .pdf-cover-header {
            background-color: #1b202b;
            padding: 11mm 14mm;
            width: 100%;
            box-sizing: border-box;
        }

        .pdf-cover-brand {
            color: #FFFFFF;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 24pt;
            font-weight: bold;
            letter-spacing: 0.8px;
        }

        .pdf-cover-hero {
            display: block;
            width: 100%;
            height: 95mm;
            object-fit: cover;
            margin: 0;
        }

        .pdf-cover-hero--placeholder {
            width: 100%;
            height: 95mm;
            background-color: #E5E7EB;
        }

        .pdf-cover-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10mm;
            margin-left: 14mm;
            margin-right: 14mm;
            width: auto;
        }

        .pdf-cover-details-left {
            width: 52%;
            vertical-align: top;
            padding-right: 8mm;
        }

        .pdf-cover-details-right {
            width: 48%;
            vertical-align: top;
            padding-left: 4mm;
        }

        .pdf-cover-level-title {
            color: #C1EC4A;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12.5pt;
            font-weight: bold;
            line-height: 1.4;
            text-transform: uppercase;
            margin-bottom: 5mm;
            letter-spacing: 0.4px;
        }

        .pdf-cover-address-line {
            font-family: "DejaVu Serif", serif;
            font-size: 15pt;
            font-weight: normal;
            line-height: 1.5;
            color: #000000;
            margin-bottom: 2mm;
        }

        .pdf-cover-meta-block {
            margin-bottom: 6mm;
        }

        .pdf-cover-meta-label {
            color: #C1EC4A;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1.5mm;
        }

        .pdf-cover-meta-value {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12pt;
            color: #000000;
            line-height: 1.45;
        }

        .pdf-cover-bottom {
            margin-top: 14mm;
            padding: 0 14mm 14mm 14mm;
            text-align: center;
        }

        .pdf-cover-rics {
            margin-bottom: 4mm;
        }

        .pdf-cover-rics-mark {
            display: inline-block;
            font-size: 16pt;
            font-weight: bold;
            color: #1b202b;
            border: 2px solid #1b202b;
            padding: 2px 8px;
            margin-right: 6px;
            vertical-align: middle;
        }

        .pdf-cover-rics-text {
            font-size: 11pt;
            font-weight: bold;
            color: #1b202b;
            vertical-align: middle;
        }

        .pdf-cover-disclaimer {
            font-size: 8.5pt;
            color: #666666;
            line-height: 1.45;
        }

        /* Header - Blank */
        .page-header {
            height: 0;
            margin: 0;
            padding: 0;
        }

        /* Footer */
        .page-footer-wrapper {
            background-color: #1b202b;
            color: #FFFFFF;
            font-size: 9pt;
            font-family: "DejaVu Sans", sans-serif;
            padding: 8px 10px;
            box-sizing: border-box;
        }

        /* Table of Contents — dedicated page(s), separate from report body */
        .toc-page {
            page-break-after: always;
            page-break-inside: auto;
        }

        .toc {
            margin-top: 0;
            margin-bottom: 0;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000000;
        }

        .toc h2 {
            font-family: inherit;
            font-size: 16pt;
            font-weight: bold;
            color: #000000;
            margin-top: 0;
            margin-bottom: 0.85cm;
            text-align: center;
            line-height: 1.4;
        }

        .toc-item {
            margin: 0.25cm 0;
            font-size: 12pt;
            line-height: 1.5;
            color: #000000;
        }

        .toc-item a,
        .toc-item a:visited,
        .toc-item a:hover {
            font-size: 12pt;
            color: #000000;
            text-decoration: none;
        }

        .toc-item .toc-page-num {
            font-size: 12pt;
        }

        .toc-category {
            font-weight: bold;
            color: #000000;
            margin-top: 0.4cm;
            font-size: 12pt;
        }

        .toc-category a,
        .toc-category a:visited,
        .toc-category a:hover {
            font-size: 12pt;
        }

        /* Category Styles */
        .category-section {
            margin-top: 0.45cm;
            margin-bottom: 0.15cm;
        }

        .category-section:first-of-type {
            margin-top: 0;
        }

        .category-title-wrapper {
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        /* Keep subcategory bar attached to the first block of content below it */
        .pdf-subcategory-block {
            page-break-inside: auto;
        }

        .pdf-subcategory-block > .subsection-bar {
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .category-title {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 14pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 0.3cm;
            margin-top: 0;
        }

        /* Sub-section Bar Styling */
        .subsection-bar {
            background-color: #666666;
            color: #FFFFFF;
            padding: 6px 12px;
            font-size: 12pt;
            font-weight: bold;
            margin-top: 0.25cm;
            margin-bottom: 0.15cm;
            line-height: 24px;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        /* Section Styles — allow natural page flow (avoid huge blank gaps) */
        .section-item {
            margin-bottom: 0.35cm;
            page-break-inside: auto;
        }

        .section-content-flow {
            page-break-inside: auto;
        }

        .section-header {
            margin-bottom: 0.2cm;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .section-name {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
        }

        .condition-text {
            font-size: 10.5pt;
            color: #000000;
            font-weight: normal;
            margin-left: 0.5cm;
        }

        /* Condition Rating Badge */
        .condition-badge {
            display: inline-block; 
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            margin-left: 0.5cm;
            vertical-align: middle;
        }

        .condition-badge--1 {
            background-color: #10B981;
        }

        .condition-badge--2 {
            background-color: #F59E0B;
        }

        .condition-badge--3 {
            background-color: #EF4444;
        }

        .condition-badge--ni {
            background-color: #94A3B8;
        }

        /* View Images Link */
        .view-images-link {
            margin-top: 0.3cm;
            font-size: 10pt;
        }

        .view-images-link a,
        .view-images-link a:visited,
        .view-images-link a:hover {
            color: #000000;
            text-decoration: none;
        }

        /* Survey Images Section */
        .survey-images-section {
            page-break-before: always;
            margin-top: 0;
            margin-bottom: 0.5cm;
        }

        .survey-images-title {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 14pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 0.5cm;
            margin-top: 0;
        }

        .image-group {
            margin-bottom: 1cm;
            page-break-inside: avoid;
        }

        .image-group-title {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 0.3cm;
            margin-top: 0.5cm;
        }

        .image-grid {
            width: 100%;
            margin-top: 0.3cm;
        }

        .image-row {
            display: block;
            width: 100%;
            margin-bottom: 0.5cm;
            page-break-inside: avoid;
        }

        .image-item {
            display: inline-block;
            vertical-align: top;
            padding: 0.2cm;
            width: 48%;
            box-sizing: border-box;
        }

        .image-item img {
            max-width: 100%;
            height: auto;
            border: 1px solid #CCCCCC;
            display: block;
        }

        .image-caption {
            font-size: 9pt;
            color: #666666;
            margin-top: 0.2cm;
            text-align: center;
        }

        /* Report Content */
        .report-content {
            margin-top: 0.15cm;
            padding: 0;
            background-color: #FFFFFF;
            border: none;
            font-size: 10.5pt;
            line-height: 1.5;
            color: #000000;
            text-align: left;
            page-break-inside: auto;
        }

        .report-content p {
            margin: 0.2cm 0;
            color: #000000;
        }

        .report-content strong {
            font-weight: bold;
            color: #000000;
        }

        .report-content ul, .report-content ol {
            margin: 0.2cm 0;
            padding-left: 1.5cm;
            color: #000000;
        }

        /* Form Data Display */
        .form-data {
            margin-top: 0.2cm;
            padding: 0;
            background-color: #FFFFFF;
            border: none;
        }

        .form-data-row {
            margin: 0.15cm 0;
            display: flex;
        }

        .form-data-label {
            font-weight: bold;
            color: #000000;
            width: 3cm;
            flex-shrink: 0;
        }

        .form-data-value {
            color: #000000;
        }

        .defects-list {
            list-style-type: disc;
            margin: 0.2cm 0;
            padding-left: 1.5cm;
            color: #000000;
        }

        /* Section estimated costs table (Flettons report layout) */
        .section-costs-wrap {
            margin-top: 0.25cm;
            page-break-before: avoid;
            page-break-inside: auto;
        }

        .section-costs-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 10.5pt;
            font-family: "DejaVu Sans", sans-serif;
        }

        /* Keep column header with its category block (move whole table if it does not fit) */
        .section-costs-table-block {
            page-break-inside: avoid;
            page-break-after: auto;
            margin-bottom: 0;
        }

        .section-costs-table-block--splittable {
            page-break-inside: auto;
        }

        .section-costs-table-block--splittable thead {
            display: table-header-group;
        }

        .section-costs-table-block thead {
            display: table-header-group;
            page-break-after: avoid;
        }

        .section-costs-table-block thead tr {
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .section-costs-table-block tbody tr.section-costs-category-row {
            page-break-before: avoid;
            page-break-after: avoid;
        }

        .section-costs-table-block--with-totals {
            page-break-inside: avoid;
        }

        .section-costs-table-block--with-totals.section-costs-table-block--splittable {
            page-break-inside: auto;
        }

        .section-costs-table-block tbody tr.section-costs-totals-row {
            page-break-before: avoid;
            page-break-inside: avoid;
        }

        .section-costs-data-row {
            page-break-inside: avoid;
        }

        .section-costs-totals-row {
            page-break-inside: avoid;
        }

        .section-costs-table thead th {
            background-color: #1b202b;
            color: #FFFFFF;
            font-weight: bold;
            padding: 0.35cm 0.4cm;
            text-align: left;
            border: 1px solid #1b202b;
            vertical-align: middle;
        }

        .section-costs-table tbody td {
            padding: 0.35cm 0.4cm;
            border: 1px solid #CCCCCC;
            vertical-align: top;
            background-color: #FFFFFF;
            color: #000000;
            line-height: 1.45;
        }

        .section-costs-category-row td {
            background-color: #666666;
            color: #FFFFFF;
            font-weight: bold;
            border-color: #666666;
        }

        .section-costs-col-desc {
            width: 58%;
        }

        .section-costs-col-due {
            width: 18%;
        }

        .section-costs-col-amount {
            width: 24%;
            text-align: right;
        }

        .section-costs-table tbody tr.section-costs-totals-row td {
            background-color: #1b202b !important;
            color: #FFFFFF !important;
            font-weight: bold;
            vertical-align: middle;
            border: 1px solid #1b202b !important;
        }

        .section-costs-totals-label {
            text-align: left;
            width: 58%;
        }

        .section-costs-totals-due {
            width: 18%;
        }

        .section-costs-totals-sum {
            text-align: right;
            width: 24%;
        }

        /* Configuration of Accommodation table (Flettons report layout) */
        .accommodation-config-section {
            margin-top: 0.25cm;
            page-break-inside: auto;
        }

        .accommodation-config-section > .subsection-bar {
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .accommodation-config-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            font-size: 10.5pt;
            font-family: "DejaVu Sans", sans-serif;
            page-break-inside: auto;
        }

        .accommodation-config-table thead {
            display: table-header-group;
        }

        .accommodation-config-table tbody tr {
            page-break-inside: avoid;
        }

        .accommodation-config-table thead th {
            background-color: #1b202b;
            color: #FFFFFF;
            font-weight: bold;
            padding: 0.35cm 0.4cm;
            text-align: left;
            border: 1px solid #1b202b;
            vertical-align: middle;
        }

        .accommodation-config-table tbody td {
            padding: 0.35cm 0.4cm;
            border: 1px solid #CCCCCC;
            vertical-align: top;
            background-color: #FFFFFF;
            color: #000000;
            line-height: 1.45;
        }

        .accommodation-config-table .col-room {
            width: 16%;
        }

        .accommodation-config-table .col-location {
            width: 14%;
        }

        .accommodation-config-table .col-position {
            width: 14%;
        }

        .accommodation-config-table .col-observations {
            width: 56%;
        }

        .accommodation-config-table .cell-room-name {
            font-weight: bold;
        }

        .accommodation-observations-text {
            margin: 0;
            padding: 0;
        }

        .accommodation-observations-text p {
            margin: 0 0 0.28cm 0;
            padding: 0;
        }

        .accommodation-observations-text p:last-child {
            margin-bottom: 0;
        }

        .accommodation-photo-ref {
            display: block;
            margin-top: 0.35cm;
            color: #666666;
            font-size: 10pt;
        }

        .accommodation-photo-ref a,
        .accommodation-photo-ref a:visited,
        .accommodation-photo-ref a:hover {
            color: #666666;
            text-decoration: none;
        }

        .pdf-anchor-target {
            display: block;
            height: 0;
            overflow: hidden;
            line-height: 0;
            font-size: 0;
        }

        .accommodation-cell-empty {
            color: #999999;
        }

        /* Body pages only — cover uses RICS footer inside .pdf-cover-page (no fixed bar) */
        .pdf-fixed-footer {
            display: none;
        }

        /* Component Details */
        .component-details {
            margin: 0.3cm 0;
            padding-left: 0;
            border-left: none;
        }

        .component-name {
            font-weight: bold;
            color: #000000;
            margin-bottom: 0.2cm;
        }

        /* Content Section Styles */
        .content-section {
            margin-bottom: 0.35cm;
            page-break-inside: auto;
        }

        .content-section > .subsection-bar {
            page-break-after: avoid;
        }

        .content-section-body {
            margin-top: 0.2cm;
            padding: 0;
            background-color: #FFFFFF;
            border: none;
            line-height: 1.5;
            color: #000000;
            text-align: left;
            font-size: 10.5pt;
        }

        /* Accommodation Section */
        .accommodation-section {
            margin-bottom: 0.5cm;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    @include('surveyor.surveys.pdf.partials.cover-page', [
        'coverPage' => $coverPage ?? [],
        'pdfService' => $pdfService ?? null,
    ])

    <!-- Table of Contents -->
    <div class="toc-page">
    <div class="toc">
        <h2>Table of Contents</h2>
        @php
            $pageNumber = 3;
            $mainSectionNumber = 1;
        @endphp
        
        @foreach($categories as $categoryName => $subCategories)
            <div class="toc-item toc-category">
                @if(isset($pdfService))
                    <a href="#{{ $pdfService->categoryAnchorId($categoryName) }}">{{ $categoryName }}</a>
                @else
                    {{ $categoryName }}
                @endif
            </div>
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($subCategories as $subCategoryName => $sections)
                @if(!empty($subCategoryName))
                    <div class="toc-item" style="margin-left: 0.5cm;">
                        @if(isset($pdfService))
                            <a href="#{{ $pdfService->subcategoryAnchorId($categoryName, $subCategoryName) }}">{{ $subCategoryName }}</a>
                        @else
                            {{ $subCategoryName }}
                        @endif
                    </div>
                    @php $subSectionNumber++; @endphp
                @endif
                @foreach($sections as $section)
                    <div class="toc-item" style="margin-left: 1cm;">
                        @if(isset($pdfService))
                            <a href="#{{ $pdfService->sectionAnchorId($section) }}">{{ $section['name'] }}</a>
                        @else
                            <span>{{ $section['name'] }}</span>
                        @endif
                        <span style="float: right;">{{ $pageNumber }}</span>
                    </div>
                    @php $pageNumber++; @endphp
                @endforeach
            @endforeach
            @php $mainSectionNumber++; @endphp
        @endforeach

        @if(!empty($accommodationTableRows) || !empty($accommodationSections))
            <div class="toc-item toc-category" style="margin-top: 0.5cm;">
                @if(isset($pdfService))
                    <a href="#{{ $pdfService->accommodationConfigAnchorId() }}">Configuration of Accommodation</a>
                @else
                    Configuration of Accommodation
                @endif
            </div>
            @php $pageNumber++; @endphp
        @endif

        @if(!empty($contentSections['standalone']))
            <div class="toc-item toc-category" style="margin-top: 0.5cm;">Content Sections</div>
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($contentSections['standalone'] as $contentSection)
                <div class="toc-item" style="margin-left: 0.5cm;">
                    @if(isset($pdfService))
                        <a href="#{{ $pdfService->contentSectionAnchorId($contentSection) }}">{{ $contentSection->title }}</a>
                    @else
                        <span>{{ $contentSection->title }}</span>
                    @endif
                    <span style="float: right;">{{ $pageNumber }}</span>
                </div>
                @php 
                    $pageNumber++;
                    $subSectionNumber++;
                @endphp
            @endforeach
            @php $mainSectionNumber++; @endphp
        @endif

        @if(!empty($surveyImages) && count($surveyImages) > 0)
            @php
                $imagesPageNumber = $pageNumber;
            @endphp
            <div class="toc-item toc-category" style="margin-top: 0.5cm;">Survey Images</div>
            <div class="toc-item" style="margin-left: 0.5cm;">
                <a href="#survey-images-section">Survey Images</a>
                <span style="float: right;">{{ $imagesPageNumber }}</span>
            </div>
            @foreach($surveyImages as $imageGroup)
                <div class="toc-item" style="margin-left: 1cm;">
                    <a href="#{{ $imageGroup['anchor_id'] }}">{{ $imageGroup['name'] }}</a>
                </div>
            @endforeach
        @endif
    </div>
    </div>

    <!-- Regular Sections -->
    @php
        $mainSectionNumber = 1;
        $firstCategory = true;
    @endphp
    @foreach($categories as $categoryName => $subCategories)
        <div class="category-section" style="{{ $firstCategory ? 'margin-top: 0;' : '' }}">
            <a id="{{ isset($pdfService) ? $pdfService->categoryAnchorId($categoryName) : '' }}" name="{{ isset($pdfService) ? $pdfService->categoryAnchorId($categoryName) : '' }}" class="pdf-anchor-target"></a>
            <div class="category-title-wrapper">
                <h1 class="category-title">{{ $categoryName }}</h1>
            </div>
            @php $firstCategory = false; @endphp
            
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($subCategories as $subCategoryName => $sections)
                <div class="pdf-subcategory-block">
                @if(!empty($subCategoryName))
                    <a id="{{ isset($pdfService) ? $pdfService->subcategoryAnchorId($categoryName, $subCategoryName) : '' }}" name="{{ isset($pdfService) ? $pdfService->subcategoryAnchorId($categoryName, $subCategoryName) : '' }}" class="pdf-anchor-target"></a>
                    <div class="subsection-bar">{{ $subCategoryName }}</div>
                    @php $subSectionNumber++; @endphp
                @endif

                @php
                    $itemNumber = 1;
                    $currentSubSection = $subSectionNumber - 1;
                @endphp
                @foreach($sections as $section)
                    @php
                        $sectionReportText = isset($pdfService)
                            ? $pdfService->resolveSectionPdfReportContent($section)
                            : trim((string) ($section['merged_report_content'] ?? $section['report_content'] ?? ''));
                        $costsPdf = isset($pdfService)
                            ? $pdfService->buildSectionCostsGroupedForPdf($section)
                            : ['has_costs' => !empty($section['costs']), 'groups' => [], 'total' => 0.0];
                    @endphp
                    <a id="{{ isset($pdfService) ? $pdfService->sectionAnchorId($section) : '' }}" name="{{ isset($pdfService) ? $pdfService->sectionAnchorId($section) : '' }}" class="pdf-anchor-target"></a>
                    <div class="section-item">
                        <div class="section-content-flow">
                        <div class="section-header">
                            <span class="section-name">{{ $section['name'] }}</span>
                            @if(!empty($section['condition_rating']))
                                @php
                                    $rating = strtolower($section['condition_rating']);
                                    $ratingClass = 'condition-badge--' . ($rating === 'ni' ? 'ni' : $rating);
                                @endphp
                                <span class="condition-badge {{ $ratingClass }}"></span>
                            @endif
                        </div>

                        @if(trim($sectionReportText) !== '')
                            <div class="report-content">
                                {!! nl2br(e($sectionReportText)) !!}
                            </div>
                        @elseif(isset($pdfService) && $pdfService->sectionHasPdfDisplayContent($section))
                            <div class="form-data">
                                @if(!empty($section['location']))
                                    <div class="form-data-row">
                                        <span class="form-data-label">Location:</span>
                                        <span class="form-data-value">{{ $section['location'] }}</span>
                                    </div>
                                @endif
                                @if(!empty($section['structure']))
                                    <div class="form-data-row">
                                        <span class="form-data-label">Structure:</span>
                                        <span class="form-data-value">{{ $section['structure'] }}</span>
                                    </div>
                                @endif
                                @if(!empty($section['material']))
                                    <div class="form-data-row">
                                        <span class="form-data-label">Material:</span>
                                        <span class="form-data-value">{{ $section['material'] }}</span>
                                    </div>
                                @endif
                                @if(!empty($section['defects']) && is_array($section['defects']) && count($section['defects']) > 0)
                                    <div class="form-data-row">
                                        <span class="form-data-label">Defects:</span>
                                        <span class="form-data-value">
                                            <ul class="defects-list">
                                                @foreach($section['defects'] as $defect)
                                                    <li>{{ $defect }}</li>
                                                @endforeach
                                            </ul>
                                        </span>
                                    </div>
                                @endif
                                @if(!empty($section['remaining_life']))
                                    <div class="form-data-row">
                                        <span class="form-data-label">Remaining Life:</span>
                                        <span class="form-data-value">{{ $section['remaining_life'] }}</span>
                                    </div>
                                @endif
                                @if(!empty($section['notes']))
                                    <div class="form-data-row">
                                        <span class="form-data-label">Notes:</span>
                                        <span class="form-data-value">{{ $section['notes'] }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(!empty($costsPdf['has_costs']))
                            @include('surveyor.surveys.pdf.partials.section-costs-table', ['costsPdf' => $costsPdf, 'pdfService' => $pdfService ?? null])
                        @endif
                        @if(isset($pdfService) && $pdfService->sectionHasPhotos($section))
                            <div class="view-images-link">
                                <a href="#{{ $pdfService->sectionImageAnchorId($section) }}">View Images ({{ $pdfService->sectionPhotoCount($section) }})</a>
                            </div>
                        @endif
                        </div>
                    </div>
                    @php $itemNumber++; @endphp
                @endforeach

                {{-- Content sections linked to this subcategory --}}
                @if(isset($contentSections['by_subcategory'][$categoryName][$subCategoryName]))
                    @foreach($contentSections['by_subcategory'][$categoryName][$subCategoryName] as $contentSection)
                        <div class="content-section">
                            <div class="subsection-bar">{{ $contentSection->title }}</div>
                            <div class="content-section-body">
                                {!! nl2br(e($contentSection->content ?? '')) !!}
                            </div>
                        </div>
                        @php $itemNumber++; @endphp
                    @endforeach
                @endif
                </div>
            @endforeach

            {{-- Content sections linked to this category --}}
            @if(isset($contentSections['by_category'][$categoryName]))
                @php
                    $itemNumber = 1;
                @endphp
                @foreach($contentSections['by_category'][$categoryName] as $contentSection)
                    <div class="content-section">
                        <div class="subsection-bar">{{ $contentSection->title }}</div>
                        <div class="content-section-body">
                            {!! nl2br(e($contentSection->content ?? '')) !!}
                        </div>
                    </div>
                    @php $itemNumber++; @endphp
                @endforeach
            @endif
        </div>
        @php $mainSectionNumber++; @endphp
    @endforeach

    <!-- Configuration of Accommodation (table layout) -->
    @if(!empty($accommodationTableRows) && count($accommodationTableRows) > 0)
        <div class="category-section accommodation-config-section">
            <a id="{{ isset($pdfService) ? $pdfService->accommodationConfigAnchorId() : '' }}" name="{{ isset($pdfService) ? $pdfService->accommodationConfigAnchorId() : '' }}" class="pdf-anchor-target"></a>
            <div class="subsection-bar">Configuration of Accommodation</div>

            <table class="accommodation-config-table">
                <thead>
                    <tr>
                        <th class="col-room">Room/Area</th>
                        <th class="col-location">Location</th>
                        <th class="col-position">Front/Rear/Center</th>
                        <th class="col-observations">Photos and Observations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accommodationTableRows as $row)
                        <tr>
                            <td class="col-room">
                                <span class="cell-room-name">{{ $row['room'] }}</span>
                            </td>
                            <td class="col-location">
                                @if(!empty($row['location']))
                                    {{ $row['location'] }}
                                @else
                                    <span class="accommodation-cell-empty">—</span>
                                @endif
                            </td>
                            <td class="col-position">
                                @if(!empty($row['position']))
                                    {{ $row['position'] }}
                                @else
                                    <span class="accommodation-cell-empty">—</span>
                                @endif
                            </td>
                            <td class="col-observations">
                                @if(!empty($row['observations']))
                                    <div class="accommodation-observations-text">
                                        @foreach(preg_split("/\n\s*\n/", trim($row['observations'])) as $paragraph)
                                            @if(trim($paragraph) !== '')
                                                <p>{!! nl2br(e(trim($paragraph))) !!}</p>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="accommodation-cell-empty">No survey data recorded for this room yet.</span>
                                @endif
                                @if(!empty($row['photo_ref']))
                                    <span class="accommodation-photo-ref">
                                        @if(!empty($row['photo_anchor_id']))
                                            <a href="#{{ $row['photo_anchor_id'] }}">{{ $row['photo_ref'] }}</a>
                                        @else
                                            {{ $row['photo_ref'] }}
                                        @endif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @php $mainSectionNumber++; @endphp
    @elseif(!empty($accommodationSections))
        <div class="category-section accommodation-config-section">
            <div class="subsection-bar">Configuration of Accommodation</div>
            <table class="accommodation-config-table">
                <thead>
                    <tr>
                        <th class="col-room">Room/Area</th>
                        <th class="col-location">Location</th>
                        <th class="col-position">Front/Rear/Center</th>
                        <th class="col-observations">Photos and Observations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accommodationSections as $accommodation)
                        <tr>
                            <td class="col-room"><span class="cell-room-name">{{ $accommodation['display_label'] ?? $accommodation['name'] ?? '' }}</span></td>
                            <td class="col-location">{{ trim((string) ($accommodation['location'] ?? '')) !== '' ? $accommodation['location'] : '—' }}</td>
                            <td class="col-position">—</td>
                            <td class="col-observations">
                                @if(!empty($accommodation['report_content']))
                                    {!! nl2br(e($accommodation['report_content'])) !!}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @php $mainSectionNumber++; @endphp
    @endif

    <!-- Standalone Content Sections -->
    @if(!empty($contentSections['standalone']))
        <div class="category-section">
            <div class="category-title-wrapper">
                <h1 class="category-title">Content Sections</h1>
            </div>
            
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($contentSections['standalone'] as $contentSection)
                <a id="{{ isset($pdfService) ? $pdfService->contentSectionAnchorId($contentSection) : '' }}" name="{{ isset($pdfService) ? $pdfService->contentSectionAnchorId($contentSection) : '' }}" class="pdf-anchor-target"></a>
                <div class="content-section">
                    <div class="subsection-bar">{{ $contentSection->title }}</div>
                    <div class="content-section-body">
                        {!! nl2br(e($contentSection->content ?? '')) !!}
                    </div>
                </div>
                @php $subSectionNumber++; @endphp
            @endforeach
        </div>
    @endif

    <!-- Survey Images Section -->
    @if(!empty($surveyImages) && count($surveyImages) > 0)
        <a id="survey-images-section" name="survey-images-section" class="pdf-anchor-target"></a>
        <div class="survey-images-section">
            <h1 class="survey-images-title">Survey Images</h1>
            
            @foreach($surveyImages as $imageGroup)
                <a id="{{ $imageGroup['anchor_id'] }}" name="{{ $imageGroup['anchor_id'] }}" class="pdf-anchor-target"></a>
                <div class="image-group">
                    <div class="image-group-title">{{ $imageGroup['name'] }}</div>
                    <div class="image-grid">
                        @php
                            $photos = $imageGroup['photos'];
                            $photoCount = count($photos);
                        @endphp
                        @for($i = 0; $i < $photoCount; $i += 2)
                            <div class="image-row">
                                @for($j = $i; $j < min($i + 2, $photoCount); $j++)
                                    @php
                                        $photo = $photos[$j];
                                        $absolutePath = isset($pdfService) ? $pdfService->resolvePhotoAbsolutePathForPdf($photo) : null;
                                        $photoLabel = isset($photo['pdf_number'])
                                            ? 'Photo ' . $photo['pdf_number']
                                            : ($photo['file_name'] ?? 'Image ' . ($j + 1));
                                    @endphp
                                    <div class="image-item">
                                        @if($absolutePath)
                                            <img src="{{ $absolutePath }}" alt="{{ $photoLabel }}">
                                        @else
                                            <div style="padding: 1cm; border: 1px solid #CCCCCC; text-align: center; color: #999999;">
                                                Image not found: {{ $photo['file_name'] ?? 'Unknown' }}
                                            </div>
                                        @endif
                                        <div class="image-caption">{{ $photoLabel }}</div>
                                    </div>
                                @endfor
                            </div>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    @endif

<script type="text/php">
if (isset($pdf)) {
    $font = $fontMetrics->getFont("dejavu sans", "normal");
    $fontBold = $fontMetrics->getFont("dejavu sans", "bold");
    $pageWidth = $pdf->get_width();
    $pageHeight = $pdf->get_height();
    $footerHeight = 68;
    $footerRgb = array(0.106, 0.125, 0.169);
    $white = array(1, 1, 1);
    $leftPad = 71;
    $rightPad = 71;

    if ($PAGE_NUM > 1) {
        $footerY = $pageHeight - $footerHeight;
        $pdf->filled_rectangle(0, $footerY, $pageWidth, $footerHeight, $footerRgb);
        $pdf->page_text($leftPad, $footerY + 42, "Flettons Surveyors 20-22 Wenlock Road, London, N1 7GU", $font, 8, $white);
        $pdf->page_text($leftPad, $footerY + 28, "E: info@flettons.com | T: 0330 043 4650 | W: www.flettons.com", $font, 8, $white);
        $pdf->page_text($pageWidth - $rightPad - 40, $footerY + 34, "Page {PAGE_NUM}", $font, 9, $white);
    }
}
</script>

</body>
</html>
