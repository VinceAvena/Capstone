@extends('layouts.app')
@section('title', 'Report Overpricing')

@section('content')
<div class="page-head"><div><h1>Report overpricing</h1><p>Tell us where you were charged more than the posted price. A photo of the receipt or price tag is required.</p></div></div>

<div class="card form-card">
    <form method="POST" action="{{ route('shopper.complaints.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="field">
            <label for="stall_id">1. Which stall?</label>
            <select id="stall_id" name="stall_id" required data-url="{{ route('shopper.stalls.prices', ['stall' => '__ID__']) }}" class="@error('stall_id') is-invalid @enderror">
                <option value="">Choose a stall</option>
                @foreach ($stalls as $s)
                    <option value="{{ $s->id }}" @selected(old('stall_id', $selectedStall) == $s->id)>{{ $s->stall_number }} · {{ $s->name }} ({{ $s->section }})</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="commodity_id">2. What did you buy?</label>
            <select id="commodity_id" name="commodity_id" required data-old="{{ old('commodity_id') }}" class="@error('commodity_id') is-invalid @enderror">
                <option value="">Choose a stall first</option>
            </select>
            <div class="hint">Only items with a posted price can be reported.</div>
        </div>

        <div class="field">
            <label for="reported_price">3. How much were you charged? (₱ per unit)</label>
            <input type="number" step="0.01" min="0.01" id="reported_price" name="reported_price" value="{{ old('reported_price') }}" required inputmode="decimal" class="@error('reported_price') is-invalid @enderror">
        </div>

        <div id="price-info"></div>

        <div class="field">
            <label for="photo">4. Photo evidence (receipt or price tag)</label>
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" capture="environment" required data-max-kb="{{ $maxKb }}" class="@error('photo') is-invalid @enderror">
            <div class="hint">JPG, PNG or WebP, up to {{ $maxKb / 1024 }} MB. On a phone you can take the photo directly.</div>
            <div id="photo-preview" class="mt"></div>
        </div>

        <div class="field">
            <label for="description">5. Anything else we should know? <span class="muted">(optional)</span></label>
            <textarea id="description" name="description" maxlength="1000">{{ old('description') }}</textarea>
        </div>

        <div class="actions">
            <button type="submit" class="btn primary">Submit complaint</button>
            <a class="btn ghost" href="{{ route('dashboard') }}">Cancel</a>
        </div>
    </form>
</div>
@endsection
