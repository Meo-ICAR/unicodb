<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Client;
use App\Models\ClientPractice;
use App\Models\ClientType;
use App\Models\Company;
use App\Models\Practice;
use App\Models\PracticeScope;
use App\Models\Principal;
use App\Models\User;
use Illuminate\Database\Seeder;

class OperationalSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        $agentUser = User::where('email', 'agent@agency.com')->first();
        $principal = Principal::first();
        $practiceScope = PracticeScope::first();
        $clientType = ClientType::first();

        if (!$company || !$agentUser || !$principal || !$practiceScope || !$clientType) {
            return;
        }

        // Create Client
        $client = Client::firstOrCreate(
            ['tax_code' => 'RSSMRA80A01H501U', 'company_id' => $company->id],
            [
                'is_person' => 1,
                'name' => 'Rossi',
                'first_name' => 'Mario',
                'email' => 'mario.rossi@example.com',
                'phone' => '3331234567',
                'client_type_id' => $clientType->id
            ]
        );

        // Add Address to Client
        if ($client->wasRecentlyCreated) {
            $address = new Address([
                'name' => 'Residenza',
                'street' => 'Via Roma 10',
                'city' => 'Milano',
                'zip_code' => '20100'
            ]);
            $client->addresses()->save($address);
        }

        // Create Practice
        $practice = Practice::firstOrCreate(
            ['name' => 'Mutuo Acquisto Prima Casa Rossi', 'company_id' => $company->id],
            [
                'principal_id' => $principal->id,
                'agent_id' => $agentUser->id,
                'CRM_code' => 'CRM-1001',
                'principal_code' => 'PRIN-2002',
                'amount' => 150000.0,
                'net' => 150000.0,
                'practice_scope_id' => $practiceScope->id,
                'status' => 'working',
                'perfected_at' => now()->toDateString(),
                'is_active' => 1
            ]
        );

        // Bind Client to Practice
        ClientPractice::firstOrCreate(
            ['practice_id' => $practice->id, 'client_id' => $client->id],
            [
                'role' => 'intestatario',
                'name' => 'Intestatario Principale',
                'company_id' => $company->id
            ]
        );
    }
}
