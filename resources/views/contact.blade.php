@extends('layouts.app')

@section('title', 'Contact')
@section('meta_description', __('messages.meta.contact_description'))

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_15_05 PM.png') }}" alt="Contact Deco Sim"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.contact_page.hero_title') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ __('messages.nav.contact') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard">

            <div class="text-center mb-4">
                <h5 class="akg-section-label">{{ __('messages.contact_page.quick_contacts') }}</h5>
                <h2 class="akg-section-head">{{ __('messages.contact_page.hero_title') }}</h2>
                <p class="text-muted">{{ __('messages.contact_page.intro') }}</p>
            </div>

            <div class="row g-4 align-items-start">
                <div class="col-lg-5">
                    <div class="akg-card h-100">
                        <h5 class="text-gold mb-3">{{ __('messages.contact_page.quick_contacts') }}</h5>
                        <p class="mb-2"><i class="fa fa-phone me-2 text-gold"></i><a class="text-light"
                            href="tel:+96178768725">{{ __('messages.contact_page.phone_label') }}: +961 78768725</a></p>
                        <p class="mb-2"><i class="fab fa-whatsapp me-2 text-gold"></i><a class="text-light"
                            href="https://wa.me/96178768725" target="_blank" rel="noopener">{{ __('messages.contact_page.whatsapp_available') }}</a></p>
                        <p class="mb-2"><i class="fa fa-envelope-open me-2 text-gold"></i><a class="text-light"
                            href="mailto:alikrechtgroup@gmail.com">alikrechtgroup@gmail.com</a></p>
                        <p class="small text-muted mb-0">{{ __('messages.contact_page.privacy_note') }}</p>

                        <div class="mt-4">
                            <iframe class="rounded w-100" height="250"
                                src="https://www.google.com/maps?q=67QM+45X%20Ain%20Baal&output=embed"
                                style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="akg-card p-4">
                        <h5 class="text-gold mb-3">{{ __('messages.contact_page.send_message') }}</h5>

                        <form action="{{ route('contact.send') }}" method="POST" class="row g-3 js-recaptcha" id="contactForm">
                            @csrf
                            <input type="hidden" name="g-recaptcha-response">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control akg-input" placeholder="Your Name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control akg-input" placeholder="Your Email"
                                    value="{{ old('email') }}" required>
                            </div>
                            <div class="col-12">
                                <input type="text" name="subject" class="form-control akg-input" placeholder="Subject"
                                    value="{{ old('subject') }}" required>
                            </div>
                            <div class="col-12">
                                <textarea name="message" class="form-control akg-input" rows="5" placeholder="Your Message" required>{{ old('message') }}</textarea>
                            </div>

                            <div class="col-12 mt-3 d-flex flex-column gap-2">
                                <button type="submit" class="btn btn-gold px-5 py-3"
                                    data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                                    data-size="invisible"
                                    data-badge="bottomright"
                                    id="submitBtn">
                                    {{ __('messages.contact_page.send') }}
                                </button>
                                <span class="text-muted small">{{ __('messages.contact_page.intro') }}</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitBtn');
            let widgetId = null;

            const ensureWidget = () => {
                if (widgetId !== null) return widgetId;
                if (typeof grecaptcha === 'undefined') return null;
                widgetId = grecaptcha.render(submitBtn, {
                    sitekey: submitBtn.dataset.sitekey,
                    size: submitBtn.dataset.size || 'invisible',
                    badge: submitBtn.dataset.badge || 'bottomright',
                    callback: onSubmitRecaptcha,
                    'error-callback': onRecaptchaError,
                });
                return widgetId;
            };

            if (form && submitBtn) {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    const id = ensureWidget();
                    if (id === null) {
                        form.submit();
                        return;
                    }
                    window.__recaptchaActiveForm = form;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Sending...';
                    grecaptcha.execute(id);
                });
            }
        });
    </script>
@endsection
