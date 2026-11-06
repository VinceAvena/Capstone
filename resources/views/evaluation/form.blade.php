@extends('layouts.app')
@section('title', 'System Evaluation')

@section('content')
<div class="page-head">
    <div><h1>System evaluation</h1><p>After trying TindaPresyo, please rate each statement from 1 (Poor) to 5 (Excellent). This follows the ISO/IEC 25010 software quality model.</p></div>
</div>

<form method="POST" action="{{ route('evaluation.store') }}">
    @csrf

    <div class="card mb form-card">
        <div class="field" style="margin:0">
            <label for="respondent_type">I am evaluating as</label>
            <select id="respondent_type" name="respondent_type" class="@error('respondent_type') is-invalid @enderror">
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(old('respondent_type', $existing->respondent_type ?? $defaultType) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="hint">IT experts and end users are analysed as two groups.</div>
        </div>
    </div>

    @php $scale = [1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent']; @endphp

    @foreach ($characteristics as $key => $group)
        <div class="card mb">
            <h2>{{ $loop->iteration }}. {{ $group['label'] }}</h2>
            @foreach ($group['items'] as $i => $statement)
                @php $current = old("answers.$key.$i", $existing->answers["$key.$i"] ?? null); @endphp
                <div class="statement">
                    <p style="margin-bottom:8px">{{ $statement }}</p>
                    <div class="rating" role="radiogroup" aria-label="{{ $statement }}">
                        @foreach ($scale as $value => $word)
                            <label>
                                <input type="radio" name="answers[{{ $key }}][{{ $i }}]" value="{{ $value }}" @checked((int) $current === $value) required>
                                <span>{{ $value }} · {{ $word }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="card mb">
        <div class="field" style="margin:0">
            <label for="comments">Comments and suggestions <span class="muted">(optional)</span></label>
            <textarea id="comments" name="comments" maxlength="2000">{{ old('comments', $existing->comments ?? '') }}</textarea>
        </div>
    </div>

    <button type="submit" class="btn primary">{{ $existing ? 'Update my evaluation' : 'Submit evaluation' }}</button>
</form>
@endsection
