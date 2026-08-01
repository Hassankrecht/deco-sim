@component('mail::message')
<div style="text-align:center; margin-bottom:20px;">
    <img src="{{ asset('assets/img/deco-sim-logo-email.jpg') }}" alt="Deco Sim logo" width="100">
    <h2 style="color:#d4af37; margin-top:10px;">Deco Sim</h2>
</div>

@if ($isAdmin)
# 🟡 New Order Received

Hello Admin,<br>
A new order has been placed through your **Deco Sim** website.

@else
# ✅ Thank You for Your Order!

Hello **{{ $order->name }}**,  
Your order with **Deco Sim** has been received successfully!
Below are your details:
@endif

---

### 🧾 Order Details
- **Order ID:** #{{ $order->id }}
- **Name:** {{ $order->name }}
- **Email:** {{ $order->email }}
- **Phone:** {{ $order->phone_number }}
- **Address:** {{ $order->address }}, {{ $order->town }}, {{ $order->country }}
- **Zip Code:** {{ $order->zipcode }}
- **Total:** **{{ \App\Support\Currency::format($order->total_price) }}**
- **Status:** {{ ucfirst($order->status ?? 'Pending') }}

---

### 🛒 Items
@foreach ($order->items as $item)
- {{ $item->name }} (x{{ $item->quantity }}) — {{ \App\Support\Currency::format($item->total_price) }}
@endforeach

---

@component('mail::panel')
📎 A PDF invoice with your company logo is attached below.  
Please keep it for your records.
@endcomponent

@if ($isAdmin)
@component('mail::button', ['url' => url('/admin/orders/'.$order->id)])
📦 View in Dashboard
@endcomponent
@else
@component('mail::button', ['url' => route('checkout.thankyou', $order->id)])
🔍 View Order Summary
@endcomponent
@endif

---

<div style="background-color:#111; color:#d4af37; padding:15px; border-radius:8px; text-align:center; margin-top:30px;">
    <strong>Deco Sim</strong><br>
    Benfica, Rua Dona Xepa, Angola<br>
    📞 +244 972 100 585 | ✉️ Decosim2023@gmail.com
</div>
@endcomponent
