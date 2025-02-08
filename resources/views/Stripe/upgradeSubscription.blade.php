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
            <h2 class="mb-2 font-bold">Tiempo restante para que expire tu plan</h2>
            <p>{{ $remaining_plan_days }} días</p>
        
            <h2 class="my-2 font-bold">Desglose de módulos adicionales</h2>
            <ul class="list-disc list-inside text-gray-600 mb-4">
                @foreach ($modules_updated as $module)
                    <li>{{ $module }}</li>
                @endforeach
            </ul>
            @php
                $total = collect($products)->sum('price');
                $subtotal = $discount_ticket ? $total + ($discount_ticket['is_percentage_discount'] 
                            ? ($total * $discount_ticket['discount_amount'] / 100) 
                            : $discount_ticket['discount_amount']) 
                            : $total;
                $discount = $discount_ticket 
                            ? ($discount_ticket['is_percentage_discount'] 
                                ? $discount_ticket['discount_amount'] . '%' 
                                : '$' . number_format($discount_ticket['discount_amount'], 2)) 
                            : '$0.00';
            @endphp
        
            <div class="border-t border-gray-200 mt-4 pt-4">
                <div class="flex justify-between text-lg font-semibold">
                    <span>Subtotal:</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg font-semibold text-red-600">
                    <span>Descuento:</span>
                    <span>{{ $discount }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-gray-900 border-t border-gray-300 mt-2 pt-2">
                    <span>Total a pagar:</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>
            <small>*El monto a pagar es proporcional a los días restantes de tu plan actual. En tu próximo pago el monto será completo.</small>
        </div>
        

        <!-- Botón de pago -->
        <div class="text-center mt-10">
            <form id="checkout-form" action="{{ route('update-plan-modules-checkout') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="products" value="{{ json_encode($products) }}">
                <input type="hidden" name="modules_updated" value="{{ json_encode($modules_updated) }}">
                <input type="hidden" name="activated_modules" value="{{ json_encode($activated_modules) }}">
                <button type="submit" onclick="location.reload()" class="bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700">
                    Completar el pago
                </button>
            </form>
        </div>
    </div>
</body>
</html>
