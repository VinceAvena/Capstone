@props(['status'])
{{-- A small coloured label for a complaint or stall status. --}}
@php
    $label = \App\Models\Complaint::STATUSES[$status] ?? (\App\Models\Stall::STATUSES[$status] ?? ucfirst(str_replace('_', ' ', $status)));
@endphp
<span class="badge s-{{ $status }}">{{ $label }}</span>
