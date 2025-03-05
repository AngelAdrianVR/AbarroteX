<?php

namespace App\Actions\Fortify;

use App\Models\CashRegister;
use App\Models\DiscountTicket;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Str;

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

        // crear slug del nombre de la tienda
        $slug = Str::slug($input['store_name'], '-', 'es');
        // Verifica si el slug ya existe en la base de datos
        $count = Store::where('slug', $slug)->count();
        // Si existe, agrega un sufijo numérico
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        //crea la tienda relacionada a este nuevo usuario.
        $store = Store::create([
            'name' => $input['store_name'],
            'slug' => $slug,
            'contact_name' => $input['name'],
            'address' => $input['address'],
            'type' => $input['type'],
            'contact_phone' => $input['contact_phone'],
            'next_payment' => now()->addDays(30),
            'online_store_properties' => [
                "whatsapp" => '',
                "delivery_price" => null,
                "delivery_conditions" => null,
                "min_free_delivery" => null,
                "inventory" => false,
                "sold_out_active" => false,
                "banner" => 1,
                "cash_payment" => true,
                "card_payment" => false,
                "mercado_pago" => false,
            ],
            'activated_modules' => $activated_modules,
        ]);

        // Obtener las configuraciones iniciales de la tienda
        $settings = Setting::all();

        $settings->each(function ($setting) use ($store) {
            // Lista de configuraciones que deben tener un valor de 0
            $settingsToZero = [
                'Impresión automática de tickets',
                'Control de inventario',
                'Aviso de monto máximo en caja',
            ];

            // Determinar el valor según el nombre de la configuración
            $value = in_array($setting->name, $settingsToZero) ? 0 : 1;

            // Asociar la configuración a la tienda con el valor correspondiente
            $store->settings()->attach($setting->id, ['value' => $value]);
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
            'phone' => $input['contact_phone'],
            'rol' => 'Administrador',
            'password' => Hash::make($input['password']),
            'store_id' => $store->id,
            'cash_register_id' => $cash_register->id,
            'printer_config' => [ //agrega las configuraciones por default de una impresora
                "UUIDService" => "49535343-fe7d-4ae5-8fa9-9fafd205e455",
                "UUIDCharacteristic" => "49535343-8841-43f4-a8d4-ecbe34729bb3",
                "automaticPrinting" => false,
            ],
            'scale_config' => [ //agrega las configuraciones por default de una báscula
                "baudRate" => 9600,
                "parity" => "none",
                "dataBit" => 8,
                "stopBit" => 1,
                "flowControl" => "none",
                "is_enabled" => false,
            ],
        ]);
        $user->syncRoles(['Administrador']);

        // crear un cupon de descuento para la tienda
        $cupon = DiscountTicket::create([
             // generar código alfanumerico que tenga que ver con el nombre de la tienda
            'code' => strtoupper(Str::random(5)),
            'description' => "Descuento de bienvenida para referidos de la tienda $store->name",
            'discount_amount' => 10,
        ]);

        // crear un partner con los datos de la tienda
        Partner::create([
            'name' => $input['store_name'],
            'phone' => $input['contact_phone'],
            'email' => $user->email,
            'discount_ticket_id' => $cupon->id,
        ]);

        return $user;
    }
}
