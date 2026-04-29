@extends('layouts.survey-mock')

@section('title', 'Desk Study')

@section('content')
@php
    $mapImageUrl = $deskStudy['map']['image_url'] ?? null;
    $updatedAtIso = $deskStudy['updated_at'] ?? null;
@endphp
<div class="survey-detail-screen" data-desk-study-root
     data-survey-id="{{ $survey->id }}"
     data-save-url="{{ route('surveyor.surveys.desk-study.save', $survey) }}"
     data-upload-url="{{ route('surveyor.surveys.desk-study.map-image', $survey) }}"
     data-delete-map-url="{{ route('surveyor.surveys.desk-study.map-image.delete', $survey) }}"
     data-csrf="{{ csrf_token() }}"
     data-updated-at="{{ $updatedAtIso }}">
    <section class="survey-detail-section survey-detail-section--headline">
        <div class="survey-detail-headline">
            <div class="survey-detail-location">
                <i class="fas fa-chevron-left survey-detail-location-icon"></i>
                <input type="text" class="sdm-desk-field" data-field="address" value="{{ $deskStudy['address'] }}" placeholder="Address" style="width:100%; border:0; background:transparent; font:inherit; outline:none;" />
            </div>
            <div class="survey-detail-jobref">
                <span class="survey-detail-jobref-label">Job Reference</span>
                <input type="text" class="survey-detail-jobref-value sdm-desk-field" data-field="job_reference" value="{{ $deskStudy['job_reference'] }}" placeholder="Job reference" style="border:0; background:transparent; font:inherit; outline:none; text-align:right;" />
            </div>
        </div>
    </section>

    <section class="survey-detail-section">
        <div class="survey-detail-grid survey-detail-grid--two">
            <article class="survey-detail-card">
                <header class="survey-detail-card-header">
                    <div class="ds-card-header">
                        <div class="ds-card-title">
                            <span class="ds-card-icon"><i class="fa-solid fa-location-dot"></i></span>
                            <h3>Location Overview</h3>
                        </div>
                        <span class="ds-chip ds-chip--muted">Primary Property</span>
                    </div>
                </header>
                <div class="survey-detail-card-body">
                    <div class="sdm-map-upload">
                        <div class="sdm-map-preview-wrap">
                            @if($mapImageUrl)
                                <img src="{{ $mapImageUrl }}" alt="Location map preview" class="img-fluid rounded sdm-map-preview" />
                            @else
                                <div class="sdm-map-empty rounded">
                                    <div class="sdm-map-empty-title">Upload map screenshot</div>
                                    <div class="sdm-map-empty-sub">Drag & drop an image here, or click to browse.</div>
                                </div>
                            @endif
                        </div>
                        <input type="file" class="sdm-map-file" accept="image/*" style="display:none;" />
                        <div class="sdm-map-actions" style="margin-top:0.75rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                            <button type="button" class="btn btn-sm btn-primary sdm-map-browse">Upload screenshot</button>
                            <button type="button" class="btn btn-sm btn-outline-danger sdm-map-remove" @if(!$mapImageUrl) disabled @endif>Remove</button>
                            <span class="sdm-save-indicator" style="margin-left:auto; font-size:0.875rem; color:#64748b;"></span>
                        </div>
                    </div>
                    <div class="ds-kv-strip">
                        <div class="ds-kv">
                            <div class="ds-kv-icon"><i class="fa-solid fa-globe"></i></div>
                            <div class="ds-kv-meta">
                                <div class="ds-kv-label">Longitude</div>
                                <input type="text" class="ds-kv-input sdm-desk-field" data-field="longitude" value="{{ $deskStudy['map']['longitude'] }}" placeholder="Longitude" />
                            </div>
                        </div>
                        <div class="ds-kv">
                            <div class="ds-kv-icon"><i class="fa-solid fa-location-crosshairs"></i></div>
                            <div class="ds-kv-meta">
                                <div class="ds-kv-label">Latitude</div>
                                <input type="text" class="ds-kv-input sdm-desk-field" data-field="latitude" value="{{ $deskStudy['map']['latitude'] }}" placeholder="Latitude" />
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="survey-detail-card">
                <header class="survey-detail-card-header">
                    <div class="ds-card-header">
                        <div class="ds-card-title">
                            <span class="ds-card-icon"><i class="fa-solid fa-water"></i></span>
                            <h3>Flood Risk Summary</h3>
                        </div>
                    </div>
                </header>
                <div class="survey-detail-card-body">
                    <div class="sdm-repeater" data-repeater="flood_risks">
                        @foreach ($deskStudy['flood_risks'] as $i => $risk)
                            <div class="sdm-row ds-list-row">
                                <div class="ds-list-icon">
                                    <i class="fa-solid fa-droplet"></i>
                                </div>
                                <div class="ds-list-main">
                                    <input type="text" class="ds-list-input" data-part="label" value="{{ $risk['label'] }}" placeholder="Label" />
                                </div>
                                <div class="ds-list-right">
                                    <span class="ds-status-dot"></span>
                                    <input type="text" class="ds-list-input ds-list-input--right" data-part="value" value="{{ $risk['value'] }}" placeholder="Value" />
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger sdm-row-remove ds-row-remove" aria-label="Remove">×</button>
                            </div>
                        @endforeach
                        <button type="button" class="btn btn-sm btn-outline-primary sdm-row-add">Add</button>
                    </div>
                    <div class="ds-risk-insight">
                        <div class="ds-risk-insight-head">
                            <span class="ds-risk-shield"><i class="fa-solid fa-shield-halved"></i></span>
                            <div class="ds-risk-title">Risk Insight</div>
                        </div>
                        <div class="ds-risk-body" data-risk-insight>
                            The property is assessed to have a very low risk of flooding from rivers and seas.
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section class="survey-detail-section" id="desk-study-planning">
        <article class="survey-detail-card">
            <header class="survey-detail-card-header">
                <div class="ds-card-header">
                    <div class="ds-card-title">
                        <span class="ds-card-icon"><i class="fa-solid fa-clipboard-check"></i></span>
                        <h3>Planning & Compliance</h3>
                    </div>
                </div>
            </header>
            <div class="survey-detail-card-body">
                <div class="sdm-repeater" data-repeater="planning">
                    @foreach ($deskStudy['planning'] as $i => $item)
                        <div class="sdm-row ds-grid-row">
                            <div class="ds-grid-col">
                                <div class="ds-grid-label">Label</div>
                                <input type="text" class="ds-grid-input" data-part="label" value="{{ $item['label'] }}" placeholder="Label" />
                            </div>
                            <div class="ds-grid-col">
                                <div class="ds-grid-label">Value</div>
                                <input type="text" class="ds-grid-input" data-part="value" value="{{ $item['value'] }}" placeholder="Value" />
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger sdm-row-remove ds-row-remove" aria-label="Remove">×</button>
                        </div>
                    @endforeach
                    <button type="button" class="btn btn-sm btn-outline-primary sdm-row-add">Add</button>
                </div>
            </div>
        </article>
    </section>

    <section class="survey-detail-section">
        <div class="ds-footer-bar">
            <div class="ds-footer-item">
                <i class="fa-regular fa-clock"></i>
                <span>Last updated:</span>
                <strong data-last-updated>—</strong>
            </div>
            <div class="ds-footer-item ds-footer-center">
                <i class="fa-regular fa-file-lines"></i>
                <span>Data sources:</span>
                <strong>Environment Agency, GOV.UK, Ordnance Survey</strong>
            </div>
            <div class="ds-footer-item ds-footer-right">
                <span class="ds-confidence">
                    <i class="fa-regular fa-circle-check"></i>
                    <span>Confidence:</span>
                    <strong>High</strong>
                </span>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/survey-detail-theme.css') }}">
    <style>
        :root { --ds-accent: #C1EC4A; }
        .sdm-map-empty { border: 1px dashed #cbd5e1; padding: 2rem 1.25rem; text-align: center; background: #f8fafc; }
        .sdm-map-empty-title { font-weight: 600; color: #0f172a; }
        .sdm-map-empty-sub { margin-top: 0.25rem; color: #64748b; font-size: 0.875rem; }
        .sdm-map-upload.is-dragover .sdm-map-empty,
        .sdm-map-upload.is-dragover .sdm-map-preview-wrap { outline: 2px solid #3b82f6; outline-offset: 2px; }

        /* Screenshot-like spacing/icon sizing */
        .survey-detail-card-header h3 { margin: 0; font-size: 1.05rem; font-weight: 600; color: #0f172a; }
        .ds-card-header { display:flex; align-items:center; justify-content:space-between; gap: 0.75rem; }
        .ds-card-title { display:flex; align-items:center; gap: 0.6rem; min-width: 0; }
        .ds-card-icon { width: 34px; height: 34px; border-radius: 10px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; color:var(--ds-accent); flex: 0 0 auto; }
        .ds-card-icon i { font-size: 1rem; }
        .ds-chip { font-size: 0.7rem; letter-spacing: .06em; text-transform: uppercase; padding: .3rem .5rem; border-radius: 999px; border: 1px solid #e2e8f0; }
        .ds-chip--muted { background:#f8fafc; color:#64748b; }

        .ds-kv-strip { display:grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1rem; }
        .ds-kv { display:flex; align-items:center; gap: 0.7rem; border:1px solid #e2e8f0; border-radius: 14px; padding: .75rem .85rem; background:#fff; }
        .ds-kv-icon { width: 34px; height: 34px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; color:#334155; }
        .ds-kv-icon i { font-size: 1rem; }
        .ds-kv-meta { min-width:0; width:100%; }
        .ds-kv-label { font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; color:#64748b; margin-bottom: .15rem; }
        .ds-kv-input { width:100%; border:0; padding:0; background:transparent; outline:none; font-weight: 600; color:#0f172a; }

        .ds-list-row { display:flex; align-items:center; gap: .75rem; padding: .65rem .25rem; border-bottom: 1px solid #eef2f7; }
        .ds-list-row:last-of-type { border-bottom: 0; }
        .ds-list-icon { width: 34px; height: 34px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background:#f1f5f9; color:#334155; flex: 0 0 auto; }
        .ds-list-icon i { font-size: 1rem; }
        .ds-list-main { flex: 1 1 auto; min-width: 0; }
        .ds-list-right { display:flex; align-items:center; gap: .4rem; flex: 0 0 40%; justify-content: flex-end; }
        .ds-status-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--ds-accent); flex: 0 0 auto; }
        .ds-list-input { width:100%; border:0; background:transparent; outline:none; padding: .1rem .2rem; color:#0f172a; }
        .ds-list-input--right { text-align:right; font-weight: 600; }
        .ds-row-remove { width: 30px; height: 30px; border-radius: 10px; line-height: 1; padding: 0; display:flex; align-items:center; justify-content:center; }

        .ds-grid-row { display:grid; grid-template-columns: 1fr 1fr auto; gap: .75rem; align-items:end; padding: .5rem 0; border-bottom: 1px solid #eef2f7; }
        .ds-grid-row:last-of-type { border-bottom: 0; }
        .ds-grid-label { font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; color:#64748b; margin-bottom: .2rem; }
        .ds-grid-input { width:100%; border:1px solid #e2e8f0; border-radius: 12px; padding: .55rem .65rem; outline:none; background:#fff; }
        .ds-grid-input:focus { border-color: #94a3b8; box-shadow: none; }

        .ds-risk-insight { margin-top: 1rem; border: 1px solid rgba(193, 236, 74, 0.45); background: rgba(193, 236, 74, 0.14); border-radius: 14px; padding: .85rem .9rem; }
        .ds-risk-insight-head { display:flex; align-items:center; gap: .55rem; }
        .ds-risk-shield { width: 34px; height: 34px; border-radius: 12px; display:flex; align-items:center; justify-content:center; background: rgba(193, 236, 74, 0.35); color: #334155; flex: 0 0 auto; }
        .ds-risk-title { font-weight: 700; color:#0f172a; }
        .ds-risk-body { margin-top: .35rem; color:#334155; font-size: .9rem; line-height: 1.35; }

        .ds-footer-bar { display:flex; align-items:center; justify-content:space-between; gap: 1rem; padding: .7rem .25rem; color:#64748b; font-size: .82rem; }
        .ds-footer-item { display:flex; align-items:center; gap: .5rem; min-width: 0; }
        .ds-footer-item i { color:#94a3b8; }
        .ds-footer-item strong { color:#334155; font-weight: 600; }
        .ds-footer-center { flex: 1 1 auto; justify-content:center; text-align:center; }
        .ds-footer-right { justify-content:flex-end; }
        .ds-confidence { display:inline-flex; align-items:center; gap: .5rem; padding: .35rem .6rem; border-radius: 999px; border: 1px solid rgba(193, 236, 74, 0.45); background: rgba(193, 236, 74, 0.14); color:#0f172a; }
        .ds-confidence i { color: var(--ds-accent); }

        @media (max-width: 768px) {
            .ds-kv-strip { grid-template-columns: 1fr; }
            .ds-list-right { flex-basis: 48%; }
            .ds-grid-row { grid-template-columns: 1fr; }
            .ds-footer-bar { flex-direction: column; align-items:flex-start; gap: .5rem; }
            .ds-footer-center { justify-content:flex-start; text-align:left; }
            .ds-footer-right { justify-content:flex-start; }
        }
    </style>
@endpush

@push('scripts')
<script>
(() => {
    const root = document.querySelector('[data-desk-study-root]');
    if (!root) return;

    const saveUrl = root.getAttribute('data-save-url');
    const uploadUrl = root.getAttribute('data-upload-url');
    const deleteMapUrl = root.getAttribute('data-delete-map-url');
    const csrf = root.getAttribute('data-csrf');
    const lastUpdatedEl = root.querySelector('[data-last-updated]');
    const initialUpdatedAt = root.getAttribute('data-updated-at');

    const indicator = root.querySelector('.sdm-save-indicator');
    const setIndicator = (text, isError=false) => {
        if (!indicator) return;
        indicator.textContent = text || '';
        indicator.style.color = isError ? '#ef4444' : '#64748b';
    };

    function collectRepeater(name) {
        const block = root.querySelector(`.sdm-repeater[data-repeater="${name}"]`);
        if (!block) return [];
        const rows = [...block.querySelectorAll('.sdm-row')];
        return rows.map(r => {
            const label = (r.querySelector('[data-part="label"]')?.value || '').trim();
            const value = (r.querySelector('[data-part="value"]')?.value || '').trim();
            return { label, value };
        }).filter(x => x.label || x.value);
    }

    function collectData() {
        const address = (root.querySelector('[data-field="address"]')?.value || '').trim();
        const job_reference = (root.querySelector('[data-field="job_reference"]')?.value || '').trim();
        const longitude = (root.querySelector('[data-field="longitude"]')?.value || '').trim();
        const latitude = (root.querySelector('[data-field="latitude"]')?.value || '').trim();
        return {
            address,
            job_reference,
            longitude,
            latitude,
            flood_risks: collectRepeater('flood_risks'),
            planning: collectRepeater('planning'),
        };
    }

    function fmtUpdatedAt(iso) {
        if (!iso) return '—';
        const d = new Date(iso);
        if (Number.isNaN(d.getTime())) return '—';
        return d.toLocaleString();
    }

    function setLastUpdated(iso) {
        if (!lastUpdatedEl) return;
        lastUpdatedEl.textContent = fmtUpdatedAt(iso);
    }

    setLastUpdated(initialUpdatedAt);

    let saveTimer = null;
    let lastToastAt = 0;
    function toastOncePer(ms, fn) {
        const now = Date.now();
        if (now - lastToastAt < ms) return;
        lastToastAt = now;
        try { fn(); } catch (_) {}
    }

    async function saveDebounced() {
        setIndicator('Saving…');
        clearTimeout(saveTimer);
        saveTimer = setTimeout(async () => {
            try {
                const payload = collectData();
                const res = await fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json.success) {
                    const msg = (json && (json.message || json.error)) ? (json.message || json.error) : 'Save failed';
                    setIndicator(msg, true);
                    if (window.toastr) {
                        toastOncePer(2000, () => window.toastr.error(msg));
                    }
                    return;
                }
                setIndicator('Saved');
                setTimeout(() => setIndicator(''), 1200);
                if (json.updated_at) setLastUpdated(json.updated_at);
                if (window.toastr) {
                    toastOncePer(2000, () => window.toastr.success('Saved'));
                }
            } catch (e) {
                setIndicator('Save failed', true);
                if (window.toastr) {
                    toastOncePer(2000, () => window.toastr.error('Save failed'));
                }
            }
        }, 400);
    }

    // Any field change triggers save
    root.addEventListener('input', (e) => {
        if (e.target && (e.target.classList.contains('sdm-desk-field') || e.target.matches('.sdm-repeater input'))) {
            saveDebounced();
        }
    });

    // Repeater add/remove
    root.querySelectorAll('.sdm-repeater').forEach(block => {
        block.addEventListener('click', (e) => {
            const addBtn = e.target.closest('.sdm-row-add');
            if (addBtn) {
                e.preventDefault();
                const row = document.createElement('div');
                row.className = 'sdm-row';
                row.style.cssText = 'display:flex; gap:0.5rem; align-items:center; margin:0 0 0.5rem 0;';
                row.innerHTML = `
                    <input type="text" class="form-control form-control-sm" data-part="label" placeholder="Label" />
                    <input type="text" class="form-control form-control-sm" data-part="value" placeholder="Value" />
                    <button type="button" class="btn btn-sm btn-outline-danger sdm-row-remove">×</button>
                `;
                addBtn.parentElement.insertBefore(row, addBtn);
                row.querySelector('input')?.focus();
                saveDebounced();
                return;
            }
            const rmBtn = e.target.closest('.sdm-row-remove');
            if (rmBtn) {
                e.preventDefault();
                rmBtn.closest('.sdm-row')?.remove();
                saveDebounced();
            }
        });
    });

    // Map upload
    const mapWrap = root.querySelector('.sdm-map-upload');
    const fileInput = root.querySelector('.sdm-map-file');
    const browseBtn = root.querySelector('.sdm-map-browse');
    const removeBtn = root.querySelector('.sdm-map-remove');
    const previewWrap = root.querySelector('.sdm-map-preview-wrap');

    const openPicker = () => fileInput && fileInput.click();
    if (browseBtn) browseBtn.addEventListener('click', (e) => { e.preventDefault(); openPicker(); });
    if (previewWrap) previewWrap.addEventListener('click', () => openPicker());

    async function uploadFile(file) {
        if (!file) return;
        setIndicator('Uploading image…');
        const fd = new FormData();
        fd.append('map_image', file);
        try {
            const res = await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                body: fd,
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok || !json.success || !json.map_image_url) {
                const msg = (json && (json.message || json.error)) ? (json.message || json.error) : 'Upload failed';
                setIndicator(msg, true);
                if (window.toastr) {
                    window.toastr.error(msg);
                }
                return;
            }
            // render preview
            if (previewWrap) {
                previewWrap.innerHTML = `<img src="${json.map_image_url}" alt="Location map preview" class="img-fluid rounded sdm-map-preview" />`;
            }
            if (removeBtn) removeBtn.disabled = false;
            setIndicator('Uploaded');
            setTimeout(() => setIndicator(''), 1200);
            if (json.updated_at) setLastUpdated(json.updated_at);
            if (window.toastr) {
                window.toastr.success('Map screenshot uploaded');
            }
        } catch (e) {
            setIndicator('Upload failed', true);
            if (window.toastr) {
                window.toastr.error('Upload failed');
            }
        }
    }

    if (fileInput) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files && fileInput.files[0];
            uploadFile(file);
            fileInput.value = '';
        });
    }

    if (mapWrap) {
        ['dragenter','dragover'].forEach(evt => mapWrap.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            mapWrap.classList.add('is-dragover');
        }));
        ['dragleave','drop'].forEach(evt => mapWrap.addEventListener(evt, (e) => {
            e.preventDefault();
            e.stopPropagation();
            mapWrap.classList.remove('is-dragover');
        }));
        mapWrap.addEventListener('drop', (e) => {
            const file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            uploadFile(file);
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (removeBtn.disabled) return;
            setIndicator('Removing image…');
            try {
                const res = await fetch(deleteMapUrl, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json.success) {
                    const msg = (json && (json.message || json.error)) ? (json.message || json.error) : 'Remove failed';
                    setIndicator(msg, true);
                    if (window.toastr) {
                        window.toastr.error(msg);
                    }
                    return;
                }
                if (previewWrap) {
                    previewWrap.innerHTML = `
                        <div class="sdm-map-empty rounded">
                            <div class="sdm-map-empty-title">Upload map screenshot</div>
                            <div class="sdm-map-empty-sub">Drag & drop an image here, or click to browse.</div>
                        </div>
                    `;
                }
                removeBtn.disabled = true;
                setIndicator('Removed');
                setTimeout(() => setIndicator(''), 1200);
                if (json.updated_at) setLastUpdated(json.updated_at);
                if (window.toastr) {
                    window.toastr.success('Map screenshot removed');
                }
            } catch (e2) {
                setIndicator('Remove failed', true);
                if (window.toastr) {
                    window.toastr.error('Remove failed');
                }
            }
        });
    }
})();
</script>
@endpush