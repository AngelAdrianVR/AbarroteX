<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ProntipagosService
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = config('prontipagos.base_url');
        $this->username = config('prontipagos.username');
        $this->password = config('prontipagos.password');
    }

    public function getAccessToken()
    {
        // Intenta recuperar el token desde caché
        $cachedToken = Cache::get('prontipagos_access_token');

        if ($cachedToken) {
            return $cachedToken;
        }

        // Si no hay token en caché, lo solicita a la API
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/prontipagos-external-api-ws/ws/v1/auth/login", [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if ($response->successful() && $response->json('code') === 0) {
            $token = $response->json('payload.accessToken');
            $expiresIn = $response->json('payload.expiresIn', 300); // segundos por defecto 5 minutos

            // Guarda el token en caché
            Cache::put('prontipagos_access_token', $token, $expiresIn);

            return $token;
        }

        throw new \Exception('Error al obtener access token: ' . $response->body());
    }

    public function getCurrentBalance()
    {
        $token = $this->getAccessToken();

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get("{$this->baseUrl}/prontipagos-external-api-ws/ws/protected/v1/balance/current");

        if ($response->successful() && $response->json('code') === 0) {
            return $response->json('payload')[0]; // contiene accountId y balance
        }

        throw new \Exception('Error al obtener balance: ' . $response->body());
    }

    // Recupera todos los productos del catalogo de prontipagos
    public function getCatalogProducts($page = 0, $pageSize = 100)
    {
        $token = $this->getAccessToken();
        $todosLosProductos = [];
        $page = 0;
        $pageSize = 100;

        do {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->get("{$this->baseUrl}/prontipagos-external-api-ws/ws/protected/v1/product/list", [
                'page' => $page,
                'pageSize' => $pageSize
            ]);

            if (!$response->successful() || $response->json('code') !== 0) {
                throw new \Exception("Error al obtener catálogo (página $page): " . $response->body());
            }

            $payload = $response->json('payload');
            $data = $payload['data'] ?? [];

            $todosLosProductos = array_merge($todosLosProductos, $data);
            $page++;
            $total = $payload['total'] ?? 0;

        } while (count($todosLosProductos) < $total);

        return $todosLosProductos;
    }

    //catalogo de productos. solo trae los primeros 100 productos
    // public function getCatalogProducts($page = 0, $pageSize = 100)
    // {
    //     $token = $this->getAccessToken();

    //     $response = Http::withHeaders([
    //         'Accept' => 'application/json',
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $token,
    //     ])->get("{$this->baseUrl}/prontipagos-external-api-ws/ws/protected/v1/product/list", [
    //         'page' => $page,
    //         'pageSize' => $pageSize
    //     ]);

    //     if ($response->successful() && $response->json('code') === 0) {
    //         return $response->json('payload.data'); // productos
    //     }

    //     throw new \Exception('Error al obtener catálogo: ' . $response->body());
    // }

    public function realizarVenta(array $datos)
    {
        $token = $this->getAccessToken();

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post("{$this->baseUrl}/prontipagos-external-api-ws/ws/protected/v1/sell/product", $datos);

        if ($response->successful() && $response->json('code') === 0) {
            return $response->json('payload');
        }

        throw new \Exception('Error al realizar venta: ' . $response->body());
    }

    public function getSaleStatus($transactionId)
    {
        $token = $this->getAccessToken();

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get("{$this->baseUrl}/prontipagos-external-api-ws/ws/protected/v1/check-status", [
            'transactionId' => $transactionId
        ]);

        if ($response->successful() && $response->json('code') === 0) {
            return $response->json('payload');
        }

        throw new \Exception('Error al consultar estado de venta: ' . $response->body());
    }


}
