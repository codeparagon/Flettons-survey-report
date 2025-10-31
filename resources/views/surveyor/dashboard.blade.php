@extends('layouts.app')

@section('title', 'Surveyor Dashboard')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="survai-dashboard">
<div class="row sd-hero">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title sd-title">Your Performance Dashboard</h2>
            <p class="pageheader-text sd-sub">Welcome, {{ auth()->user()->name ?? 'Surveyor' }}</p>
        </div>
    </div>
</div>

<div class="row sd-row">
    <!-- Gauges Row -->
    <div class="col-md-4 mb-3">
        <div class="sd-card">
            <div class="sd-card-body text-center">
                <div class="sd-card-title">Reports On Time</div>
                <div class="sd-gauge">
                    <div class="sd-gauge-box">
                        <div class="sd-donut" id="ontimeDonut" data-value="95">
                            <svg viewBox="0 0 100 100" class="sd-donut-svg">
                                <circle class="sd-donut-track" cx="50" cy="50" r="42"/>
                                <circle class="sd-donut-bar" cx="50" cy="50" r="42"/>
                                <text class="sd-donut-text" x="50" y="55">95%</text>
                            </svg>
                        </div>
                </div>
                    <div class="sd-gauge-scale"><span>0</span><span>100</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="sd-card">
            <div class="sd-card-body text-center">
                <div class="sd-card-title">Overall Performance</div>
                <div class="sd-gauge-box">
                    <div class="sd-donut sd-dial" id="performanceDial" data-value="84">
                        <svg viewBox="0 0 100 100" class="sd-donut-svg">
                            <circle class="sd-donut-track" cx="50" cy="50" r="42"/>
                            <circle class="sd-donut-bar" cx="50" cy="50" r="42"/>
                            <g class="sd-ticks">
                                <!-- 12 ticks placed INSIDE ring (closer to center) -->
                                <g class="sd-t" transform="rotate(0 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(30 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(60 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(90 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(120 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(150 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(180 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(210 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(240 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(270 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(300 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                                <g class="sd-t" transform="rotate(330 50 50)"><line x1="50" y1="20" x2="50" y2="15"/></g>
                            </g>
                            <g class="sd-needle" id="performanceNeedle"><line x1="50" y1="50" x2="50" y2="10"/></g>
                            <text class="sd-donut-text" x="50" y="55">84</text>
                        </svg>
                </div>
                </div>
                <!-- <div class="sd-card-caption">Overall<br>Performance</div> -->
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="sd-card">
            <div class="sd-card-body text-center">
                <div class="sd-card-title">Active Jobs</div>
                <div class="sd-gauge-box">
                    <div class="sd-donut" id="activeDonut" data-value="50">
                        <svg viewBox="0 0 100 100" class="sd-donut-svg">
                            <circle class="sd-donut-track" cx="50" cy="50" r="42"/>
                            <circle class="sd-donut-bar" cx="50" cy="50" r="42"/>
                            <text class="sd-donut-text" x="50" y="55">20</text>
                        </svg>
                </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    
<div class="row sd-row">
    <div class="col-md-6 mb-3">
        <div class="sd-card">
            <div class="sd-card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="sd-card-title m-0">Completion Rate</div>
                    <div id="completionRateLabel" class="sd-kpi">0%</div>
                </div>
                <div class="sd-progress">
                    <div id="completionProgressBar" class="sd-progress-bar" style="width:0%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="sd-card">
            <div class="sd-card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="sd-card-title m-0">Productivity History</div>
                    <div class="sd-kpi sd-kpi-warm" id="prodAvgLabel"></div>
</div>
                <canvas id="productivityLine" height="120"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- All extra sections removed for a clean dashboard focused on the graphs -->
</div>
@endsection

@push('styles')
<style>
/* Page-wide dark background to keep color constant */
.dashboard-wrapper, .dashboard-content, body {
    background-color: #1a202c !important;
}

