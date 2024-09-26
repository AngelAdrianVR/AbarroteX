<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StripeController extends Controller
{
    public function index(Request $request)
    {
        $products = [
            [
                'name' => 'Periodo de suscripción: ' . $request->suscription_period,
                'price' => $request->amount,
            ],
        ];

        $modules = $request->activeModules;

        return view('product.index', compact('products', 'modules'));
    }

    public function checkout(Request $request)
    {
        $products = json_decode($request->input('products'), true);

        \Stripe\Stripe::setApiKey(config(key:'stripe.sk'));

        $LineItems = [];

        foreach ($products as $product ) {
            $LineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product['name'],
                    ],
                    'unit_amount' => $product['price'] * 100,
                ],
                'quantity' => 1,
            ];
        }

        $session = \Stripe\Checkout\Session::create([
            'line_items' => $LineItems,
            'mode' => 'payment',
            // 'success_url' =>  route('stripe.success', [], true) . "?session_id={CHECKOUT_SESSION_ID}",
            'success_url' =>  route('stripe.success', [], true) . "?products=" . urlencode(json_encode($products)),
            'cancel_url' =>  route('stripe.cancel', [], true),
        ]);

        return redirect($session->url);
    }

    // public function checkout()
    // {
    //     $stripe = new \Stripe\StripeClient(config('stripe.sk'));

    //     try {
    //         $checkout_session = $stripe->checkout->sessions->create([
    //             'ui_mode' => 'embedded',
    //             'line_items' => [[
    //                 'price' => 'price_1Q321KKyxepsgPCZeXozTmbW', // Cambia esto por el ID de tu producto real
    //                 'quantity' => 1,
    //             ]],
    //             'mode' => 'payment',
    //         ]);

    //         return response()->json([
    //             'id' => $checkout_session->id,
    //         ]);
    //     } catch (\Exception $e) {
    //         // Maneja el error y devuelve una respuesta de error JSON
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function success(Request $request)
    {
        $products = json_decode($request->input('products'), true);

        $store = Store::find(auth()->user()->store_id);
        
        //si se paga mensualidad
        if ( $products[0]['name'] === "Periodo de suscripción: Mensual" ) {
            $store->suscription_period = 'Mensual';
            $proximoPago = now()->addDays(30);
            $store->next_payment = $proximoPago->toDateTimeString();
        } else {
            //si se paga mensualidad
            $store->suscription_period = 'Anual';
            $proximoPago = now()->addDays(365);
            $store->next_payment = $proximoPago->toDateTimeString();
        }

        $store->is_active = true;
        $store->status = 'Pagado';
        $store->save();

        return inertia('Stripe/Success');
    }

    // Success con session id ---------------------------------
    // public function success(Request $request)
    // {
    //     \Stripe\Stripe::setApiKey(config(key:'stripe.sk'));
    //     $sessionId = $request->get('session_id');

    //     $session = \Stripe\Checkout\Session::retrieve($sessionId);

    //     if( !$session ) {
    //         throw new NotFoundHttpException();
    //     }

    //     $customer = \Stripe\Customer::retrieve($session->customer);

    //     return inertia('Stripe/Success', compact('customer'));
    // }

    public function cancel()
    {
        return route('checkout.cancel');
    }

}
