<?php

declare(strict_types=1);

namespace Workbench\App\Providers\Filament;

use Awcodes\Typebar\TypebarPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Workbench\App\Filament\Pages\Auth\Login;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors(['primary' => Color::Indigo])
            ->discoverResources(
                in: __DIR__ . '/../../Filament/Resources',
                for: 'Workbench\App\Filament\Resources',
            )
            ->plugin(
                TypebarPlugin::make()
                    ->keys(['#', '*', '_', '[', ']', '(', ')', '`'])
                    ->pairs(['(' => ')', '[' => ']', '`' => '`'])
                    ->mobileOnly(false)
                    ->collapsible(),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                $this->csrfMiddleware(),
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * @return class-string
     */
    private function csrfMiddleware(): string
    {
        // PreventRequestForgery replaced VerifyCsrfToken in Laravel 13. Laravel 12 rows
        // in the CI matrix don't have it yet, so fall back there, as Filament's own panel
        // generator does.
        return class_exists(PreventRequestForgery::class)
            ? PreventRequestForgery::class
            : VerifyCsrfToken::class;
    }
}
