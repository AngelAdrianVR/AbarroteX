<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth.user.permissions' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->getAllPermissions()->pluck('name');
                }
                return [];
            },
            'auth.user.store' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->store->load('media');
                }
                return null;
            },
            'auth.user.store.first_user' => function () use ($request) {
                if ($request->user()) {
                    $firstUser = $request->user()->store->users()->first();
                    if ($firstUser) {
                        return [
                            'name' => $firstUser->name,
                            'email' => $firstUser->email,
                            'id' => $firstUser->id,
                            'phone' => $firstUser->phone,
                        ];
                    }
                }
                return null;
            },
            'auth.user.store.last_payment' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->store->lastPayment?->load('media');
                }
                return null;
            },
            'auth.user.store.csf' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->store->getFirstMedia('csf')?->original_url;
                }
                return null;
            },
            'auth.user.store.settings' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->store->settings->map(function ($setting) {
                        return [
                            'id' => $setting->id,
                            'name' => $setting->key,
                            'value' => $setting->pivot->value,
                        ];
                    });
                }
                return [];
            },
        ]);
    }
}
