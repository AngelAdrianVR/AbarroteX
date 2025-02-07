<?php

namespace App\Http\Controllers;

use App\Models\Store;
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

        return view('Stripe.upgradeSubscription', compact('products', 'modules_updated', 'activated_modules', 'remaining_plan_days', 'discount_ticket'));
    }

    //funcion de checkout para pagar plan expirado
    public function checkout(Request $request)
    {
        $products = json_decode($request->input('products'), true); //lo hago array pero solo tiene un objeto. Lo dejo asi par ano modificar el ejemplo en caso de muchos elementos
        $modules_updated = json_decode($request->input('modules_updated'), true); //modulos pagados para guardarlos en base de datos y actualizar en caso de agregar nuevos o quitar

        \Stripe\Stripe::setApiKey(config(key:'stripe.sk')); //el helper config toma las llaves desde el directorio config/stripe

        $LineItems = [];

        foreach ($products as $product ) {
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
        $activated_modules = json_decode($request->input('activated_modules'), true); //modulos pagados para guardarlos en base de datos y actualizar en caso de agregar nuevos o quitar

        \Stripe\Stripe::setApiKey(config(key:'stripe.sk')); //el helper config toma las llaves desde el directorio config/stripe

        
        $LineItems = [];
        
        foreach ($products as $product ) {
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

    //     \Stripe\Stripe::setApiKey(config('stripe.sk'));

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
            \Stripe\Stripe::setApiKey(config(key:'stripe.sk'));
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            // Comprobar que el pago se haya completado correctamente
            if ($session->payment_status !== 'paid') {
                return to_route('stripe.error');
            }

            // Procesar los datos de la solicitud
            $products = json_decode($request->input('products'), true);
            $modules_updated = json_decode($request->input('modules_updated'), true);

            // Recuperar tienda logueada
            $store = Store::find(auth()->user()->store_id);
            $daysToAdd = ($products[0]['name'] === "Periodo de suscripción: Mensual") ? 30 : 365;
            $now = now();
            $nextPayment = $store->next_payment ? Carbon::parse($store->next_payment) : $now;

            // Si el pago está vencido, partir de hoy, si no, sumar a la fecha actual de next_payment
            $store->next_payment = ($nextPayment->isPast() ? $now : $nextPayment)->addDays($daysToAdd)->toDateTimeString();

            if ($nextPayment->isPast()) {
                $store->suscription_period = $daysToAdd === 30 ? 'Mensual' : 'Anual';
            }
            $store->save();


            // // Encontrar la tienda del usuario autenticado
            // $store = Store::find(auth()->user()->store_id);
            
            // // Asignar el periodo de suscripción basado en el nombre del producto
            // if ($products[0]['name'] === "Periodo de suscripción: Mensual") {
            //     $store->suscription_period = 'Mensual';
            //     $proximoPago = now()->addDays(30);
            // } else {
            //     $store->suscription_period = 'Anual';
            //     $proximoPago = now()->addDays(365);
            // }
            
            // // Actualizar la fecha del próximo pago
            // $store->next_payment = $proximoPago->toDateTimeString();

            // Si se han actualizado los módulos, guardarlos en la base de datos
            if ($modules_updated) {
                $store->activated_modules = $modules_updated;
            }

            // Actualizar el estado de la tienda
            $store->is_active = true;
            $store->status = 'Pagado';
            $store->save();

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

        try {
            // Verificar el estado del pago a través de la API de Stripe usando el session_id
            \Stripe\Stripe::setApiKey(config(key:'stripe.sk'));
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            // Comprobar que el pago se haya completado correctamente
            if ($session->payment_status !== 'paid') {
                return to_route('stripe.error');
            }

            // Procesar los datos de la solicitud
            $modules_updated = json_decode($request->input('modules_updated'), true); //todos los modulos de la tienda actualizados (completos)

            // Encontrar la tienda del usuario autenticado
            $store = Store::find(auth()->user()->store_id);            

            // Actualizar los módulos, guardarlos en la base de datos
            $store->activated_modules = $modules_updated;
            $store->save();

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
