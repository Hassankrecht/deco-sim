@component('mail::message')
<div style="text-align:center; margin-bottom:20px;">
    <img src="{{ asset('assets/img/ChatGPT Image Nov 3, 2025, 08_00_27 AM.png') }}" alt="Deco Sim Logo" width="100">
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
- **Total:** **${{ number_format($order->total_price, 2) }}**
- **Status:** {{ ucfirst($order->status ?? 'Pending') }}

---

### 🛒 Items
@foreach ($order->items as $item)
- {{ $item->name }} (x{{ $item->quantity }}) — ${{ number_format($item->total_price, 2) }}
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
    123 Business Street, Prague, Czech Republic<br>
    📞 +420 777 555 333 | ✉️ support@alikrechtgroup.com<br>
    🌐 <a href="https://alikrechtgroup.com" style="color:#d4af37;">alikrechtgroup.com</a>
</div>
@endcomponent
