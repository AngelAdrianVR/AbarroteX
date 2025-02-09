<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pago con tarjeta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-gray-100">
    <div class="max-w-3xl mx-auto py-10">
        <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Resumen de Pago</h1>
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="mb-2 font-bold">Desglose de Módulos</h2>

            @php
                $subtotal = 0;
            @endphp

            @if ($products[0]['name'] === 'Periodo de suscripción: Mensual')
                @foreach ($modules as $module)
                @php $subtotal += $module['cost']; @endphp
                <div class="flex justify-between">
                    <p>{{ $module['name'] }}</p>
                    <p>${{ number_format($module['cost'], 2) }}</p>
                </div>
                @endforeach
            @else
                @foreach ($modules as $module)
                @php $subtotal += $module['cost'] * 10; @endphp
                <div class="flex justify-between">
                    <p>{{ $module['name'] }}</p>
                    <p>${{ number_format($module['cost'] * 10, 2) }}</p>
                </div>
                @endforeach
            @endif

            @foreach ($products as $product)
            {{-- @php $subtotal += $product['price']; @endphp --}}
            <div class="flex justify-between items-center border-t border-gray-200 py-4">
                <h2 class="text-xl font-semibold text-gray-700">{{ $product['name'] }}</h2>
                <h1 class="text-2xl font-bold text-gray-900">${{ number_format($product['price'], 2) }}</h1>
            </div>
            @endforeach

            @php
                $discount = 0;
                if (!empty($discount_ticket)) {
                    if ($discount_ticket['is_percentage_discount']) {
                        $discount = $subtotal * ($discount_ticket['discount_amount'] / 100);
                    } else {
                        $discount = $discount_ticket['discount_amount'];
                    }
                }
                $total = max($subtotal - $discount, 0);
            @endphp

            <div class="border-t border-gray-300 mt-4 pt-4">
                <div class="flex justify-between text-lg font-semibold">
                    <p>Subtotal:</p>
                    <p>${{ number_format($subtotal, 2) }}</p>
                </div>
                <div class="flex justify-between text-lg font-semibold text-red-500">
                    <p>Descuento:</p>
                    <p>- ${{ number_format($discount, 2) }}</p>
                </div>
                <div class="flex justify-between text-xl font-bold mt-2">
                    <p>Total:</p>
                    <p>${{ number_format($total, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-10">
            <form id="checkout-form" action="{{ route('checkout') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="products" value="{{ json_encode($products) }}">
                <input type="hidden" name="modules_updated" value="{{ json_encode($modules_updated) }}">
                <button type="submit" onclick="location.reload()" class="bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700">
                    Completar el pago
                </button>
            </form>
        </div>
    </div>
</body>
</html>
