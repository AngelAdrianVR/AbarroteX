<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\CashRegister;
use App\Models\User;
use App\Notifications\OnlineSaleNotification;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function create()
    {
        $total_users = User::where('store_id', auth()->user()->store_id)->get(['id'])->count();
        $permissions = Permission::all()->groupBy(function ($data) {
            return $data->category;
        });
        $roles = Role::where('id', '<>', 1) //todos los roles menos 'Administrador'
            ->where('store_id', auth()->user()->store_id)
            ->get();

        return inertia('User/Create', compact('total_users', 'permissions', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rol' => 'required|numeric|min:1',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
        ]);

        $store_id = auth()->user()->store_id;

        // primer caja registradora para asignar a empleado (paquete basico)
        $cash_register = CashRegister::where('store_id', $store_id)->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'store_id' => $store_id,
            'password' => bcrypt('ezyventas'),
            'cash_register_id' => $cash_register->id,
            'email_verified_at' => now(),
            'scale_config' => ["port" => null,"baudRate" => 9600,"parity" => "none","dataBit" => 8,"stopBit" => 1,"flowControl" => "none","is_enabled" => false],
        ]);

        $user->syncRoles($request->rol);
    }

    public function edit(User $user)
    {
        $permissions = Permission::all()->groupBy(function ($data) {
            return $data->category;
        });
        $roles = Role::where('id', '<>', 1) //todos los roles menos 'Administrador'
            ->where('store_id', auth()->user()->store_id)
            ->get();
            
        $user_rol = $user->roles->pluck('id')[0];

        return inertia('User/Edit', compact('user', 'permissions', 'roles', 'user_rol'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'rol' => 'required|numeric|min:1',
            'phone' => 'required',
        ]);

        $user->update($request->all());
        $user->syncRoles($request->rol);

        return to_route('settings.index', ['tab' => 2]);
    }

    public function show(User $user)
    {
        return inertia('User/Show');
    }

    public function getNotifications()
    {
        $items = NotificationResource::collection(auth()->user()->notifications->where('type', '!=', OnlineSaleNotification::class));

        return response()->json(compact('items'));
    }

    public function getOnlineSalesNotifications()
    {
        $items = NotificationResource::collection(auth()->user()->notifications->where('type', OnlineSaleNotification::class));

        return response()->json(compact('items'));
    }

    public function deleteNotifications(Request $request)
    {
        auth()->user()->notifications()->whereIn('id', $request->notifications_ids)->delete();

        return response()->json(['message' => "Se han eliminado las notificaciones seleccionadas"]);
    }

    public function readNotifications(Request $request)
    {
        $unread = auth()->user()->unreadNotifications->count();
        if ($request->notifications_ids) {
            auth()->user()->notifications->whereIn('id', $request->notifications_ids)->markAsRead();
        } else {
            auth()->user()->notifications->where('type', '!=', OnlineSaleNotification::class)->markAsRead();
        }

        return response()->json(compact('unread'));
    }

    public function readOnlineSalesNotifications(Request $request)
    {
        $unread = auth()->user()->unreadNotifications->count();
        if ($request->notifications_ids) {
            auth()->user()->notifications->whereIn('id', $request->notifications_ids)->markAsRead();
        } else {
            auth()->user()->notifications->where('type', OnlineSaleNotification::class)->markAsRead();
        }

        return response()->json(compact('unread'));
    }

    public function resetPassword(User $user)
    {
        $user->update([
            'password' => bcrypt('ezyventas')
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
    }

    public function tutorialsCompleted()
    {
        $user = auth()->user();
        $user->tutorials_seen = true;
        $user->save();

        return to_route('sales.point');
    }

    public function updatePrinterConfig(Request $request, User $user)
    {
        $request->validate([
            'printer_config.UUIDService' => 'nullable|string|min:1|max:255',
            'printer_config.UUIDCharacteristic' => 'nullable|string|min:1|max:255',
            'printer_config.ticketPrinterName' => 'nullable|string|min:1|max:255',
            'printer_config.ticketWidth' => 'nullable|string|min:1|max:255',
            'printer_config.ticketFinalWhiteLines' => 'nullable|numeric|min:1|max:255',
            'printer_config.ticketTerms' => 'nullable|string|max:255',
            'printer_config.labelPrinterName' => 'nullable|string|min:1|max:255',
            'printer_config.labelResolution' => 'nullable|numeric|min:1|max:5000',
            'printer_config.labelWidth' => 'nullable|numeric|min:0|max:5000',
            'printer_config.labelHeight' => 'nullable|numeric|min:0|max:5000',
            'printer_config.labelLineHeight' => 'nullable|numeric|min:0|max:5000',
            'printer_config.labelFont' => 'nullable|string|min:1|max:255',
            'printer_config.labelBarCodeHumanReadable' => 'nullable|string|min:1|max:255',
            'printer_config.labelGap' => 'nullable|numeric|min:0|max:5000',
        ]);

        $user->update($request->all());
    }

    public function savePrinter(Request $request, User $user)
    {
        $user->update([
            'printer_config.printer' => $request->printer
        ]);
    }

    public function getParzibyteSerial()
    {
        return response()->json(['serial' => env('PARZIBYTE_API_SERIAL', null)]);
    }
}
