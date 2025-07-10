<?php

namespace App\Http\Controllers;

use App\Services\ProntipagosService;
use Illuminate\Http\Request;

class ProntipagosController extends Controller
{
    public function index() 
    {
        return inertia('Prontipagos/Index');
    }

    public function getToken(ProntipagosService $prontipagos)
    {
        try {
            $token = $prontipagos->getAccessToken();
            return response()->json(['access_token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCurrentBalance(ProntipagosService $prontipagos)
    {
        try {
            $balance = $prontipagos->getCurrentBalance();
            return response()->json($balance);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCatalogoProducts(ProntipagosService $prontipagos)
    {
        try {
            $productos = $prontipagos->getCatalogProducts();
            return response()->json($productos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function realizarVenta(Request $request, ProntipagosService $prontipagos)
    {
        $validated = $request->validate([
            'sku' => 'nullable|string',
            'amount' => 'required|numeric',
            'requiresRegionalization' => 'nullable|boolean',
            'reference' => 'required|string',
            'transacctionId' => 'required|numeric',
        ]);

        try {
            $venta = $prontipagos->realizarVenta($validated);
            return response()->json($venta);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSaleStatus($id, ProntipagosService $prontipagos)
    {
        try {
            // el metodo getSaleStatus se encuentra en el archivo de servicio
            $estado = $prontipagos->getSaleStatus($id);
            return response()->json($estado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



}