/* Scoped dashboard styling */
.survai-dashboard { color:#E5E7EB; }
.survai-dashboard .sd-hero { background:#1a202c; margin-left:-12px; margin-right:-12px; padding:16px; border-radius:8px; }
.survai-dashboard .sd-title { color:#C1EC4A!important; font-weight:800; letter-spacing:0.2px; }
.survai-dashboard .sd-sub { color:#A7B0BE; }
.survai-dashboard .sd-row { background:#1a202c; margin-left:-12px; margin-right:-12px; padding:8px 16px 16px 16px; border-radius:12px; }
.survai-dashboard .sd-card { background:#1a202c; border:1px solid rgba(255,255,255,0.06); border-radius:16px; box-shadow: 0 8px 24px rgba(0,0,0,0.35); }
.survai-dashboard .sd-card-body { padding:12px; }
.survai-dashboard .sd-card-title { color:#E5E7EB; font-weight:700; margin-bottom:6px; }
.survai-dashboard .sd-card-caption { color:#A7B0BE; font-weight:700; line-height:1.1; margin-top:8px; }
.survai-dashboard .sd-progress { height:16px; background:#1F2937; border-radius:999px; overflow:hidden; }
.survai-dashboard .sd-progress-bar { height:100%; background:#C1EC4A; border-radius:999px; transition:width .6s ease; }
.survai-dashboard .sd-kpi { color:#C1EC4A; font-weight:800; }
.survai-dashboard .sd-kpi-warm { color:#F59E0B; }
.survai-dashboard .sd-gauge { position:relative; display:flex; justify-content:center; align-items:center; }
.survai-dashboard .sd-gauge { margin-top:4px; }
.survai-dashboard .sd-gauge-scale { position:absolute; bottom:-6px; left:0; right:0; display:flex; justify-content:space-between; color:#9CA3AF; font-size:12px; }
.survai-dashboard .sd-gauge-box { position:relative; width:100%; max-width:280px;margin: 0 auto; }
/* .survai-dashboard .sd-gauge-box::before { content:""; display:block; padding-top:100%; } */
.survai-dashboard .sd-gauge-box canvas { position:absolute; top:0; left:0; width:100% !important; height:100% !important; display:block; filter: drop-shadow(0 0 12px rgba(193,236,74,0.18)); }

/* SVG donut styles */
.sd-donut-svg { width:100%; height:100%; }
.sd-donut-track { fill:none; stroke:#1F2937; stroke-width:8; }
.sd-donut-bar { fill:none; stroke:#C1EC4A; stroke-width:8; stroke-linecap:round; transform: rotate(-90deg); transform-origin: 50% 50%; stroke-dasharray: 0 999; transition: stroke-dasharray .6s ease; }
.sd-donut-text { fill:#E5E7EB; font-weight:800; font-size:20px; text-anchor:middle; dominant-baseline:middle; }
.sd-t line { stroke:#2f3a4a; stroke-width:1.5; }
.sd-needle line { stroke:#C1EC4A; stroke-width:2.5; transform-origin: 50% 50%; }
.sd-needle circle { fill:#C1EC4A; }
.sd-needle { transform-origin: 50% 50%; }

/* SurvAI Branding for Buttons and Badges - High Specificity */
.card-body .btn-primary,
.btn-primary,
a.btn-primary,
button.btn-primary {
    background-color: #C1EC4A !important;
    border-color: #C1EC4A !important;
    color: #1A202C !important;
    font-weight: 600 !important;
    padding: 12px 20px !important;
    border-radius: 6px !important;
    display: inline-block !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

.card-body .btn-primary:hover,
.btn-primary:hover,
a.btn-primary:hover,
button.btn-primary:hover {
    background-color: #B0D93F !important;
    border-color: #B0D93F !important;
    color: #1A202C !important;
    text-decoration: none !important;
    box-shadow: none !important;
}

/* Badge Styling */
.badge-info,
span.badge-info {
    background-color: #C1EC4A !important;
    color: #1A202C !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

.badge-success,
span.badge-success {
    background-color: #C1EC4A !important;
    color: #1A202C !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

.badge-warning,
span.badge-warning {
    background-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

.badge-danger,
span.badge-danger {
    background-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

.badge-secondary,
span.badge-secondary {
    background-color: #1A202C !important;
    color: #C1EC4A !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
}

/* Small button styling */
.btn-sm {
    padding: 8px 16px !important;
    font-size: 14px !important;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const brandGreen = '#C1EC4A';
    const brandInk = '#1A202C';
    const darkBg = '#111827';
    const track = '#1F2937';

    // Sample KPI calculations
    const total = {{ (int)($totalJobs) }};
    const pending = {{ (int)($pendingJobs) }};
    const inProg = {{ (int)($inProgressJobs) }};
    const completed = {{ (int)($completedJobs) }};
    const onTimePct = completed > 0 ? 95 : 0; // placeholder; replace with real metric if available
    const performance = Math.min(100, Math.round((completed * 2 + inProg) / Math.max(1,total) * 100 / 3));
    const activeJobs = inProg || 0;
    const completionRate = total > 0 ? Math.round((completed / total) * 100) : 0;

    const CR = document.getElementById('completionRateLabel');
    const CRBar = document.getElementById('completionProgressBar');
    if (CR) CR.textContent = completionRate + '%';
    if (CRBar){
        CRBar.style.width = completionRate + '%';
        CRBar.setAttribute('aria-valuenow', completionRate);
    }

    // Plugin to render center value text
    const centerTextPlugin = {
        id: 'centerText',
        afterDraw(chart, args, opts){
            const {ctx, chartArea:{left, right, top, bottom}} = chart;
            const value = chart.config.data.datasets[0].data[0];
            const suffix = chart.canvas && chart.canvas.dataset && chart.canvas.dataset.suffix ? chart.canvas.dataset.suffix : '';
            ctx.save();
            ctx.fillStyle = '#E5E7EB';
            ctx.font = '700 28px Inter, system-ui, -apple-system, Segoe UI, Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const text = suffix === '%' ? parseInt(value,10) + '%' : parseInt(value, 10);
            ctx.fillText(text, (left+right)/2, (top+bottom)/2);
            ctx.restore();
        }
    };

    // SVG donuts - compute stroke for each
    function renderSvgDonut(wrapperId, value, text){
        const el = document.getElementById(wrapperId);
        if (!el) return;
        const svg = el.querySelector('svg');
        const bar = svg.querySelector('.sd-donut-bar');
        const label = svg.querySelector('.sd-donut-text');
        const r = 42;
        const circ = 2 * Math.PI * r;
        const val = Math.max(0, Math.min(100, value));
        bar.style.strokeDasharray = (circ * val/100) + ' ' + circ;
        if (typeof text === 'string') label.textContent = text; else label.textContent = val + '%';
    }

    renderSvgDonut('ontimeDonut', onTimePct, onTimePct + '%');

    // Performance dial with needle
    (function(){
        const id = 'performanceDial';
        const el = document.getElementById(id);
        if (!el) return;
        const svg = el.querySelector('svg');
        const bar = svg.querySelector('.sd-donut-bar');
        const needle = svg.querySelector('#performanceNeedle');
        const text = svg.querySelector('.sd-donut-text');
        const r = 42; const circ = 2 * Math.PI * r; const val = Math.max(0, Math.min(100, performance));
        bar.style.strokeDasharray = (circ * val/100) + ' ' + circ;
        // Position needle via geometry to avoid overlay across center
        const angleDeg = -90 + (val * 3.6);
        const angle = angleDeg * Math.PI / 180;
        const cx = 50, cy = 50;
        const inner = 24; // start a bit away from center
        const outer = 42; // to ring
        const x1 = cx + Math.cos(angle) * inner;
        const y1 = cy + Math.sin(angle) * inner;
        const x2 = cx + Math.cos(angle) * (outer - 4);
        const y2 = cy + Math.sin(angle) * (outer - 4);
        const line = needle.querySelector('line');
        line.setAttribute('x1', x1.toFixed(2));
        line.setAttribute('y1', y1.toFixed(2));
        line.setAttribute('x2', x2.toFixed(2));
        line.setAttribute('y2', y2.toFixed(2));
        text.textContent = Math.round(performance);
    })();

    // Active jobs donut based on ratio vs max(10,total)
    (function(){
        const maxJobs = Math.max(10, total || 10);
        const pct = Math.min(100, Math.round((activeJobs / maxJobs) * 100));
        renderSvgDonut('activeDonut', pct, String(activeJobs));
    })();

    // Productivity history (dummy trend based on job states)
    const labels = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    const values = [2,3,1,4,5,2,3];
    const avg = values.reduce((a,b)=>a+b,0)/values.length;
    const avgEl = document.getElementById('prodAvgLabel');
    if (avgEl){ avgEl.textContent = avg.toFixed(1) + ' days'; }
    const prodCtx = document.getElementById('productivityLine');
    if (prodCtx){
        new Chart(prodCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    data: values,
                    borderColor: brandGreen,
                    backgroundColor: 'rgba(193,236,74,0.15)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0
                }]
            },
            options: {
                plugins: { legend:{display:false} },
                scales: {
                    x: { grid: { display:false }, ticks:{ color:'#9CA3AF' } },
                    y: { grid: { color:'#1F2937' }, ticks:{ color:'#9CA3AF', precision:0 } }
                }
            }
        });
    }
});
</script>
@endpush


