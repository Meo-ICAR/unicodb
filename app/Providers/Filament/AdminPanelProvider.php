<?php

namespace App\Providers\Filament;

use AlizHarb\ActivityLog\ActivityLogPlugin;
use App\Models\Company;
use App\Models\User;  // <-- AGGIUNGI QUESTA RIGA
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\MicrosoftExtendSocialite;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->databaseNotifications()
            ->navigationGroups([
                'Pratiche',
                'Amministrazione',
                'Formazione',
                'Compliance',
                'Segnalazioni',
                'Organizzazione',
                'Configurazione',
                'Archivi',
                'Elenchi'
            ])
            ->default()
            ->id('admin')
            ->path('admin')
            ->favicon(asset('favicon.ico'))
            ->brandName('Compilio')
            ->brandLogo(asset('logo_compilio.png'))
            ->login()
            ->tenant(Company::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                //      AccountWidget::class,
                //     FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                ActivityLogPlugin::make()
                    ->label('Log')
                    ->pluralLabel('Logs')
                    ->navigationGroup('Compliance'),
                // ->cluster('System'),  // Optional: Group inside a cluster
                FilamentSocialitePlugin::make()
                    ->providers([
                        Provider::make('microsoft')
                            ->color('blue')  // or 'gray' for a lighter gray
                            ->label('Microsoft'),
                        Provider::make('google')
                            ->color('red')  // or 'gray' for a lighter gray
                            ->label('Google')
                    ])
                    ->redirectAfterLoginUsing(function (string $provider, FilamentSocialiteUserContract $socialiteUser, FilamentSocialitePlugin $plugin) {
                        // Change the redirect behaviour here.
                        $tenant = $user->getTenants($user->panel('admin'))->first();

                        if ($tenant) {
                            // 2. Genera l'URL per la dashboard del tenant specifico
                            return \Filament\Facades\Filament::getPanel('admin')
                                ->getUrl($tenant);
                        }
                    })
                    ->createUserUsing(function (string $provider, $oauthUser, $plugin) {
                        // Logica personalizzata per creare l'utente
                        return User::create([
                            'name' => $oauthUser->getName(),
                            'email' => $oauthUser->getEmail(),
                            'password' => null,  // Password nullable obbligatoria per Socialite
                            'avatar_url' => $oauthUser->getAvatar(),  // Salva l'URL di Google
                            'email_verified_at' => now(),  // Google certifica l'email, quindi la segniamo come verificata
                            'password' => Hash::make(Str::random(32)),  // Password casuale sicura
                        ]);
                    }),
            ]);
    }
}
