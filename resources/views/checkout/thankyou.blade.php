@extends('layouts.app')

@section('title', __('messages.checkout.thankyou_title'))
@section('meta_description', __('messages.meta.checkout_thankyou_description'))

@section('content')
  
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 08_48_50 AM.png') }}" alt="{{ __('messages.checkout.thankyou_title') }}"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content" style="padding-top: 220px;">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.checkout.thankyou_title') }}</h1>
            <p class="text-light">{{ __('messages.checkout.thankyou_sub') }}</p>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard">
            <div class="akg-card p-4">
                <h5 class="akg-section-label">{{ __('messages.checkout.order_summary') }}</h5>
                <h2 class="akg-section-head mb-3">{{ __('messages.checkout.order_number', ['id' => $order->id]) }}</h2>
                <p><strong>{{ __('messages.checkout.name_label') }}:</strong> {{ $order->name }}</p>
                <p><strong>{{ __('messages.checkout.email_label') }}:</strong> {{ $order->email }}</p>
                <p><strong>{{ __('messages.checkout.total_label') }}:</strong> {{ \App\Support\Currency::format($order->total_price) }}</p>

                <table class="table table-dark table-striped mt-4">
                    <thead class="table-warning text-dark">
                        <tr>
                            <th>{{ __('messages.cart.table_product') }}</th>
                            <th>{{ __('messages.cart.table_qty') }}</th>
                            <th>{{ __('messages.cart.table_price') }}</th>
                            <th>{{ __('messages.cart.table_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            @php
                                $itemName = $item->name;
                                if (app()->getLocale() === 'ar' && $item->product_id) {
                                    $productModel = \App\Models\Product::find($item->product_id);
                                    if ($productModel && $productModel->title_localized) {
                                        $itemName = $productModel->title_localized;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $itemName }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ \App\Support\Currency::format($item->price) }}</td>
                                <td>{{ \App\Support\Currency::format($item->total_price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="text-center mt-5 d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('checkout.invoice', $order->id) }}"
                        class="btn btn-gold text-dark fw-semibold px-5 py-2">
                        🧾 {{ __('messages.checkout.download_invoice') }}
                    </a>

                    <a href="{{ route('home') }}" class="btn btn-outline-gold fw-semibold px-5 py-2">
                        {{ __('messages.checkout.continue_shopping') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
