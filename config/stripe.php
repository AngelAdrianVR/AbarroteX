<?php
return [
    'sk' => env(key:'STRIPE_SK'), //llave secreta real
    'sk_test' => env(key:'STRIPE_SK_TEST'), //lave secreta de prueba
    'pk' => env(key:'STRIPE_PK'), //llave publica
];