@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="page-head">
    <div><h1>Price monitoring and compliance reports</h1><p>Choose a period, then print this page or download each report as a spreadsheet (CSV).</p></div>
    <button type="button" class="btn primary no-print" data-print>🖨 Print report</button>
</div>

<form method="GET" action="{{ route('admin.reports.index') }}" class="filters">
    <div class="field"><label for="from">From</label><input type="date" id="from" name="from" value="{{ $from }}"></div>
    <div class="field"><label for="to">To</label><input type="date" id="to" name="to" value="{{ $to }}"></div>
    <div class="field">
        <label for="section">Section</label>
        <select id="section" name="section">
            <option value="">All sections</option>
            @foreach ($sections as $s)<option value="{{ $s }}" @selected($section === $s)>{{ $s }}</option>@endforeach
        </select>
    </div>
    <div class="actions"><button class="btn primary" type="submit">Generate</button></div>
</form>

<div class="print-only mb">
    <h1>{{ $marketName }}: Price Monitoring and Compliance Report</h1>
    <p>Period: {{ \Illuminate\Support\Carbon::parse($from)->format('M j, Y') }} to {{ \Illuminate\Support\Carbon::parse($to)->format('M j, Y') }}
       @if ($section) · Section: {{ $section }} @endif
       · Generated {{ now()->format('M j, Y g:i A') }}</p>
</div>

{{-- 1. Price monitoring --}}
<div class="card flush mb">
    <div class="actions" style="padding:16px 18px 0;justify-content:space-between">
        <h2>1. Price monitoring summary</h2>
        <a class="btn small ghost no-print" href="{{ route('admin.reports.export', array_merge(['type' => 'prices'], request()->only(['from', 'to', 'section']))) }}">Download CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Commodity</th><th>Category</th><th class="num">Stalls posting</th><th class="num">Entries</th><th class="num">Lowest</th><th class="num">Average</th><th class="num">Highest</th></tr></thead>
            <tbody>
            @forelse ($priceSummary as $r)
                <tr>
                    <td><strong>{{ $r->name }}</strong> <span class="muted small">/ {{ $r->unit }}</span></td>
                    <td>{{ $r->category }}</td>
                    <td class="num">{{ $r->stalls }}</td>
                    <td class="num">{{ $r->entries }}</td>
                    <td class="num">₱{{ number_format($r->min_price, 2) }}</td>
                    <td class="num">₱{{ number_format($r->avg_price, 2) }}</td>
                    <td class="num">₱{{ number_format($r->max_price, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">No prices were posted in this period.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 2. Posting compliance --}}
<div class="card flush mb">
    <div class="actions" style="padding:16px 18px 0;justify-content:space-between">
        <h2>2. Price posting compliance per stall</h2>
        <a class="btn small ghost no-print" href="{{ route('admin.reports.export', array_merge(['type' => 'compliance'], request()->only(['from', 'to', 'section']))) }}">Download CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Vendor</th><th>Section</th><th class="num">Days posted</th><th class="num">Compliance</th><th>Last posted</th></tr></thead>
            <tbody>
            @forelse ($compliance as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong> · {{ $s->name }}</td>
                    <td>{{ $s->vendor?->name }}</td>
                    <td>{{ $s->section }}</td>
                    <td class="num">{{ (int) $s->days_posted }} of {{ $s->days_total }}</td>
                    <td class="num"><span class="badge {{ $s->rate >= 80 ? 'ok' : 'stale' }}">{{ $s->rate }}%</span></td>
                    <td>{{ $s->last_posted ? \Illuminate\Support\Carbon::parse($s->last_posted)->format('M j, Y') : 'Never' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No approved stalls.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 3. Complaints --}}
<div class="card mb">
    <div class="actions" style="justify-content:space-between">
        <h2>3. Overpricing complaints in the period</h2>
        <a class="btn small ghost no-print" href="{{ route('admin.reports.export', array_merge(['type' => 'complaints'], request()->only(['from', 'to', 'section']))) }}">Download CSV</a>
    </div>
    <div class="grid cols-4 mb">
        <div class="card stat"><div class="num">{{ $complaintTotal }}</div><div class="lbl">Complaints filed</div></div>
        @foreach (['pending', 'under_review', 'confirmed', 'dismissed', 'resolved'] as $st)
            <div class="card stat"><div class="num">{{ $complaintsByStatus[$st] ?? 0 }}</div><div class="lbl">{{ \App\Models\Complaint::STATUSES[$st] }}</div></div>
        @endforeach
        <div class="card stat"><div class="num">{{ $avgOvercharge !== null ? $avgOvercharge . '%' : '-' }}</div><div class="lbl">Average overcharge (confirmed)</div></div>
    </div>

    <h3>Confirmed violations per stall</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th class="num">Confirmed complaints</th><th class="num">Average overcharge</th><th>Flag</th></tr></thead>
            <tbody>
            @forelse ($violations as $v)
                <tr>
                    <td><strong>{{ $v->stall->stall_number }}</strong> · {{ $v->stall->name }}</td>
                    <td class="num">{{ $v->count }}</td>
                    <td class="num">{{ $v->avg_percent }}%</td>
                    <td>@if ($v->stall->is_flagged)<span class="badge flag">Flagged</span>@else - @endif</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">No confirmed violations in this period.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 4. Flagged stalls --}}
<div class="card flush">
    <div class="actions" style="padding:16px 18px 0;justify-content:space-between">
        <h2>4. Flagged repeat violators (current)</h2>
        <a class="btn small ghost no-print" href="{{ route('admin.reports.export', array_merge(['type' => 'flagged'], request()->only(['from', 'to', 'section']))) }}">Download CSV</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Stall</th><th>Vendor</th><th>Section</th><th>Flagged on</th><th class="num">Confirmed</th></tr></thead>
            <tbody>
            @forelse ($flagged as $s)
                <tr>
                    <td><strong>{{ $s->stall_number }}</strong> · {{ $s->name }}</td>
                    <td>{{ $s->vendor?->name }}</td>
                    <td>{{ $s->section }}</td>
                    <td>{{ $s->flagged_at?->format('M j, Y') }}</td>
                    <td class="num">{{ $s->violationCount() }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No stalls are flagged.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
