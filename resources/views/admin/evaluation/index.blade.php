@extends('layouts.app')
@section('title', 'Evaluation Results')

@section('content')
<div class="page-head">
    <div><h1>ISO/IEC 25010 evaluation results</h1><p>Weighted means use the five-point scale of Table 4. The two groups are compared with Welch's t-test (α = 0.05).</p></div>
    <button type="button" class="btn primary no-print" data-print>🖨 Print</button>
</div>

<div class="grid cols-4 mb">
    <div class="card stat"><div class="num">{{ $respondents }}</div><div class="lbl">Respondents</div></div>
    @foreach ($byType as $type => $n)
        <div class="card stat"><div class="num">{{ $n }}</div><div class="lbl">{{ $types[$type] ?? $type }}</div></div>
    @endforeach
</div>

<div class="card flush mb">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Quality characteristic</th>
                    <th class="num">IT experts<br><span class="muted">mean (n)</span></th>
                    <th>Rating</th>
                    <th class="num">End users<br><span class="muted">mean (n)</span></th>
                    <th>Rating</th>
                    <th class="num">All<br><span class="muted">mean</span></th>
                    <th>Rating</th>
                    <th class="num">t</th>
                    <th class="num">p-value</th>
                    <th>Significant difference?</th>
                </tr>
            </thead>
            <tbody>
            @foreach (array_merge($rows, [$total]) as $r)
                <tr @if ($loop->last) style="background:#f5f1e6;font-weight:700" @endif>
                    <td>{{ $r['label'] }}</td>
                    <td class="num">{{ $r['expert'] !== null ? number_format($r['expert'], 2) : '-' }} ({{ $r['n_expert'] }})</td>
                    <td>{{ $r['expert_rating'] }}</td>
                    <td class="num">{{ $r['user'] !== null ? number_format($r['user'], 2) : '-' }} ({{ $r['n_user'] }})</td>
                    <td>{{ $r['user_rating'] }}</td>
                    <td class="num">{{ $r['all'] !== null ? number_format($r['all'], 2) : '-' }}</td>
                    <td>{{ $r['all_rating'] }}</td>
                    @if ($r['test'])
                        <td class="num">{{ number_format($r['test']['t'], 3) }}</td>
                        <td class="num">{{ number_format($r['test']['p'], 4) }}</td>
                        <td>@if ($r['significant'])<span class="badge s-confirmed">Yes</span>@else<span class="badge ok">No</span>@endif</td>
                    @else
                        <td class="num">-</td><td class="num">-</td><td class="muted small">Needs 2+ respondents per group</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card mb">
    <h3>Likert scale (Table 4)</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Weighted mean</th><th>Descriptive rating</th></tr></thead>
            <tbody>
                <tr><td>4.20 – 5.00</td><td>Excellent</td></tr>
                <tr><td>3.40 – 4.19</td><td>Very Good</td></tr>
                <tr><td>2.60 – 3.39</td><td>Good</td></tr>
                <tr><td>1.80 – 2.59</td><td>Fair</td></tr>
                <tr><td>1.00 – 1.79</td><td>Poor</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h2>Comments and suggestions</h2>
    @forelse ($comments as $e)
        <p><strong>{{ $types[$e->respondent_type] ?? $e->respondent_type }}:</strong> {{ $e->comments }}</p>
    @empty
        <p class="muted">No comments yet.</p>
    @endforelse
</div>
@endsection
