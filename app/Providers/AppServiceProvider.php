<?php

namespace App\Providers;

use App\Models\ActivityLog;
// use App\Models\ClientMandate;
use App\Models\Practice;
use App\Observers\ClientMandateObserver;
use App\Observers\PracticeObserver;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*
         * // Qui facciamo il bind!
         * $this->app->bind(
         *     SignatureServiceInterface::class,
         *     YousignSignatureService::class
         * );
         */

        /*
         * * BONUS MULTI-TENANT:
         * Se ogni tenant avesse il proprio provider preferito configurato a DB,
         * potresti fare una logica dinamica qui dentro!
         */
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register utf8mb4_unicode_ci collation for SQLite (used in testing).
        // SQLite does not natively support MySQL collations, so we register a
        // no-op collation that falls back to standard string comparison.
        if ($this->app['db']->getDefaultConnection() === 'sqlite') {
            try {
                $pdo = $this->app['db']->connection()->getPdo();
                if (method_exists($pdo, 'sqliteCreateCollation')) {
                    $pdo->sqliteCreateCollation('utf8mb4_unicode_ci', 'strcmp');
                }
            } catch (\Throwable $e) {
                // Ignore errors if the connection is not yet available
            }
        }

        ActivityLog::creating(function (ActivityLog $activityLog) {
            if (Filament::getTenant()) {
                $activityLog->company_id = Filament::getTenant()->id;
            }
        });
    }

    //    ClientMandate::observe(ClientMandateObserver::class);
    // Practice::observe(PraticaObserver::class);
}
