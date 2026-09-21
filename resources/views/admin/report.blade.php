@php
    $has = fn ($key) => in_array($key, $sections, true);
    $firstAt = $totals->first_at ? \Carbon\Carbon::parse($totals->first_at, 'UTC')->timezone('Asia/Manila') : null;
    $lastAt = $totals->last_at ? \Carbon\Carbon::parse($totals->last_at, 'UTC')->timezone('Asia/Manila') : null;
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Canteen Stall Evaluation Report — {{ $periodLabel }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: oklch(0.21 0.02 155);
            --ink-2: oklch(0.40 0.02 155);
            --ink-3: oklch(0.52 0.015 155);
            --rule: oklch(0.86 0.012 155);
            --rule-strong: oklch(0.30 0.06 155);
            --brand: oklch(0.42 0.12 155);
            --brand-deep: oklch(0.30 0.09 155);
            --brand-soft: oklch(0.96 0.025 155);
            --low: oklch(0.50 0.17 28);
            --low-soft: oklch(0.96 0.025 28);
            --desk: oklch(0.925 0.01 155);
            --panel: oklch(0.985 0.004 155);
            --paper: #fff;
            --radius: 10px;
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body {
            margin: 0;
            background: var(--desk);
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
        ::selection { background: oklch(0.85 0.09 155); color: var(--ink); }
        :focus-visible { outline: 2px solid var(--brand); outline-offset: 2px; }
        button, input { font: inherit; color: inherit; }
        input[type="checkbox"], input[type="radio"] { accent-color: var(--brand); width: 16px; height: 16px; margin: 0; flex: none; }

        .desk {
            display: grid;
            grid-template-columns: 21rem minmax(0, 1fr);
            gap: 32px;
            align-items: start;
            padding: 24px 32px 64px 0;
        }

        /* ── Options panel ─────────────────────────────── */
        .panel {
            position: sticky;
            top: 0;
            height: 100vh;
            height: 100dvh;
            overflow-y: auto;
            background: var(--panel);
            border-right: 1px solid var(--rule);
            margin-top: -24px;
            display: flex;
            flex-direction: column;
        }
        .panel-head { padding: 20px 22px 16px; border-bottom: 1px solid var(--rule); }
        .back {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12.5px; font-weight: 600; color: var(--ink-2); text-decoration: none;
        }
        .back:hover { color: var(--brand-deep); }
        .back svg { width: 14px; height: 14px; }
        .panel-title { font-family: 'Sora', sans-serif; font-size: 18px; font-weight: 700; margin: 12px 0 2px; letter-spacing: -0.02em; }
        .panel-sub { margin: 0; font-size: 12.5px; color: var(--ink-2); }
        .panel form { padding: 6px 22px 0; flex: 1; display: flex; flex-direction: column; }
        fieldset { border: 0; margin: 0; padding: 16px 0; border-bottom: 1px solid var(--rule); min-width: 0; }
        legend { padding: 0; font-size: 13px; font-weight: 700; margin-bottom: 10px; display: flex; justify-content: space-between; width: 100%; align-items: baseline; }
        .quick { display: inline-flex; gap: 10px; }
        .quick button {
            border: 0; background: none; padding: 0; cursor: pointer;
            font-size: 12px; font-weight: 600; color: var(--brand); text-decoration: underline; text-underline-offset: 3px;
        }
        .quick button:hover { color: var(--brand-deep); }
        .opt { display: flex; align-items: center; gap: 10px; padding: 5px 0; font-size: 13.5px; cursor: pointer; }
        .opt small { color: var(--ink-3); font-size: 12px; margin-left: auto; font-variant-numeric: tabular-nums; }
        .range { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 8px; margin: 6px 0 0 26px; }
        .range[data-off="true"] { opacity: .45; }
        .field label { display: block; font-size: 11.5px; font-weight: 600; color: var(--ink-2); margin-bottom: 3px; }
        .field input {
            width: 100%; padding: 7px 9px; border: 1px solid var(--rule); border-radius: 7px;
            background: #fff; font-size: 13px;
        }
        .field input:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 3px oklch(0.42 0.12 155 / 0.15); }
        .field input:disabled { background: var(--desk); cursor: not-allowed; }
        .stall-list { max-height: 208px; overflow-y: auto; margin: 0 -6px; padding: 0 6px; scrollbar-width: thin; scrollbar-color: var(--rule) transparent; }
                .errors { margin: 14px 0 0; padding: 10px 12px; border-radius: 8px; background: var(--low-soft); color: var(--low); font-size: 12.5px; font-weight: 600; }
        .errors ul { margin: 0; padding-left: 16px; }
        .actions {
            position: sticky; bottom: 0; margin: auto -22px 0; padding: 14px 22px 18px;
            background: var(--panel); border-top: 1px solid var(--rule);
            /* CHANGED: one full-width Print button, since Apply was removed. */
            display: grid; grid-template-columns: 1fr; gap: 8px;
        }
        /* ADDED: range hint shown while a custom range is incomplete or reversed. */
        .range-hint { grid-column: 1 / -1; margin: 2px 0 0; font-size: 12px; font-weight: 600; color: var(--low); }
        .range-hint[hidden] { display: none; }
        /* ADDED: the paper dims slightly while a new preview is loading. */
        .sheet { transition: opacity .12s ease-out; }
        .sheet[aria-busy="true"] { opacity: .55; }
        /* ADDED: visually hidden status text read out by screen readers when the preview updates. */
        .sr-status { position: absolute; width: 1px; height: 1px; overflow: hidden; clip-path: inset(50%); white-space: nowrap; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 14px; border-radius: 8px; font-size: 13.5px; font-weight: 700; cursor: pointer;
            border: 1px solid var(--rule); background: #fff; color: var(--ink);
            transition: background-color .15s ease-out, border-color .15s ease-out;
        }
        .btn:hover { border-color: var(--ink-3); }
        .btn svg { width: 16px; height: 16px; }
        .btn-primary { background: var(--brand); border-color: var(--brand); color: #fff; }
        .btn-primary:hover { background: var(--brand-deep); border-color: var(--brand-deep); }

        /* ── Paper ─────────────────────────────────────── */
        .sheet-wrap { min-width: 0; }
        .sheet {
            width: 210mm;
            max-width: 100%;
            min-height: 297mm;
            margin: 0 auto;
            padding: 16mm 15mm 14mm;
            background: var(--paper);
            box-shadow: 0 1px 2px oklch(0.2 0.03 155 / 0.08), 0 14px 36px -8px oklch(0.2 0.03 155 / 0.18);
            font-size: 10pt;
            line-height: 1.45;
            font-variant-numeric: tabular-nums;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .letterhead { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 14px; }
        .letterhead img { width: 60px; height: 60px; object-fit: contain; }
        .lh-small { margin: 0; font-size: 8.5pt; color: var(--ink-2); }
        .lh-name { margin: 1px 0 0; font-family: 'Sora', sans-serif; font-size: 14pt; font-weight: 700; letter-spacing: 0.01em; text-transform: uppercase; color: var(--brand-deep); line-height: 1.15; }
        .lh-campus { margin: 1px 0 0; font-size: 9.5pt; font-weight: 600; }
        .lh-right { text-align: right; font-size: 8.5pt; color: var(--ink-2); line-height: 1.35; }
        .double-rule { border: 0; border-top: 2.5px solid var(--rule-strong); margin: 12px 0 0; }
        .double-rule + .double-rule { border-top-width: 0.75px; margin-top: 2px; }

        .doc-title { font-family: 'Sora', sans-serif; font-size: 19pt; font-weight: 700; letter-spacing: -0.02em; margin: 20px 0 2px; line-height: 1.15; text-wrap: balance; }
        .doc-sub { margin: 0; color: var(--ink-2); font-size: 9.5pt; }
        .meta { display: grid; grid-template-columns: repeat(4, 1fr); margin: 14px 0 0; border-top: 1px solid var(--rule); border-bottom: 1px solid var(--rule); }
        .meta div { padding: 8px 10px 8px 0; }
        .meta div + div { padding-left: 10px; border-left: 1px solid var(--rule); }
        .meta dt { font-size: 7.5pt; font-weight: 700; color: var(--ink-3); text-transform: uppercase; letter-spacing: 0.06em; }
        .meta dd { margin: 2px 0 0; font-weight: 600; font-size: 9pt; }

        .doc-section { margin-top: 22px; }
        .doc-section h2 {
            font-family: 'Sora', sans-serif; font-size: 11.5pt; font-weight: 700; margin: 0 0 8px; letter-spacing: -0.01em;
            color: var(--brand-deep); padding-bottom: 5px; border-bottom: 1.5px solid var(--brand);
            break-after: avoid;
        }
        .doc-section p { margin: 0 0 8px; max-width: 72ch; }
        .muted { color: var(--ink-2); }
        .small { font-size: 8.5pt; }

        .tbl-scroll { overflow-x: auto; }
        table.doc { width: 100%; border-collapse: collapse; font-size: 9pt; }
        table.doc th {
            text-align: left; font-size: 7.5pt; font-weight: 700; color: var(--ink-2); text-transform: uppercase; letter-spacing: 0.05em;
            padding: 6px 6px; border-bottom: 1px solid var(--rule-strong); vertical-align: bottom;
        }
        table.doc td { padding: 5.5px 6px; border-bottom: 1px solid var(--rule); vertical-align: middle; }
        table.doc .num { text-align: right; white-space: nowrap; }
        table.doc .ctr { text-align: center; }
        table.doc tr.first td { background: var(--brand-soft); font-weight: 700; }
        table.doc td.low { color: var(--low); font-weight: 800; }
        table.doc .rank { font-weight: 800; width: 34px; }
        table.doc .stall { font-weight: 600; }
        table.doc tfoot td { border-bottom: 0; padding-top: 7px; font-size: 8pt; color: var(--ink-2); }



        .chart { display: grid; grid-template-columns: minmax(0, 11rem) 1fr 3.4rem; align-items: center; column-gap: 10px; row-gap: 5px; font-size: 9pt; }
        .chart .name { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; }
        .chart .bar { height: 12px; background: repeating-linear-gradient(90deg, transparent 0 calc(25% - 1px), var(--rule) calc(25% - 1px) 25%); position: relative; }
        .chart .bar > span { display: block; height: 100%; background: var(--brand); border-radius: 0 2px 2px 0; }
        .chart .val { text-align: right; font-weight: 700; }
        .chart .axis { grid-column: 2; display: flex; justify-content: space-between; font-size: 7.5pt; color: var(--ink-3); margin-top: 2px; }

        .attention-empty { padding: 10px 12px; background: var(--brand-soft); border-radius: 4px; font-weight: 600; }

        .prepared { margin-top: 34px; width: 16rem; max-width: 100%; break-inside: avoid; }
        .sig-role { font-size: 8.5pt; color: var(--ink-2); margin: 0 0 30px; }
        .sig-line { border-top: 1px solid var(--ink); padding-top: 4px; }
        .sig-name { font-weight: 800; text-transform: uppercase; font-size: 9.5pt; letter-spacing: 0.02em; margin: 0; }
        .sig-title { font-size: 8.5pt; color: var(--ink-2); margin: 0; }
        .sig-date { margin: 8px 0 0; font-size: 8.5pt; color: var(--ink-2); }
        .sig-date b { color: var(--ink); font-weight: 600; }

        .doc-end { margin-top: 30px; padding-top: 8px; border-top: 1px solid var(--rule); display: flex; justify-content: space-between; gap: 12px; font-size: 7.5pt; color: var(--ink-3); }
        .empty-doc { padding: 40px 0; text-align: center; color: var(--ink-2); }

        @media screen and (max-width: 1080px) {
            .desk { grid-template-columns: minmax(0, 1fr); padding: 0 0 48px; gap: 20px; }
            .panel { position: static; height: auto; margin: 0; border-right: 0; border-bottom: 1px solid var(--rule); min-width: 0; }
            .actions { position: sticky; bottom: 0; z-index: 2; }
            .sheet-wrap { padding: 0 16px; }
        }
        @media screen and (max-width: 760px) {
            .sheet { width: 100%; min-height: 0; padding: 22px 18px; }
            .meta { grid-template-columns: 1fr 1fr; }
            .meta div:nth-child(3) { border-left: 0; padding-left: 0; }
            .meta div:nth-child(n+3) { border-top: 1px solid var(--rule); }
            .letterhead { grid-template-columns: auto 1fr; }
            .lh-right { display: none; }
            .chart { grid-template-columns: minmax(0, 7rem) 1fr 3rem; }
        }

        @page {
            size: A4 portrait;
            margin: 14mm 14mm 16mm;
            @bottom-left { content: "ISU Cauayan · Canteen Stall Evaluation Report"; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 7.5pt; color: #6b7280; }
            @bottom-right { content: "Page " counter(page) " of " counter(pages); font-family: 'Plus Jakarta Sans', sans-serif; font-size: 7.5pt; color: #6b7280; }
        }
        @media print {
            body { background: #fff; }
            .panel { display: none !important; }
            .desk { display: block; padding: 0; }
            .sheet-wrap { padding: 0; }
            .sheet { width: auto; min-height: 0; padding: 0; margin: 0; box-shadow: none; }
            .tbl-scroll { overflow: visible; }
            thead { display: table-header-group; }
            tr, .chart > * { break-inside: avoid; }
            .doc-section h2 { break-after: avoid; }
        }
    </style>
</head>
<body>
<div class="desk">

    <aside class="panel" aria-label="Report options">
        <div class="panel-head">
            <a href="{{ route('admin.dashboard') }}" class="back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                Back to Overview
            </a>
            <h1 class="panel-title">Print report</h1>
            {{-- CHANGED: copy now says the preview updates on its own. --}}
            <p class="panel-sub">Choose what goes on paper. The preview updates as you change options.</p>
            @if ($errors->any())
                <div class="errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.report') }}" id="reportForm">
            <input type="hidden" name="stall_filter" value="1">
            <input type="hidden" name="section_filter" value="1">

            <fieldset>
                <legend>Period</legend>
                @foreach (['all' => 'All records', 'month' => 'This month', 'year' => 'This year', 'custom' => 'Custom range'] as $value => $label)
                    <label class="opt">
                        <input type="radio" name="period" value="{{ $value }}" @checked($period === $value)>
                        {{ $label }}
                    </label>
                @endforeach
                <div class="range" id="customRange" data-off="{{ $period === 'custom' ? 'false' : 'true' }}">
                    <div class="field">
                        <label for="from">From</label>
                        <input type="date" id="from" name="from" value="{{ old('from', $period === 'custom' ? $from?->format('Y-m-d') : '') }}" @disabled($period !== 'custom')>
                    </div>
                    <div class="field">
                        <label for="to">To</label>
                        <input type="date" id="to" name="to" value="{{ old('to', $period === 'custom' ? $to?->format('Y-m-d') : '') }}" @disabled($period !== 'custom')>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>
                    <span>Stalls <span class="muted" id="stallCount" style="font-weight:500">({{ count($selectedIds) }} of {{ $allStalls->count() }})</span></span>
                    <span class="quick">
                        <button type="button" data-check="stalls" data-state="1">All</button>
                        <button type="button" data-check="stalls" data-state="0">None</button>
                    </span>
                </legend>
                <div class="stall-list">
                    @forelse ($allStalls as $stall)
                        <label class="opt">
                            <input type="checkbox" name="stalls[]" value="{{ $stall->id }}" data-group="stalls" @checked(in_array($stall->id, $selectedIds))>
                            {{ $stall->name }}
                        </label>
                    @empty
                        <p class="muted small">No stalls yet.</p>
                    @endforelse
                </div>
            </fieldset>

            <fieldset>
                <legend>
                    <span>Sections</span>
                    <span class="quick">
                        <button type="button" data-check="sections" data-state="1">All</button>
                        <button type="button" data-check="sections" data-state="0">None</button>
                    </span>
                </legend>
                @foreach ($sectionOptions as $key => $label)
                    <label class="opt">
                        <input type="checkbox" name="sections[]" value="{{ $key }}" data-group="sections" @checked($has($key))>
                        {{ $label }}
                    </label>
                @endforeach
            </fieldset>


            {{-- CHANGED: removed the Apply button and the "out of date" note; the preview now refreshes on every change. --}}
            <div class="actions">
                {{-- CHANGED: dropped name="print" value="1"; nothing reads it now that auto-print on reload is gone. --}}
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6"/><rect x="6" y="14" width="12" height="7" rx="1"/><path d="M6 18H4a1 1 0 0 1-1-1v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a1 1 0 0 1-1 1h-2"/></svg>
                    Print report
                </button>
            </div>
        </form>
    </aside>

    <div class="sheet-wrap">
        <article class="sheet">
            <header class="letterhead">
                <img src="{{ asset('assets/images/isu_logo.png') }}" alt="Isabela State University seal">
                <div>
                    <p class="lh-small">Republic of the Philippines</p>
                    <p class="lh-name">Isabela State University</p>
                    <p class="lh-campus">Cauayan City Campus</p>
                </div>
                <div class="lh-right">Canteen Client Evaluation<br>Decision Support System</div>
            </header>
            <hr class="double-rule"><hr class="double-rule">

            <h2 class="doc-title">Canteen Stall Evaluation Report</h2>
            <p class="doc-sub">Stall ranking by Simple Additive Weighting (SAW), with criterion weights from the Analytic Hierarchy Process (AHP)</p>

            <dl class="meta">
                <div><dt>Period covered</dt><dd>{{ $periodLabel }}</dd></div>
                <div>
                    <dt>Evaluation dates</dt>
                    <dd>
                        @if ($firstAt)
                            {{ $firstAt->format('M j, Y') }}{{ $firstAt->isSameDay($lastAt) ? '' : ' – ' . $lastAt->format('M j, Y') }}
                        @else
                            None recorded
                        @endif
                    </dd>
                </div>
                <div><dt>Stalls included</dt><dd>{{ count($selectedIds) }} of {{ $allStalls->count() }}</dd></div>
                <div><dt>Generated</dt><dd>{{ $generatedAt->format('M j, Y · g:i A') }}</dd></div>
            </dl>

            @if ($sections === [])
                <p class="empty-doc">No sections selected. Tick at least one section in the options panel.</p>
            @elseif (count($selectedIds) === 0)
                <p class="empty-doc">No stalls selected. Tick at least one stall in the options panel.</p>
            @else

                @if ($has('ranking'))
                    <section class="doc-section">
                        <h2>Stall ranking</h2>
                        @if ($rated->isNotEmpty())
                            <div class="tbl-scroll">
                                <table class="doc">
                                    <thead>
                                        <tr>
                                            <th class="rank">Rank</th>
                                            <th>Stall</th>
                                            <th class="num">Evals</th>
                                            @foreach ($columns as $criterion => $column)
                                                <th class="num">{{ Str::before($criterionLabels[$criterion], ' ') }}</th>
                                            @endforeach
                                            <th class="num">Mean</th>
                                            <th class="num">SAW</th>
                                            <th class="num">AHP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rated as $i => $row)
                                            <tr class="{{ $i === 0 ? 'first' : '' }}">
                                                <td class="rank">{{ $i + 1 }}</td>
                                                <td class="stall">{{ $row->name }}</td>
                                                <td class="num">{{ $row->eval_count }}</td>
                                                @foreach ($columns as $criterion => $column)
                                                    <td class="num {{ (float) $row->$column < 3 ? 'low' : '' }}">{{ number_format($row->$column, 2) }}</td>
                                                @endforeach
                                                <td class="num {{ (float) $row->overall_score < 3 ? 'low' : '' }}">{{ number_format($row->overall_score, 2) }}</td>
                                                <td class="num" style="font-weight:700">{{ number_format($row->saw_score, 4) }}</td>
                                                <td class="num">{{ number_format($row->ahp_score, 4) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="{{ 6 + count($columns) }}">
                                                Ranked by SAW score. Means below 3.00 are in bold red.
                                                @if ($unrated->isNotEmpty())
                                                    Not ranked, no evaluations in this period: {{ $unrated->pluck('name')->join(', ', ' and ') }}.
                                                @endif
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="muted">No stall has evaluations in this period, so there is no ranking.</p>
                        @endif
                    </section>
                @endif

                @if ($has('attention') && $rated->isNotEmpty())
                    <section class="doc-section">
                        <h2>Stalls needing attention</h2>
                        @if ($attention->isNotEmpty())
                            <p class="muted small">A stall is listed when its overall mean or any criterion mean is below 3.00.</p>
                            <div class="tbl-scroll">
                                <table class="doc">
                                    <thead>
                                        <tr>
                                            <th>Stall</th>
                                            <th class="num">Mean</th>
                                            <th>Below 3.00</th>
                                            <th>Weakest criterion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attention as $row)
                                            <tr>
                                                <td class="stall">{{ $row->name }}</td>
                                                <td class="num {{ (float) $row->overall_score < 3 ? 'low' : '' }}">{{ number_format($row->overall_score, 2) }}</td>
                                                <td>{{ $row->below ? collect($row->below)->map(fn ($c) => $criterionLabels[$c])->join(', ') : 'None' }}</td>
                                                <td>{{ $criterionLabels[$row->weakest] }} ({{ number_format($row->{$columns[$row->weakest]}, 2) }})</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="attention-empty">No stall has an overall or criterion mean below 3.00.</p>
                        @endif
                    </section>
                @endif

                @if ($has('chart') && $rated->isNotEmpty())
                    <section class="doc-section">
                        <h2>SAW score by stall</h2>
                        <div class="chart" role="img" aria-label="Bar chart of SAW scores by stall, from 0 to 1">
                            @foreach ($rated as $row)
                                <span class="name" title="{{ $row->name }}">{{ $row->name }}</span>
                                <span class="bar"><span style="width: {{ max(0, min(1, $row->saw_score)) * 100 }}%"></span></span>
                                <span class="val">{{ number_format($row->saw_score, 4) }}</span>
                            @endforeach
                            <span class="axis"><span>0</span><span>0.25</span><span>0.50</span><span>0.75</span><span>1.00</span></span>
                        </div>
                    </section>
                @endif

            @endif

            @if ($has('prepared'))
                <div class="prepared">
                    <p class="sig-role">Prepared by:</p>
                    <div class="sig-line">
                        <p class="sig-name">{{ $preparedBy['name'] }}</p>
                        <p class="sig-title">{{ $preparedBy['title'] }}</p>
                    </div>
                    <p class="sig-date">Date: <b>{{ $generatedAt->format('F j, Y') }}</b></p>
                </div>
            @endif

            <footer class="doc-end">
                <span>Generated by the ISU Cauayan Canteen Client Evaluation DSS on {{ $generatedAt->format('F j, Y \a\t g:i A') }} (PHT).</span>
                <span>For internal use.</span>
            </footer>
        </article>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('reportForm');
    var range = document.getElementById('customRange');
    var dates = range.querySelectorAll('input');

    function syncRange() {
        var custom = form.querySelector('input[name="period"]:checked');
        var on = custom && custom.value === 'custom';
        range.dataset.off = on ? 'false' : 'true';
        dates.forEach(function (d) { d.disabled = !on; });
    }

    // ADDED: live preview. Each change fetches this same page with the new options and
    // swaps in only the paper, the stall count and the tab title. The address bar is
    // updated too, so a refresh or bookmark keeps the chosen options.
    var sheet = document.querySelector('.sheet');
    var stallCount = document.getElementById('stallCount');
    var hint = document.createElement('p');
    hint.className = 'range-hint';
    hint.hidden = true;
    hint.textContent = 'Pick a start and end date, with the end on or after the start.';
    range.appendChild(hint);

    // ADDED: polite screen reader announcement after each preview update.
    var status = document.createElement('p');
    status.className = 'sr-status';
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    document.querySelector('.panel').appendChild(status);

    var timer = null;
    var controller = null;
    var pending = Promise.resolve(true);

    function rangeReady() {
        var custom = form.querySelector('input[name="period"]:checked');
        if (!custom || custom.value !== 'custom') return true;
        return dates[0].value !== '' && dates[1].value !== '' && dates[0].value <= dates[1].value;
    }

    function refresh() {
        var ready = rangeReady();
        hint.hidden = ready;
        if (!ready) return pending;

        var url = new URL(form.action);
        url.search = new URLSearchParams(new FormData(form)).toString();

        if (controller) controller.abort();
        controller = new AbortController();
        sheet.setAttribute('aria-busy', 'true');

        pending = fetch(url, { signal: controller.signal, headers: { 'Accept': 'text/html' } })
            .then(function (res) {
                if (!res.ok || res.redirected) throw new Error('Preview request failed');
                return res.text();
            })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var nextSheet = doc.querySelector('.sheet');
                if (!nextSheet) throw new Error('Preview missing');
                sheet.innerHTML = nextSheet.innerHTML;
                var nextCount = doc.getElementById('stallCount');
                if (nextCount) stallCount.textContent = nextCount.textContent;
                document.title = doc.title;
                history.replaceState(null, '', url);
                sheet.removeAttribute('aria-busy');
                // ADDED: clears a validation error box left over from a full page load.
                var errors = document.querySelector('.errors');
                if (errors) errors.remove();
                status.textContent = 'Preview updated.';
                return true;
            })
            .catch(function (err) {
                // CHANGED: resolves false so a waiting Print skips printing. An aborted
                // request resolves true because a newer request has replaced it.
                if (err.name === 'AbortError') return true;
                sheet.removeAttribute('aria-busy');
                form.submit();
                return false;
            });
        return pending;
    }

    function schedule() {
        clearTimeout(timer);
        timer = setTimeout(function () {
            // CHANGED: timer is cleared once it fires, so Print does not re-fetch a current preview.
            timer = null;
            refresh();
        }, 200);
    }

    // ADDED: Enter in an option field refreshes the preview instead of submitting the form,
    // which would otherwise open the print dialog.
    form.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
            e.preventDefault();
            schedule();
        }
    });

    form.addEventListener('change', function (e) {
        if (e.target.name === 'period') syncRange();
        schedule();
    });

    document.querySelectorAll('[data-check]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var on = btn.dataset.state === '1';
            form.querySelectorAll('input[data-group="' + btn.dataset.check + '"]').forEach(function (box) { box.checked = on; });
            schedule();
        });
    });

    // ADDED: Print no longer reloads the page. It waits for any preview still loading,
    // then opens the print dialog. Without JavaScript the button still submits with print=1.
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (timer) {
            clearTimeout(timer);
            timer = null;
            refresh();
        }
        if (!rangeReady()) {
            hint.hidden = false;
            return;
        }
        // CHANGED: prints only when the latest preview loaded; a failed update reloads the page instead.
        pending.then(function (ok) { if (ok) window.print(); });
    });

    syncRange();

    {{-- CHANGED: removed the auto-print-on-load block; Print now prints the live preview directly. --}}
})();
</script>
</body>
</html>
