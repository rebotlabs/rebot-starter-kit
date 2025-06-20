<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'i18n' => [
                'current' => app()->getLocale(),
                'default' => config('app.fallback_locale'),
                'messages' => $this->getTranslationMessages(app()->getLocale()),
            ],
            'ziggy' => fn (): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'currentOrganization' => $request->user()?->currentOrganization,
            'organizations' => $request->user()?->organizations()?->get(),
            'currentUserRole' => function () use ($request) {
                $organization = $request->user()?->currentOrganization;

                return $organization?->getCurrentUserRole();
            },
            'currentUserCanManage' => function () use ($request) {
                $organization = $request->user()?->currentOrganization;

                return $organization?->currentUserCanManage() ?? false;
            },
            'unreadNotificationsCount' => function () use ($request) {
                return $request->user()?->unreadNotifications()->count() ?? 0;
            },
        ];
    }

    /**
     * Get translation messages for SSR hydration
     */
    private function getTranslationMessages(string $locale): array
    {
        $filePath = public_path("lang/php_{$locale}.json");

        if (! file_exists($filePath)) {
            return [];
        }

        $contents = file_get_contents($filePath);

        if ($contents === false) {
            return [];
        }

        return json_decode($contents, true) ?? [];
    }
}
