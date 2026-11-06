@props(['logs', 'admin' => false])
{{--
    The history of a complaint: status changes, messages, and system notices.
    Only the administrator sees real names; everyone else sees roles.
--}}
@php
    $who = function ($log) use ($admin) {
        if (! $log->user) {
            return 'TindaPresyo system';
        }
        $role = match (true) {
            $log->user->isAdmin() => 'Market administration',
            $log->user->isVendor() => 'Vendor',
            $log->user->isShopper() => 'Shopper',
            default => 'Market inspector',
        };

        return $admin ? $log->user->name . ' (' . $role . ')' : $role;
    };
@endphp
<ul class="timeline">
    @forelse ($logs as $log)
        <li class="{{ $log->type }}">
            <div>
                @if ($log->type === 'status')
                    <strong>
                        @if ($log->from_status)
                            {{ \App\Models\Complaint::STATUSES[$log->from_status] ?? $log->from_status }} →
                        @endif
                        {{ \App\Models\Complaint::STATUSES[$log->to_status] ?? $log->to_status }}
                    </strong>
                @elseif ($log->type === 'message')
                    <strong>Message</strong>
                @else
                    <strong>Notice</strong>
                @endif
                <span class="when">· {{ $who($log) }} · {{ $log->created_at->format('M j, Y g:i A') }}</span>
            </div>
            @if ($log->message)
                <div class="bubble">{{ $log->message }}</div>
            @endif
        </li>
    @empty
        <li><span class="muted">No activity yet.</span></li>
    @endforelse
</ul>
