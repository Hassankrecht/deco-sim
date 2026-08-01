@extends('layouts.app')

@section('title', __('messages.dashboard.title'))
@section('meta_description', __('messages.dashboard.meta_description'))

@section('content')
    <div class="container py-5" style="margin-top: 82px">
        @push('head')
            <style>
                .user-card { background: #0f172a; border: 1px solid #1f2937; color: #f8fafc; }
                .user-card .text-muted { color: #e2e8f0 !important; }
                .table-dark th { color: #facc15 !important; }
                .table-dark td { color: #ffffff !important; background: #0b1220; }
                .table-dark td.fg-strong { color: #ffd166 !important; font-weight: 800; }
                .table-dark tr:nth-child(even) td { background: #0d1526; }
            </style>
        @endpush

        <div class="mb-3 d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-gold text-dark">{{ __('messages.dashboard.my_coupons') }}</a>
            <a href="{{ route('dashboard.orders') }}" class="btn btn-outline-gold">{{ __('messages.dashboard.my_orders') }}</a>
            <a href="{{ route('dashboard.profile') }}" class="btn btn-outline-gold">{{ __('messages.dashboard.profile') }}</a>
        </div>

        <div class="akg-card p-4 mb-4 user-card">
            <h4 class="text-gold mb-2">{{ __('messages.dashboard.welcome', ['name' => auth()->user()->name ?? __('messages.dashboard.user')]) }}</h4>
            <p class="text-muted mb-0">{{ __('messages.dashboard.coupons_desc') }}</p>
        </div>

        <div class="akg-card p-4 user-card">
            @if($coupons->isEmpty())
                <p class="text-muted mb-0">{{ __('messages.dashboard.no_coupons') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('messages.dashboard.code') }}</th>
                            <th>{{ __('messages.dashboard.type') }}</th>
                            <th>{{ __('messages.dashboard.value') }}</th>
                            <th>{{ __('messages.dashboard.usage') }}</th>
                            <th>{{ __('messages.dashboard.min_total') }}</th>
                            <th>{{ __('messages.dashboard.starts') }}</th>
                            <th>{{ __('messages.dashboard.expires') }}</th>
                            <th>{{ __('messages.dashboard.status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($coupons as $c)
                            @php
                                $now = \Carbon\Carbon::now();
                                $isExpired = $c->expiration_date && \Carbon\Carbon::parse($c->expiration_date)->lt($now);
                                $isUsedOut = ($c->usage_limit > 0) && ($c->used_count >= $c->usage_limit);
                                $active = $c->status && !$isExpired && !$isUsedOut;
                            @endphp
                            <tr>
                                <td class="fw-bold text-gold fg-strong">{{ $c->code }}</td>
                                <td>{{ $c->type }}</td>
                                <td class="fg-strong">{{ $c->value }}</td>
                                <td class="fg-strong">{{ $c->used_count }} / {{ $c->usage_limit > 0 ? $c->usage_limit : '∞' }}</td>
                                <td>{{ $c->min_total ? \App\Support\Currency::format($c->min_total) : '—' }}</td>
                                <td>{{ $c->starts_at ? \Carbon\Carbon::parse($c->starts_at)->format('Y-m-d') : '—' }}</td>
                                <td>{{ $c->expiration_date ? \Carbon\Carbon::parse($c->expiration_date)->format('Y-m-d') : '—' }}</td>
                                <td>
                                    @if($active)
                                        <span class="badge bg-success">{{ __('messages.dashboard.active') }}</span>
                                    @elseif($isExpired)
                                        <span class="badge bg-secondary">{{ __('messages.dashboard.expired') }}</span>
                                    @elseif($isUsedOut)
                                        <span class="badge bg-secondary">{{ __('messages.dashboard.used') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('messages.dashboard.inactive') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
