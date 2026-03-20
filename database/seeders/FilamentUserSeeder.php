<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FilamentUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = Company::where('vat_number', '05822361007')->first()->id;
        // Crea utente Filament admin
        User::firstOrCreate(
            ['email' => 'hassistosrl@gmail.com'],
            [
                'name' => 'Hassisto Admin',
                'password' => Hash::make('password'),
                'company_id' => $companyId,  // NULL per Super Admin globali
            ]
        );

        // Crea utente Filament admin con email univoca
        User::firstOrCreate(
            ['email' => 'mario@globadvisor.it'],
            [
                'name' => 'Mario Gargiulo',
                'password' => Hash::make('password'),
                'company_id' => $companyId,  // NULL per Super Admin globali
            ]
        );

        // Crea utente per Aces Finance
        User::firstOrCreate(
            ['email' => 'sergio.bracale@races.it'],
            [
                'name' => 'Sergio Bracale',
                'password' => Hash::make('password'),
                'company_id' => $companyId,  // NULL per Super Admin globali
            ]
        );
    }
}
