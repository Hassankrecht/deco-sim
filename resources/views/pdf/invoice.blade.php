<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Deco Sim</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background-color: #0f0f0f;
            color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #1b1b1b;
            text-align: center;
            padding: 25px 0;
            border-bottom: 3px solid #d4af37;
        }
        .header img {
            width: 100px;
            display: block;
            margin: 0 auto 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            color: #d4af37;
            text-transform: uppercase;
        }
        .content {
            padding: 30px;
        }
        h2 {
            color: #d4af37;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #222;
            color: #f5f5f5;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #aaa;
            margin-top: 30px;
            border-top: 1px solid #444;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('assets/img/deco-sim-logo-pdf.jpg') }}" alt="Deco Sim logo">
        <h1>Deco Sim</h1>
        <p>High Quality Doors & Professional Solutions</p>
    </div>

    <div class="content">
        <h2>Invoice #{{ $order->id }}</h2>
        <p><strong>Customer:</strong> {{ $order->name }}</p>
        <p><strong>Email:</strong> {{ $order->email }}</p>
        <p><strong>Phone:</strong> {{ $order->phone_number }}</p>
        <p><strong>Address:</strong> {{ $order->address }}, {{ $order->town }}, {{ $order->country }}</p>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h3 style="text-align:right; margin-top:20px;">Total: ${{ number_format($order->total_price, 2) }}</h3>
    </div>

    <div class="footer">
        © {{ date('Y') }} Deco Sim — All Rights Reserved<br>
        📞 +244 972 100 585 • ✉️ Decosim2023@gmail.com
    </div>
</body>
</html>
