<?php

namespace App\Actions\Fortify;

use App\Models\Banner;
use App\Models\CashRegister;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'store_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'address' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'min:10', 'max:10'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        //crea la tienda relacionada a este nuevo usuario.
        $store = Store::create([
            'name' => $input['store_name'],
            'contact_name' => $input['name'],
            'address' => $input['address'],
            'type' => $input['type'],
            'contact_phone' => $input['contact_phone'],
            'next_payment' => now()->addDays(15),
        ]);

        //Se crea el registro para guardar los banners en él con el id de la tienda
        Banner::create([
            'store_id' => $store->id
        ]);

        // agregar las configuraciones iniciales a la tienda
        $settings = Setting::all();
            $settings->each(function($setting) use ($store){
                $store->settings()->attach($setting->id, ['value' => null]);
            });

        //Crea la caja registradora para esta nueva tienda
        CashRegister::create([
            'name' => 'Nueva caja',
            'started_cash' => 0,
            'current_cash' => 0,
            'max_cash' => 5000,
            'store_id' => $store->id,
        ]);

        //crea al usuario relacionado a esta tienda con el rol de administrador
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'rol' => 'Administrador',
            'password' => Hash::make($input['password']),
            'store_id' => $store->id,
        ]);
    }
}
