@extends('layouts.app')

@section('title', 'Order Confirmation')
@section('meta _description', 'Confirm your order with Deco Sim. Review your luxury product details and complete your secure purchase.')

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_17_11 PM.png') }}" alt="Order confirmation"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.checkout.confirm_title') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-light active">Order Confirmation</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard">
            <h5 class="akg-section-label">{{ __('messages.checkout.section_label') }}</h5>
            <h2 class="akg-section-head mb-4">{{ __('messages.checkout.confirm_label') }}</h2>

            {{-- ✅ عرض معلومات العميل --}}
            <div class="mb-4">
                <h6 class="text-gold mb-3">{{ __('messages.checkout.customer_info') }}</h6>
                <p><strong>Name:</strong> {{ $data['name'] }}</p>
                <p><strong>Email:</strong> {{ $data['email'] }}</p>
                <p><strong>Phone:</strong> {{ $data['phone_number'] }}</p>
                <p><strong>Address:</strong> {{ $data['address'] }}, {{ $data['town'] }}, {{ $data['country'] }} -
                    {{ $data['zipcode'] }}</p>
            </div>

            {{-- ✅ عرض تفاصيل المنتجات --}}
            <div class="mb-4">
                <h6 class="text-gold mb-3">{{ __('messages.checkout.order_summary') }}</h6>
                <table class="table table-dark table-striped align-middle">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('messages.checkout.table_product') }}</th>
                            <th scope="col">{{ __('messages.checkout.table_qty') }}</th>
                            <th scope="col">{{ __('messages.checkout.table_price') }}</th>
                            <th scope="col">{{ __('messages.checkout.table_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $item)
                            <tr>
                                <td>{{ $item['title'] ?? $item['name'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ \App\Support\Currency::format($item['price']) }}</td>
                                <td>{{ \App\Support\Currency::format($item['price'] * $item['quantity']) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-warning text-dark fw-bold">
                            <td colspan="3" class="text-end">Subtotal:</td>
                            <td>{{ \App\Support\Currency::format($total) }}</td>
                        </tr>
                        @if(!empty($applied))
                            <tr class="table-warning text-dark fw-bold">
                                <td colspan="3" class="text-end">Discount ({{ $applied['code'] }}):</td>
                                <td>- {{ \App\Support\Currency::format($discount) }}</td>
                            </tr>
                        @endif
                        <tr class="table-warning text-dark fw-bold">
                            <td colspan="3" class="text-end">Total to pay:</td>
                            <td>{{ \App\Support\Currency::format($totalAfter ?? $total) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- ✅ نموذج التأكيد النهائي --}}
            <form method="POST" action="{{ route('checkout.process') }}" class="js-recaptcha">
                @csrf
                <input type="hidden" name="g-recaptcha-response">
                @foreach ($data as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <input type="hidden" name="total" value="{{ $totalAfter ?? $total }}">

                <div class="d-flex justify-content-between mt-4 flex-wrap gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-outline-gold py-2 px-4">← {{ __('messages.checkout.edit') }}</a>
                    <button type="submit"
                            class="btn btn-gold text-dark fw-semibold py-2 px-5"
                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                            data-size="invisible"
                            data-badge="bottomright">
                        {{ __('messages.checkout.place_order') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
