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

            <h2 class="mb-2 font-bold">Desgloce de modulos</h2>

            @if ($products[0]['name'] === 'Periodo de suscripción: Mensual')
                @foreach ($modules as $module)
                <div class="flex justify-between">
                    <p>{{ $module['name'] }}</p>
                    <p>${{ number_format($module['cost'], 2) }}</p>
                </div>
                @endforeach
            @else
                @foreach ($modules as $module)
                <div class="flex justify-between">
                    <p>{{ $module['name'] }}</p>
                    <p>${{ number_format($module['cost'] * 10, 2) }}</p> <!-- Multiplicación se realiza aquí -->
                </div>
                @endforeach
            @endif


            @foreach ($products as $product)
            <div class="flex justify-between items-center border-t border-gray-200 py-4">
                <h2 class="text-xl font-semibold text-gray-700">{{ $product['name'] }}</h2>
                <h1 class="text-2xl font-bold text-gray-900">${{ number_format($product['price'], 2) }}</h1>
            </div>   
            @endforeach
        </div>

        <div class="text-center mt-10">
            <form id="checkout-form" action="{{ route('checkout') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="products" value="{{ json_encode($products) }}">
                {{-- <button type="button" onclick="location.reload()" class="bg-gray-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-gray-700">
                    Cancelar
                </button> --}}
                <button type="submit" onclick="location.reload()" class="bg-blue-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700">
                    Completar el pago
                </button>
            </form>
        </div>
    </div>
</body>
</html>
