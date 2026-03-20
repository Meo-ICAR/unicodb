<?php

namespace Database\Seeders;

use App\Models\Firr;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FirrSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $firrs = [
            [
                'id' => 2,
                'minimo' => 6201.0,
                'massimo' => 9300.0,
                'aliquota' => 2.0,
                'competenza' => 2025,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'minimo' => 9301.0,
                'massimo' => 99999999.0,
                'aliquota' => 1.0,
                'competenza' => 2025,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'minimo' => 0.0,
                'massimo' => 12400.0,
                'aliquota' => 4.0,
                'competenza' => 2025,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'minimo' => 12401.0,
                'massimo' => 18600.0,
                'aliquota' => 2.0,
                'competenza' => 2025,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'minimo' => 18601.0,
                'massimo' => 99999999.0,
                'aliquota' => 1.0,
                'competenza' => 2025,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'minimo' => 0.0,
                'massimo' => 6200.0,
                'aliquota' => 4.0,
                'competenza' => 2026,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'minimo' => 6201.0,
                'massimo' => 9300.0,
                'aliquota' => 2.0,
                'competenza' => 2026,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'minimo' => 9301.0,
                'massimo' => 99999999.0,
                'aliquota' => 1.0,
                'competenza' => 2026,
                'enasarco' => 'plurimandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 10,
                'minimo' => 0.0,
                'massimo' => 12400.0,
                'aliquota' => 4.0,
                'competenza' => 2026,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 11,
                'minimo' => 12401.0,
                'massimo' => 18600.0,
                'aliquota' => 2.0,
                'competenza' => 2026,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 12,
                'minimo' => 18601.0,
                'massimo' => 99999999.0,
                'aliquota' => 1.0,
                'competenza' => 2026,
                'enasarco' => 'monomandatario',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        foreach ($firrs as $firr) {
            Firr::updateOrCreate(['id' => $firr['id']], $firr);
        }
    }
}
