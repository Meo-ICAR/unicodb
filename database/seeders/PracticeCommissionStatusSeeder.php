<?php

namespace Database\Seeders;

use App\Models\PracticeCommissionStatus;
use Illuminate\Database\Seeder;

class PracticeCommissionStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'id' => 1,
                'name' => 'pratica perfezionata',
                'code' => 'Mediafacile',
                'is_perfectioned' => 1,
                'is_working' => null,
            ],
            [
                'id' => 2,
                'name' => 'pratica in lavorazione',
                'code' => 'Mediafacile',
                'is_perfectioned' => null,
                'is_working' => 1,
            ],
            [
                'id' => 3,
                'name' => 'non definito',  // Gestione record vuoto
                'code' => 'Mediafacile',
                'is_perfectioned' => null,
                'is_working' => null,
            ],
            [
                'id' => 4,
                'name' => 'pratica deliberata',
                'code' => 'Mediafacile',
                'is_perfectioned' => null,
                'is_working' => 1,
            ],
            [
                'id' => 5,
                'name' => 'pratica erogata',
                'code' => 'Mediafacile',
                'is_perfectioned' => null,
                'is_working' => 1,
            ],
        ];
        foreach ($statuses as $status) {
            PracticeCommissionStatus::firstOrCreate(
                ['id' => $status['id']],
                $status
            );
        }
    }
}
