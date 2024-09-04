<?php

namespace App\Actions\Fortify;

use App\Models\Banner;
use App\Models\CashRegister;
use App\Models\Logo;
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
            'address' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'min:10', 'max:10'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // modulos esenciales incluidos
        $activated_modules = ['Punto de venta', 'Reportes', 'Ventas registradas', 'Productos', 'Configuraciones', 'Caja'];

        // Combina los módulos esenciales con los módulos activados desde el formulario
        $activated_modules = array_merge($activated_modules, $input['activated_modules']);

        //crea la tienda relacionada a este nuevo usuario.
        $store = Store::create([
            'name' => $input['store_name'],
            'contact_name' => $input['name'],
            'address' => $input['address'],
            'type' => $input['type'],
            'contact_phone' => $input['contact_phone'],
            'next_payment' => now()->addDays(2),
            'online_store_properties' => [],
            'activated_modules' => $activated_modules,
        ]);

        //Se crea el registro para guardar los banners en él con el id de la tienda. Pra tienda en línea
        Banner::create([
            'store_id' => $store->id
        ]);

        //Se crea el registro para guardar el logotipo en él con el id de la tienda. Pra tienda en línea
        Logo::create([
            'store_id' => $store->id
        ]);

        // agregar las configuraciones iniciales a la tienda
        $settings = Setting::all();
        $settings->each(function ($setting) use ($store) {
            $store->settings()->attach($setting->id, ['value' => 1]);
        });

        //Crea la caja registradora para esta nueva tienda
        $cash_register = CashRegister::create([
            'name' => 'Caja principal',
            'started_cash' => 0,
            'current_cash' => 0,
            'max_cash' => 5000,
            'store_id' => $store->id,
        ]);

        //crea al usuario relacionado a esta tienda con el rol de administrador
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'rol' => 'Administrador',
            'password' => Hash::make($input['password']),
            'store_id' => $store->id,
            'cash_register_id' => $cash_register->id,
        ]);
        $user->syncRoles(['Administrador']);

        return $user;
    }
}
