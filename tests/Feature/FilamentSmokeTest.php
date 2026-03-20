<?php

namespace Tests\Feature;

use App\Models\Company;  // Sostituisci con il tuo modello Tenant (es. Team, Organization)
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FilamentSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Crea il Tenant
        $this->tenant = Company::factory()->state([
            'name' => 'Test Company',
        ])->create();

        $this->admin = User::factory()->create(['company_id' => $this->tenant->id]);

        // OPZIONE A: Forza l'utente a ignorare tutte le Policy
        \Illuminate\Support\Facades\Gate::before(function () {
            return true;
        });
    }

    #[Test]
    public function carica_il_dashboard_di_filament(): void
    {
        // In Filament 5, per i panel con tenant si usa getUrl($this->tenant)
        $url = Filament::getPanel('admin')->getUrl($this->tenant);

        $this
            ->actingAs($this->admin)
            ->get($url)
            ->assertStatus(200);
    }

    #[Test]
    public function caricano_tutti_i_resource_principali(): void
    {
        $this->actingAs($this->admin);
        $panel = Filament::getPanel('admin');

        foreach ($panel->getResources() as $resource) {
            // Passiamo il tenant per generare l'URL corretto: admin/{tenant}/resource
            $url = $resource::getUrl('index', ['tenant' => $this->tenant]);

            $this
                ->get($url)
                ->assertOk()
                ->assertSeeText($resource::getNavigationLabel());
        }
    }
}
