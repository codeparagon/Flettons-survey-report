@extends('layouts.app')

@section('title', 'Condition Rating Rules')

@push('styles')
<style>
    :root {
        --builder-primary: #1a202c;
        --builder-accent: #c1ec4a;
        --builder-success: #10b981;
        --builder-danger: #ef4444;
        --builder-warning: #f59e0b;
        --builder-border: #e5e7eb;
        --builder-bg: #f9fafb;
        --builder-hover: #f3f4f6;
    }

    .crr-page-header {
        background: var(--builder-primary);
        color: white;
        padding: 18px 24px;
        border-radius: 12px;
        margin-bottom: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-start;
        justify-content: space-between;
    }

    .crr-title {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: var(--builder-accent)!important;
    }

    .crr-subtitle {
        margin-top: 4px;
        color: rgba(255,255,255,0.85);
        font-size: 13px;
        max-width: 820px;
    }

    .crr-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(220px, 1fr));
        gap: 14px;
        align-items: start;
    }

    @media (max-width: 1200px) {
        .crr-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 600px) {
        .crr-grid { grid-template-columns: 1fr; }
    }

    .crr-bin {
        background: white;
        border: 1px solid var(--builder-border);
        border-radius: 12px;
        overflow: hidden;
        min-height: 420px;
        display: flex;
        flex-direction: column;
    }

    .crr-bin-header {
        background: var(--builder-bg);
        border-bottom: 1px solid var(--builder-border);
        padding: 12px 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .crr-bin-title {
        font-weight: 800;
        color: var(--builder-primary);
        font-size: 14px;
    }

    .crr-bin-count {
        font-size: 12px;
        color: #6b7280;
        font-weight: 700;
    }

    .crr-bin-list {
        padding: 12px;
        flex: 1;
        overflow-y: auto;
        min-height: 200px;
    }

    .crr-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: var(--builder-bg);
        border: 1px solid var(--builder-border);
        border-radius: 10px;
        margin-bottom: 8px;
        user-select: none;
    }

    .crr-chip-grip {
        color: #9ca3af;
        cursor: grab;
        font-size: 12px;
        flex: 0 0 auto;
    }

    .crr-chip-badge {
        font-size: 11px;
        font-weight: 800;
        color: white;
        background: var(--builder-primary);
        padding: 2px 8px;
        border-radius: 999px;
        text-transform: uppercase;
    }

    .crr-chip-label {
        font-size: 13px;
        color: #374151;
        word-break: break-word;
    }

    .crr-actions {
        margin-top: 16px;
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: flex-end;
    }

    .btn-builder {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .btn-builder-primary {
        background: var(--builder-accent);
        color: var(--builder-primary);
    }

    .btn-builder-primary:hover {
        background: #a8d83a;
    }

    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1100;
    }

    .toast {
        background: var(--builder-primary);
        color: white;
        padding: 14px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    .toast.success { background: var(--builder-success); }
    .toast.error { background: var(--builder-danger); }
</style>
@endpush

@section('content')
    <div>
        <div class="crr-page-header">
            <div>
                <h1 class="crr-title">Condition Rating Rules</h1>
                <div class="crr-subtitle">
                    Drag option chips between <b>Not Inspected (NI)</b> and <b>1 / 2 / 3</b>.
                    When a survey is saved, auto condition ratings will average the mapped option values (ignoring NI).
                </div>
            </div>
            <div class="d-flex" style="gap: 10px; flex-wrap: wrap;">
                <button type="button" class="btn-builder btn-builder-primary" onclick="saveConditionRatingRules()">
                    <i class="fas fa-save"></i> Save Rules
                </button>
            </div>
        </div>

        <div class="crr-grid">
            <div class="crr-bin" id="ratingBin-ni" data-bin-rating="ni">
                <div class="crr-bin-header">
                    <div class="crr-bin-title">Not Inspected (NI)</div>
                    <div class="crr-bin-count" data-count-for-bin="ni">0</div>
                </div>
                <div class="crr-bin-list" id="ratingBinList-ni">
                    @foreach(($bins['ni'] ?? []) as $chip)
                        <div class="crr-chip" data-option-type="{{ $chip['option_type'] }}" data-option-value="{{ $chip['option_value'] }}">
                            <span class="crr-chip-grip"><i class="fas fa-grip-vertical"></i></span>
                            <span class="crr-chip-badge">{{ $chip['option_type'] }}</span>
                            <span class="crr-chip-label">{{ $chip['display_value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="crr-bin" id="ratingBin-1" data-bin-rating="1">
                <div class="crr-bin-header">
                    <div class="crr-bin-title">1</div>
                    <div class="crr-bin-count" data-count-for-bin="1">0</div>
                </div>
                <div class="crr-bin-list" id="ratingBinList-1">
                    @foreach(($bins['1'] ?? []) as $chip)
                        <div class="crr-chip" data-option-type="{{ $chip['option_type'] }}" data-option-value="{{ $chip['option_value'] }}">
                            <span class="crr-chip-grip"><i class="fas fa-grip-vertical"></i></span>
                            <span class="crr-chip-badge">{{ $chip['option_type'] }}</span>
                            <span class="crr-chip-label">{{ $chip['display_value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="crr-bin" id="ratingBin-2" data-bin-rating="2">
                <div class="crr-bin-header">
                    <div class="crr-bin-title">2</div>
                    <div class="crr-bin-count" data-count-for-bin="2">0</div>
                </div>
                <div class="crr-bin-list" id="ratingBinList-2">
                    @foreach(($bins['2'] ?? []) as $chip)
                        <div class="crr-chip" data-option-type="{{ $chip['option_type'] }}" data-option-value="{{ $chip['option_value'] }}">
                            <span class="crr-chip-grip"><i class="fas fa-grip-vertical"></i></span>
                            <span class="crr-chip-badge">{{ $chip['option_type'] }}</span>
                            <span class="crr-chip-label">{{ $chip['display_value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="crr-bin" id="ratingBin-3" data-bin-rating="3">
                <div class="crr-bin-header">
                    <div class="crr-bin-title">3</div>
                    <div class="crr-bin-count" data-count-for-bin="3">0</div>
                </div>
                <div class="crr-bin-list" id="ratingBinList-3">
                    @foreach(($bins['3'] ?? []) as $chip)
                        <div class="crr-chip" data-option-type="{{ $chip['option_type'] }}" data-option-value="{{ $chip['option_value'] }}">
                            <span class="crr-chip-grip"><i class="fas fa-grip-vertical"></i></span>
                            <span class="crr-chip-badge">{{ $chip['option_type'] }}</span>
                            <span class="crr-chip-label">{{ $chip['display_value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type === 'success' ? 'success' : type === 'error' ? 'error' : ''}`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${escapeHtml(message)}
            `;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        async function apiCall(url, method = 'GET', data = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            };
            if (data && method !== 'GET') options.body = JSON.stringify(data);
            const response = await fetch(url, options);
            const payload = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(payload.message || `HTTP error ${response.status}`);
            }
            return payload;
        }

        function updateCounts() {
            ['ni', '1', '2', '3'].forEach(rating => {
                const list = document.getElementById(`ratingBinList-${rating}`);
                const countEl = document.querySelector(`[data-count-for-bin="${rating}"]`);
                const count = list ? list.querySelectorAll('.crr-chip').length : 0;
                if (countEl) countEl.textContent = count;
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            ['ni', '1', '2', '3'].forEach(rating => {
                const el = document.getElementById(`ratingBinList-${rating}`);
                if (!el) return;
                new Sortable(el, {
                    group: 'condition-rating-rules-bins',
                    animation: 150,
                    handle: '.crr-chip-grip',
                    draggable: '.crr-chip',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onAdd: updateCounts,
                    onRemove: updateCounts,
                    onSort: updateCounts
                });
            });
            updateCounts();
        });

        async function saveConditionRatingRules() {
            const payload = {
                material: { ni: [], '1': [], '2': [], '3': [] },
                defects: { ni: [], '1': [], '2': [], '3': [] }
            };

            ['ni', '1', '2', '3'].forEach(rating => {
                const list = document.getElementById(`ratingBinList-${rating}`);
                if (!list) return;

                list.querySelectorAll('.crr-chip').forEach(chip => {
                    const optionType = chip.dataset.optionType;
                    const optionValue = chip.dataset.optionValue;
                    if (!payload[optionType]) return;
                    if (!payload[optionType][rating]) return;
                    payload[optionType][rating].push(optionValue);
                });
            });

            try {
                const result = await apiCall('/admin/api/condition-rating-rules', 'POST', payload);
                if (result.success) {
                    showToast('Condition rating rules saved', 'success');
                } else {
                    showToast(result.message || 'Failed to save rules', 'error');
                }
            } catch (e) {
                console.error(e);
                showToast('Failed to save rules: ' + (e.message || 'Unknown error'), 'error');
            }
        }
    </script>
@endpush

