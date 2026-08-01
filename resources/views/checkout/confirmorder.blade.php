@extends('layouts.app')

@section('title', __('messages.checkout.confirm_title'))
@section('meta_description', __('messages.meta.checkout_confirm_description'))

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_17_11 PM.png') }}" alt="{{ __('messages.checkout.confirm_title') }}"
             class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.checkout.confirm_title') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">{{ __('messages.nav.cart') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('checkout.index') }}">{{ __('messages.checkout.section_label') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ __('messages.checkout.confirm_title') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard">
            <h5 class="akg-section-label">{{ __('messages.checkout.confirm_label') }}</h5>
            <h2 class="akg-section-head mb-4">{{ __('messages.checkout.confirm_head') }}</h2>

            {{-- Customer info --}}
            <div class="mb-4">
                <h6 class="text-gold mb-3">{{ __('messages.checkout.customer_info') }}</h6>
                <p><strong>{{ __('messages.checkout.name_label') }}:</strong> {{ $data['name'] ?? '—' }}</p>
                <p><strong>{{ __('messages.checkout.email_label') }}:</strong> {{ $data['email'] ?? '—' }}</p>
                <p><strong>{{ __('messages.checkout.phone_label') }}:</strong> {{ $data['phone_number'] ?? '—' }}</p>
                <p><strong>{{ __('messages.checkout.address_label') }}:</strong> {{ $data['address'] ?? '—' }}, {{ $data['town'] ?? '' }}, {{ $data['country'] ?? '' }} - {{ $data['zipcode'] ?? '' }}</p>
            </div>

            {{-- Order items --}}
            <div class="mb-4">
                <h6 class="text-gold mb-3">{{ __('messages.checkout.order_summary') }}</h6>
                <table class="table table-dark table-striped align-middle">
                    <thead>
                    <tr>
                        <th scope="col">{{ __('messages.cart.table_product') }}</th>
                        <th scope="col">{{ __('messages.cart.table_qty') }}</th>
                        <th scope="col">{{ __('messages.cart.table_price') }}</th>
                        <th scope="col">{{ __('messages.cart.table_total') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($cartItems as $item)
                        @php
                            $itemTitle = $item['title_localized'] ?? $item['title'] ?? $item['name'] ?? '—';
                            if (app()->getLocale() === 'ar' && empty($item['title_localized'])) {
                                $productModel = \App\Models\Product::find($item['product_id'] ?? null);
                                if ($productModel && $productModel->title_localized) {
                                    $itemTitle = $productModel->title_localized;
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ $itemTitle }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ \App\Support\Currency::format($item['price']) }}</td>
                            <td>{{ \App\Support\Currency::format($item['price'] * $item['quantity']) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-warning text-dark fw-bold">
                        <td colspan="3" class="text-end">{{ __('messages.cart.subtotal') }}:</td>
                        <td>{{ \App\Support\Currency::format($total) }}</td>
                    </tr>
                    @if(!empty($applied))
                        <tr class="table-warning text-dark fw-bold">
                            <td colspan="3" class="text-end">{{ __('messages.cart.discount') }} ({{ $applied['code'] }}):</td>
                            <td>- {{ \App\Support\Currency::format($discount) }}</td>
                        </tr>
                    @endif
                    <tr class="table-warning text-dark fw-bold">
                        <td colspan="3" class="text-end">{{ __('messages.checkout.total_to_pay') }}:</td>
                        <td>{{ \App\Support\Currency::format($totalAfter ?? $total) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="mb-4">
                <h6 class="text-gold mb-2">{{ __('messages.checkout.payment') }}</h6>
                <p class="text-muted mb-1">{{ __('messages.checkout.payment_method') }}: {{ __('messages.checkout.payment_method_value') }}</p>
                <h6 class="text-gold mb-2">{{ __('messages.checkout.delivery') }}</h6>
                <p class="text-muted mb-0">{{ __('messages.checkout.delivery_note') }}</p>
            </div>

            {{-- Final confirm --}}
            <form method="POST" action="{{ route('checkout.process') }}" class="{{ Auth::check() ? '' : 'js-recaptcha' }}">
                @csrf
                @unless(Auth::check())
                    <input type="hidden" name="g-recaptcha-response">
                @endunless
                @foreach ($data as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <input type="hidden" name="total" value="{{ $totalAfter ?? $total }}">

                <div class="d-flex justify-content-between mt-4 flex-wrap gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-outline-gold py-2 px-4">{{ __('messages.checkout.edit_info') }}</a>
                    <button type="submit"
                            class="btn btn-gold text-dark fw-semibold py-2 px-5"
                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                            data-size="invisible"
                            data-badge="bottomright">
                        {{ __('messages.checkout.confirm_submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
