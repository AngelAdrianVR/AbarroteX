<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\DiscountTicket;
use App\Models\Payment;
use App\Models\Store;
use App\Notifications\AdminBasicNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StripeController extends Controller
{
    public function index(Request $request)
    {
        //en este caso solo es un elemento pero lo dejé asi para aplicarlo en futuros proyectos que tenga mas elementos
        $products = [
            [
                'name' => 'Periodo de suscripción: ' . $request->suscription_period,
                'price' => $request->amount,
            ],
        ];

        $modules = $request->activeModules; //modulos pagados para mostrarlos en el desgloce del pago
        $modules_updated = $request->modulesUpdated; //modulos pagados para guardarlos en base de datos y actualizar en caso de agregar nuevos o quitar
        $discount_ticket = $request->discountTicketUsed; //cupón de descuento utiizado

        return view('Stripe.index', compact('products', 'modules', 'modules_updated', 'discount_ticket'));
    }

    /*
     ! pago para cuando se modifican los módulos adicionales y no ha expirado el plan pagado. para pagar plan expirado es el método index de arriba
    */
    public function upgradeSubscription(Request $request)
    {
        //en este caso solo es un elemento pero lo dejé asi para aplicarlo en futuros proyectos que tenga mas elementos
        $products = [
            [
                'name' => 'Ajuste de módulos en periodo ' . $request->suscription_period,
                'price' => $request->amount,
            ],
        ];
        $modules_updated = $request->modulesUpdated; //modulos pagados para guardarlos en base de datos y actualizar en caso de agregar nuevos o quitar
        $activated_modules = $request->activeModules; //modulos pagados para agregarlos en base de datos en caso de agregados o quitados
        $remaining_plan_days = $request->remainingPlanDays; //días que faltan para que expire el plan pagado
        $discount_ticket = $request->discountTicketUsed; //cupón de descuento utiizado

        // return $request;
        return view('Stripe.upgradeSubscription', compact('products', 'modules_updated', 'activated_modules', 'remaining_plan_days', 'discount_ticket'));
    }

    //funcion de checkout para pagar plan expirado
    public function checkout(Request $request)
    {
        $products = json_decode($request->input('products'), true); //lo hago array pero solo tiene un objeto. Lo dejo asi par ano modificar el ejemplo en caso de muchos elementos
        $discount_ticket = json_decode($request->input('discount_ticket'), true); // cupon de descuento utilizado
        $modules_updated = json_decode($request->input('modules_updated'), true); //modulos pagados para guardarlos en base de datos y actualizar en caso de agregar nuevos o quitar

        \Stripe\Stripe::setApiKey(config(key: 'stripe.sk_test')); //el helper config toma las llaves desde el directorio config/stripe

        $LineItems = [];

        foreach ($products as $product) {
            $LineItems[] = [
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => $product['name'],
                    ],
                    'unit_amount' => $product['price'] * 100,
                ],
                'quantity' => 1,
            ];
        }

        try {
            $session = \Stripe\Checkout\Session::create([
                'line_items' => $LineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}"
                    . "&products=" . urlencode(json_encode($products))
                    . "&discount_ticket=" . urlencode(json_encode($discount_ticket))
                    . "&modules_updated=" . urlencode(json_encode($modules_updated)),
                'cancel_url' => route('stripe.cancel', [], true),
                'locale' => 'es',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return to_route('stripe.error');
        }

        return redirect($session->url);
    }

    //funcion de checkout para pagar Modificaciones de modulos en plan no expirado
    public function updatePlanModulesCheckout(Request $request)
    {
        $products = json_decode($request->input('products'), true); //lo hago array pero solo tiene un objeto. Lo dejo asi par ano modificar el ejemplo en caso de muchos elementos
        // $modules_updated = json_decode($request->input('modules_updated'), true); //modulo adicionales agregados al plan actual
        $discount_ticket = json_decode($request->input('discount_ticket'), true); // cupon de descuento utilizado
        $activated_modules = json_decode($request->input('activated_modules'), true); //modulos pagados para guardarlos en base de datos y actualizar en caso de agregar nuevos o quitar

        \Stripe\Stripe::setApiKey(config(key: 'stripe.sk_test')); //el helper config toma las llaves desde el directorio config/stripe


        $LineItems = [];

        foreach ($products as $product) {
            $LineItems[] = [
                'price_data' => [
                    'currency' => 'mxn',
                    'product_data' => [
                        'name' => $product['name'],
                    ],
                    'unit_amount' => $product['price'] * 100,
                ],
                'quantity' => 1,
            ];
        }
        // return $LineItems;

        try {
            $session = \Stripe\Checkout\Session::create([
                'line_items' => $LineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.update-plan-modules-success', [], true) . "?session_id={CHECKOUT_SESSION_ID}"
                    . "&products=" . urlencode(json_encode($products))
                    . "&discount_ticket=" . urlencode(json_encode($discount_ticket))
                    . "&modules_updated=" . urlencode(json_encode($activated_modules)),
                'cancel_url' => route('stripe.cancel', [], true),
                'locale' => 'es',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return to_route('stripe.error');
        }

        return redirect($session->url);
    }

    /* Metodo checkout para membresias ---------------------------------------------------------------
    --------------------------------------------------------------------------------------------------
    !Pasos:
    ? Crear un producto en Stripe como suscripción.
    ? Utilizar price_data con un precio de tipo recurring.
    ? Configurar el subscription en lugar de un checkout.session de pago único.
    ? Gestionar el éxito del pago y actualizaciones automáticas de la membresía. 

    !Consideraciones adicionales:
    * Planes y precios en Stripe:
    ? Si ya tienes creados los planes de suscripción en el dashboard de Stripe, puedes utilizar el ID del precio directamente en lugar de crear el price_data manualmente.
    ? Esto te facilita la gestión de productos recurrentes desde el panel de Stripe.

    *$LineItems[] = [
    *'price' => 'price_xxxxxxx', // ID del precio desde el dashboard
    *'quantity' => 1,]; 
    */
    // public function checkout(Request $request)
    // {
    //     $products = json_decode($request->input('products'), true);
    //     $modules_updated = json_decode($request->input('modules_updated'), true);

    //     \Stripe\Stripe::setApiKey(config('stripe.sk_test'));

    //     $LineItems = [];

    //     foreach ($products as $product ) {
    //         $LineItems[] = [
    //             'price_data' => [
    //                 'currency' => 'mxn',
    //                 'product_data' => [
    //                     'name' => $product['name'],
    //                 ],
    //                 'unit_amount' => $product['price'] * 100,
    //                 'recurring' => [
    //                     'interval' => 'month', // Elige la periodicidad (month, week, year, etc.)
    //                 ],
    //             ],
    //             'quantity' => 1,
    //         ];
    //     }

    // try {
    //     $session = \Stripe\Checkout\Session::create([
    //         'line_items' => $LineItems,
    //         'mode' => 'payment',
    //         'success_url' => route('stripe.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}"
    //                                                             . "&products=" . urlencode(json_encode($products)) 
    //                                                             . "&modules_updated=" . urlencode(json_encode($modules_updated)),
    //         'cancel_url' => route('stripe.cancel', [], true),
    //         'locale' => 'es',  
    //     ]);
    // } catch (\Stripe\Exception\ApiErrorException $e) {
    //     return to_route('stripe.error');
    // }

    //     return redirect($session->url);
    // }

    //pagina de pago realizado para plan expirado (la lógica es diferente que la de modificacion de modulos)
    public function success(Request $request)
    {
        $session_id = $request->input('session_id');

        try {
            // Verificar el estado del pago a través de la API de Stripe usando el session_id
            \Stripe\Stripe::setApiKey(config(key: 'stripe.sk_test'));
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            // Comprobar que el pago se haya completado correctamente
            if ($session->payment_status !== 'paid') {
                return to_route('stripe.error');
            }

            // Procesar los datos de la solicitud
            $products = json_decode($request->input('products'), true);
            $modules_updated = json_decode($request->input('modules_updated'), true);
            $discount_ticket = json_decode($request->input('discount_ticket'), true);

            // Recuperar tienda logueada
            $store = Store::find(auth()->user()->store_id);
            $daysToAdd = ($products[0]['name'] === "Periodo de suscripción: Mensual") ? 30 : 365;
            $now = now();
            $nextPayment = $store->next_payment ? Carbon::parse($store->next_payment) : $now;

            // Si el pago está vencido, partir de hoy, si no, sumar a la fecha actual de next_payment
            $store->next_payment = ($nextPayment->isPast() ? $now : $nextPayment)->addDays($daysToAdd)->toDateTimeString();

            if ($nextPayment->isPast() || $store->suscription_period === 'Periodo de prueba') {
                $store->suscription_period = $daysToAdd === 30 ? 'Mensual' : 'Anual';
            }
            $store->save();

            // Si se han actualizado los módulos, guardarlos en la base de datos
            if ($modules_updated) {
                $store->activated_modules = $modules_updated;
            }

            // Actualizar el estado de la tienda
            $store->is_active = true;
            $store->status = 'Pagado';
            $store->save();

            //registrar el pago a la tabla payments de la base de datos para reflejarlo en el dashboard de administrador
            Payment::create([
                'payment_method' => 'Tarjeta de crédito o débito',
                'suscription_period' => $daysToAdd === 30 ? 'Mensual' : 'Anual',
                'status' => 'Aprobado',
                'amount' => $products[0]['price'],
                'store_id' => $store->id,
                'validated_by_id' => 1,
                'created_at' => now(),
                'validated_at' => now(),
            ]);

            //lógica de cupon de descuento / referido
            if ($discount_ticket) {
                // Verificar si el cupón existe y está activo
                $ticket = DiscountTicket::find($discount_ticket['id']);

                if ($ticket && $ticket->is_active && (!$ticket->expired_date || $ticket->expired_date->isFuture())) {
                    // Incrementar el contador de veces usado del cupon
                    $ticket->increment('times_used');

                    // Verificar si el cupon está relacionado con un partner (referido)
                    if ($ticket->partner) {
                        $partner = $ticket->partner;

                        // Sumar el 50% del pago a las ganancias del partner
                        $earning = $products[0]['price'] * 0.50; // 50% del precio
                        $partner->earnings += $earning;
                        $partner->increment('referrals');
                        $partner->save();
                    }

                    // Asociar el cupon con la tienda nueva (campo 'partner_cupon')
                    $store->partner_cupon = $ticket->code;
                    $store->save();
                    
                    // Notificar a dirección
                    $admins = Admin::where('employee_properties->department', 'Dirección')->get();
                    $title = "Cupón de descuento utilizado";
                    $description = "La tienda '$store->name' ha utilizado el cupón de descuento de '{$partner->name}' con una ganancia de $ {$earning}.";
                    if (app()->environment() === 'production') {
                        $url = 'https://admin.ezyventas.com/payments';
                    } else {
                        $url = 'http://localhost:8000/payments';
                    }
                    $admins->each(fn($admin) => $admin->notify(new AdminBasicNotification($title, $description, $url)));
                }
            }

            // Redirigir a la página de éxito
            return inertia('Stripe/Success');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Log del error y redirección a una página de error
            // \Log::error('Error al crear la sesión de Stripe: ' . $e->getMessage());
            return to_route('stripe.error');
            // return inertia('Stripe/Success');
        }
    }

    //pagina de pago realizado para modificacion de modulos en plan no expirado (la lógica es diferente que la de plan expirado)
    public function updatePlanModulesSuccess(Request $request)
    {
        $session_id = $request->input('session_id');

        //procesar informacion recibida en parametros url
        $products = json_decode($request->input('products'), true);

        try {
            // Verificar el estado del pago a través de la API de Stripe usando el session_id
            \Stripe\Stripe::setApiKey(config(key: 'stripe.sk_test'));
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            // Comprobar que el pago se haya completado correctamente
            if ($session->payment_status !== 'paid') {
                return to_route('stripe.error');
            }

            // Procesar los datos de la solicitud
            $modules_updated = json_decode($request->input('modules_updated'), true); //todos los modulos de la tienda actualizados (completos)
            $discount_ticket = json_decode($request->input('discount_ticket'), true);

            // Encontrar la tienda del usuario autenticado
            $store = Store::find(auth()->user()->store_id);

            // Actualizar los módulos, guardarlos en la base de datos
            $store->activated_modules = $modules_updated;
            $store->save();

            //registrar el pago a la tabla payments de la base de datos para reflejarlo en el dashboard de administrador
            Payment::create([
                'payment_method' => 'Tarjeta de crédito o débito',
                'suscription_period' => 'Mejora de módulos',
                'status' => 'Aprobado',
                'amount' => $products[0]['price'],
                'store_id' => $store->id,
                'validated_by_id' => 1,
                'created_at' => now(),
                'validated_at' => now(),
            ]);

            //lógica de cupon de descuento / referido
            if ($discount_ticket) {
                // Verificar si el cupón existe y está activo
                $ticket = DiscountTicket::find($discount_ticket['id']);

                if ($ticket && $ticket->is_active && (!$ticket->expired_date || $ticket->expired_date->isFuture())) {
                    // Incrementar el contador de veces usado del cupon
                    $ticket->increment('times_used');

                    // Verificar si el cupon está relacionado con un partner (referido)
                    if ($ticket->partner) {
                        $partner = $ticket->partner;

                        // Sumar el 50% del pago a las ganancias del partner
                        $earning = $products[0]['price'] * 0.50; // 50% del precio
                        $partner->earnings += $earning;
                        $partner->increment('referrals');
                        $partner->save();
                    }

                    // Asociar el cupon con la tienda nueva (campo 'partner_cupon')
                    $store->partner_cupon = $ticket->code;
                    $store->save();
                    
                    // Notificar a dirección
                    $admins = Admin::where('employee_properties->department', 'Dirección')->get();
                    $title = "Cupón de descuento utilizado";
                    $description = "La tienda '$store->name' ha utilizado el cupón de descuento de '{$partner->name}' con una ganancia de $ {$earning}.";
                    if (app()->environment() === 'production') {
                        $url = 'https://admin.ezyventas.com/payments';
                    } else {
                        $url = 'http://localhost:8000/payments';
                    }
                    $admins->each(fn($admin) => $admin->notify(new AdminBasicNotification($title, $description, $url)));
                }

            }

            // Redirigir a la página de éxito
            return inertia('Stripe/Success');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Log del error y redirección a una página de error
            // \Log::error('Error al crear la sesión de Stripe: ' . $e->getMessage());
            return to_route('stripe.error');
            // return inertia('Stripe/Success');
        }
    }

    public function cancel()
    {
        return inertia('Stripe/Cancel');
    }

    public function error()
    {
        return inertia('Stripe/Error');
    }
}
