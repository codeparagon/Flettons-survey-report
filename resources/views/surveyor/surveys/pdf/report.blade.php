<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Survey Report - {{ $survey->job_reference ?? 'N/A' }}</title>
    <style>
        @page {
            margin-top: 20mm;
            margin-bottom: 30mm;
            margin-left: 25mm;
            margin-right: 20mm;
            footer: html_pageFooter;
            header: html_pageHeader;
        }

        @page :first {
            margin-top: 20mm;
            margin-bottom: 30mm;
            margin-left: 25mm;
            margin-right: 20mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.5;
            color: #000000;
            margin: 0;
            padding: 0;
        }

        /* Cover Page Styles */
        .cover-page {
            page-break-after: always;
            text-align: center;
            padding: 3cm 0 2cm 0;
        }

        .company-name {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 2cm;
        }

        .report-title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 24pt;
            font-weight: bold;
            color: #000000;
            margin: 2cm 0 1.5cm 0;
        }

        .survey-details {
            margin-top: 3cm;
            text-align: left;
            display: inline-block;
            font-size: 11pt;
        }

        .survey-details p {
            margin: 0.5cm 0;
            font-weight: normal;
            color: #000000;
        }

        .survey-address {
            font-size: 14pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 1cm;
        }

        /* Header - Blank */
        .page-header {
            height: 0;
            margin: 0;
            padding: 0;
        }

        /* Footer */
        .page-footer-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background-color: #2C2C54;
            color: #FFFFFF;
            font-size: 9pt;
            font-family: Arial, Helvetica, sans-serif;
            padding: 8px 10px;
            box-sizing: border-box;
        }

        /* Table of Contents */
        .toc {
            margin-top: 0;
            margin-bottom: 0;
        }

        .toc h2 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16pt;
            font-weight: bold;
            color: #000000;
            margin-top: 0;
            margin-bottom: 1cm;
            text-align: center;
        }

        .toc-item {
            margin: 0.3cm 0;
            font-size: 10.5pt;
            color: #000000;
        }

        .toc-category {
            font-weight: bold;
            color: #000000;
            margin-top: 0.4cm;
            font-size: 11pt;
        }

        /* Category Styles */
        .category-section {
            page-break-before: always;
            margin-top: 0;
            margin-bottom: 0.5cm;
        }

        .category-title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14pt;
            font-weight: bold;
            color: #000000;
            margin-bottom: 0.5cm;
            margin-top: 0;
        }

        /* Sub-section Bar Styling */
        .subsection-bar {
            background-color: #666666;
            color: #FFFFFF;
            padding: 6px 12px;
            font-size: 12pt;
            font-weight: bold;
            margin-top: 0.5cm;
            margin-bottom: 0.3cm;
        }

        /* Section Styles */
        .section-item {
            margin-bottom: 0.8cm;
            page-break-inside: avoid;
        }

        .section-header {
            margin-bottom: 0.3cm;
        }

        .section-name {
            font-family: Arial, Helvetica, sans-serif;
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

        /* Report Content */
        .report-content {
            margin-top: 0.3cm;
            padding: 0;
            background-color: #FFFFFF;
            border: none;
            font-size: 10.5pt;
            line-height: 1.5;
            color: #000000;
            text-align: left;
        }

        .report-content p {
            margin: 0.4cm 0;
            color: #000000;
        }

        .report-content strong {
            font-weight: bold;
            color: #000000;
        }

        .report-content ul, .report-content ol {
            margin: 0.4cm 0;
            padding-left: 1.5cm;
            color: #000000;
        }

        /* Form Data Display */
        .form-data {
            margin-top: 0.3cm;
            padding: 0;
            background-color: #FFFFFF;
            border: none;
        }

        .form-data-row {
            margin: 0.3cm 0;
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

        /* Costs Table */
        .costs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.3cm;
            font-size: 10pt;
            border: 1px solid #000000;
        }

        .costs-table th {
            background-color: #FFFFFF;
            color: #000000;
            padding: 0.3cm;
            text-align: left;
            font-weight: bold;
            font-family: Arial, Helvetica, sans-serif;
            border: 1px solid #000000;
            border-bottom: 2px solid #000000;
        }

        .costs-table td {
            padding: 0.3cm;
            border: 1px solid #000000;
            color: #000000;
        }

        .costs-table tr {
            background-color: #FFFFFF;
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
            margin-bottom: 0.8cm;
            page-break-inside: avoid;
        }

        .content-section-body {
            margin-top: 0.3cm;
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
            margin-bottom: 0.8cm;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Cover Page -->
    <div class="cover-page">
        <div class="company-name">Flettons Surveyors</div>
        <div class="report-title">Property Survey Report</div>
        <div class="survey-details">
            <p class="survey-address">{{ $survey->full_address ?? 'Property Address Not Provided' }}</p>
            <p><strong>Job Reference:</strong> {{ $survey->job_reference ?? 'N/A' }}</p>
            <p><strong>Survey Level:</strong> {{ $survey->level ?? 'N/A' }}</p>
            <p><strong>Report Date:</strong> {{ now()->format('d F Y') }}</p>
            @if($survey->surveyor)
            <p><strong>Surveyor:</strong> {{ $survey->surveyor->name ?? 'N/A' }}</p>
            @endif
        </div>
    </div>

    <!-- Table of Contents -->
    <div class="toc">
        <h2>Table of Contents</h2>
        @php
            $pageNumber = 3;
            $mainSectionNumber = 1;
        @endphp
        
        @foreach($categories as $categoryName => $subCategories)
            <div class="toc-item toc-category">{{ $mainSectionNumber }}.0 {{ $categoryName }}</div>
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($subCategories as $subCategoryName => $sections)
                @if(!empty($subCategoryName))
                    <div class="toc-item" style="margin-left: 0.5cm;">{{ $mainSectionNumber }}.{{ $subSectionNumber }} {{ $subCategoryName }}</div>
                    @php $subSectionNumber++; @endphp
                @endif
                @foreach($sections as $section)
                    <div class="toc-item" style="margin-left: 1cm;">
                        <span>{{ $section['name'] }}</span>
                        <span style="float: right;">{{ $pageNumber }}</span>
                    </div>
                    @php $pageNumber++; @endphp
                @endforeach
            @endforeach
            @php $mainSectionNumber++; @endphp
        @endforeach

        @if(!empty($accommodationSections))
            <div class="toc-item toc-category" style="margin-top: 0.5cm;">{{ $mainSectionNumber }}.0 Configuration of Accommodation</div>
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($accommodationSections as $accommodation)
                <div class="toc-item" style="margin-left: 0.5cm;">
                    <span>{{ $mainSectionNumber }}.{{ $subSectionNumber }} {{ $accommodation['accommodation_type_name'] ?? $accommodation['name'] }}</span>
                    <span style="float: right;">{{ $pageNumber }}</span>
                </div>
                @php 
                    $pageNumber++;
                    $subSectionNumber++;
                @endphp
            @endforeach
            @php $mainSectionNumber++; @endphp
        @endif

        @if(!empty($contentSections['standalone']))
            <div class="toc-item toc-category" style="margin-top: 0.5cm;">{{ $mainSectionNumber }}.0 Content Sections</div>
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($contentSections['standalone'] as $contentSection)
                <div class="toc-item" style="margin-left: 0.5cm;">
                    <span>{{ $mainSectionNumber }}.{{ $subSectionNumber }} {{ $contentSection->title }}</span>
                    <span style="float: right;">{{ $pageNumber }}</span>
                </div>
                @php 
                    $pageNumber++;
                    $subSectionNumber++;
                @endphp
            @endforeach
        @endif
    </div>

    <!-- Regular Sections -->
    @php
        $mainSectionNumber = 1;
    @endphp
    @foreach($categories as $categoryName => $subCategories)
        <div class="category-section">
            <h1 class="category-title">{{ $mainSectionNumber }}.0 {{ $categoryName }}</h1>
            
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($subCategories as $subCategoryName => $sections)
                @if(!empty($subCategoryName))
                    <div class="subsection-bar">{{ $mainSectionNumber }}.{{ $subSectionNumber }} {{ $subCategoryName }}</div>
                    @php $subSectionNumber++; @endphp
                @endif

                @php
                    $itemNumber = 1;
                    $currentSubSection = $subSectionNumber - 1;
                @endphp
                @foreach($sections as $section)
                    <div class="section-item">
                        <div class="section-header">
                            <span class="section-name">{{ $mainSectionNumber }}.{{ $currentSubSection }}.{{ $itemNumber }} {{ $section['name'] }}</span>
                            @if(!empty($section['condition_rating']))
                                <span class="condition-text">(Condition Rating: {{ strtoupper($section['condition_rating']) }})</span>
                            @endif
                        </div>

                        @if(!empty($section['report_content']))
                            <div class="report-content">
                                {!! nl2br(e($section['report_content'])) !!}
                            </div>
                        @else
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
                                @if(!empty($section['costs']) && is_array($section['costs']) && count($section['costs']) > 0)
                                    <div class="form-data-row">
                                        <span class="form-data-label">Costs:</span>
                                        <table class="costs-table">
                                            <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Description</th>
                                                    <th>Due</th>
                                                    <th>Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($section['costs'] as $cost)
                                                    <tr>
                                                        <td>{{ $cost['category'] ?? '' }}</td>
                                                        <td>{{ $cost['description'] ?? '' }}</td>
                                                        <td>{{ $cost['due'] ?? '' }}</td>
                                                        <td>£{{ $cost['cost'] ?? '0.00' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    @php $itemNumber++; @endphp
                @endforeach

                {{-- Content sections linked to this subcategory --}}
                @if(isset($contentSections['by_subcategory'][$categoryName][$subCategoryName]))
                    @foreach($contentSections['by_subcategory'][$categoryName][$subCategoryName] as $contentSection)
                        <div class="content-section">
                            <div class="subsection-bar">{{ $mainSectionNumber }}.{{ $currentSubSection }}.{{ $itemNumber }} {{ $contentSection->title }}</div>
                            <div class="content-section-body">
                                {!! nl2br(e($contentSection->content ?? '')) !!}
                            </div>
                        </div>
                        @php $itemNumber++; @endphp
                    @endforeach
                @endif
            @endforeach

            {{-- Content sections linked to this category --}}
            @if(isset($contentSections['by_category'][$categoryName]))
                @php
                    $itemNumber = 1;
                @endphp
                @foreach($contentSections['by_category'][$categoryName] as $contentSection)
                    <div class="content-section">
                        <div class="subsection-bar">{{ $mainSectionNumber }}.{{ $subSectionNumber }} {{ $contentSection->title }}</div>
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

    <!-- Accommodation Sections -->
    @if(!empty($accommodationSections))
        <div class="category-section">
            <h1 class="category-title">{{ $mainSectionNumber }}.0 Configuration of Accommodation</h1>
            
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($accommodationSections as $accommodation)
                <div class="accommodation-section section-item">
                    <div class="subsection-bar">{{ $mainSectionNumber }}.{{ $subSectionNumber }} {{ $accommodation['accommodation_type_name'] ?? $accommodation['name'] }}</div>
                    
                    @if(!empty($accommodation['condition_rating']))
                        <div class="condition-text" style="margin-top: 0.2cm;">Condition Rating: {{ strtoupper($accommodation['condition_rating']) }}</div>
                    @endif

                    @if(!empty($accommodation['report_content']))
                        <div class="report-content">
                            {!! nl2br(e($accommodation['report_content'])) !!}
                        </div>
                    @elseif(!empty($accommodation['components']))
                        <div class="form-data">
                            @foreach($accommodation['components'] as $component)
                                <div class="component-details">
                                    <div class="component-name">{{ $component['component_name'] }}</div>
                                    @if(!empty($component['material']))
                                        <div class="form-data-row">
                                            <span class="form-data-label">Material:</span>
                                            <span class="form-data-value">{{ $component['material'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($component['defects']) && is_array($component['defects']) && count($component['defects']) > 0)
                                        <div class="form-data-row">
                                            <span class="form-data-label">Defects:</span>
                                            <span class="form-data-value">
                                                <ul class="defects-list">
                                                    @foreach($component['defects'] as $defect)
                                                        <li>{{ $defect }}</li>
                                                    @endforeach
                                                </ul>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            @if(!empty($accommodation['notes']))
                                <div class="form-data-row" style="margin-top: 0.3cm;">
                                    <span class="form-data-label">Notes:</span>
                                    <span class="form-data-value">{{ $accommodation['notes'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                @php $subSectionNumber++; @endphp
            @endforeach
        </div>
        @php $mainSectionNumber++; @endphp
    @endif

    <!-- Standalone Content Sections -->
    @if(!empty($contentSections['standalone']))
        <div class="category-section">
            <h1 class="category-title">{{ $mainSectionNumber }}.0 Content Sections</h1>
            
            @php
                $subSectionNumber = 1;
            @endphp
            @foreach($contentSections['standalone'] as $contentSection)
                <div class="content-section">
                    <div class="subsection-bar">{{ $mainSectionNumber }}.{{ $subSectionNumber }} {{ $contentSection->title }}</div>
                    <div class="content-section-body">
                        {!! nl2br(e($contentSection->content ?? '')) !!}
                    </div>
                </div>
                @php $subSectionNumber++; @endphp
            @endforeach
        </div>
    @endif

    <!-- Page Header -->
    <htmlpageheader name="html_pageHeader">
        <div class="page-header"></div>
    </htmlpageheader>

    <!-- Page Footer -->
    <htmlpagefooter name="html_pageFooter">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #2C2C54; border-collapse: collapse; margin: 0; padding: 0;">
            <tr>
                <td style="padding: 6px 10px; color: #FFFFFF; font-size: 9pt; font-family: Arial, Helvetica, sans-serif; vertical-align: middle;">
                    <div style="margin: 0; padding: 0; line-height: 1.3;">Flettons Surveyors 20-22 Wenlock Road, London, N1 7GU</div>
                    <div style="margin: 0; padding: 0; line-height: 1.3;"><strong>E:</strong> info@flettons.com | <strong>T:</strong> 0330 043 4650 | <strong>W:</strong> www.flettons.com</div>
                </td>
                <td style="padding: 6px 10px; color: #000000; font-size: 10pt; font-family: Arial, Helvetica, sans-serif; text-align: right; vertical-align: middle; width: 50px; background-color: transparent;">
                    {PAGENO}
                </td>
            </tr>
        </table>
    </htmlpagefooter>
</body>
</html>
