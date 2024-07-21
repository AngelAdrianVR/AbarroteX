<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\CashRegister;
use App\Models\User;
use App\Notifications\OnlineSaleNotification;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create()
    {
        $total_users = User::where('store_id', auth()->user()->store_id)->get(['id'])->count();

        return inertia('User/Create', compact('total_users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rol' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
        ]);

        $store_id = auth()->user()->store_id;

        // primer caja registradora para asignar a empleado (paquete basico)
        $cash_register = CashRegister::where('store_id', $store_id)->first();

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'rol' => $request->rol,
            'store_id' => $store_id,
            'password' => bcrypt('ezyventas'),
            'cash_register_id' => $cash_register->id,
        ]);

        // return to_route('settings.index', ['tab' => 2]);
    }

    public function edit(User $user)
    {
        return inertia('User/Edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'rol' => 'required|string|max:255',
        ]);

        $user->update($request->all());

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
        ]);

        $user->update($request->all());
    }

    public function savePrinter(Request $request, User $user)
    {
        $user->update([
            'printer_config.printer' => $request->printer
        ]);
    }
}
