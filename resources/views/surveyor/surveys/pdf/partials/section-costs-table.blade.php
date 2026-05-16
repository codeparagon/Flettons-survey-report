@php
    $costGroups = $costsPdf['groups'] ?? [];
    $costTotal = (float) ($costsPdf['total'] ?? 0);
@endphp
<div class="section-costs-wrap">
    @foreach($costGroups as $categoryName => $costRows)
        @php
            $rowCount = is_countable($costRows) ? count($costRows) : 0;
            $splittable = $rowCount > 6;
            $isLastGroup = $loop->last;
        @endphp
        <table class="section-costs-table section-costs-table-block{{ $splittable ? ' section-costs-table-block--splittable' : '' }}{{ $isLastGroup ? ' section-costs-table-block--with-totals' : '' }}">
            <colgroup>
                <col class="section-costs-col-desc">
                <col class="section-costs-col-due">
                <col class="section-costs-col-amount">
            </colgroup>
            <thead>
                <tr>
                    <th class="section-costs-col-desc">Description of Works</th>
                    <th class="section-costs-col-due">Due</th>
                    <th class="section-costs-col-amount">Estimated Cost</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-costs-category-row">
                    <td colspan="3">{{ $categoryName }}</td>
                </tr>
                @foreach($costRows as $cost)
                    <tr class="section-costs-data-row">
                        <td class="section-costs-col-desc">{{ $cost['description'] ?? '' }}</td>
                        <td class="section-costs-col-due">{{ $cost['due'] ?? '' }}</td>
                        <td class="section-costs-col-amount">
                            @if(isset($pdfService))
                                {{ $pdfService->formatStoredCostAmountForPdf($cost['cost'] ?? $cost['amount'] ?? 0) }}
                            @else
                                £{{ $cost['cost'] ?? $cost['amount'] ?? '0' }}
                            @endif
                        </td>
                    </tr>
                @endforeach
                @if($isLastGroup)
                    @php
                        $formattedTotal = isset($pdfService)
                            ? $pdfService->formatCostAmountForPdf($costTotal)
                            : '£' . number_format($costTotal, 0, '.', ',');
                    @endphp
                    <tr class="section-costs-totals-row">
                        <td class="section-costs-totals-label">Totals</td>
                        <td class="section-costs-totals-due">&nbsp;</td>
                        <td class="section-costs-col-amount section-costs-totals-sum">{{ $formattedTotal }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</div>
