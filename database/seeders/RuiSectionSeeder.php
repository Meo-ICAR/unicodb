<?php

namespace Database\Seeders;

use App\Models\RuiSection;
use Illuminate\Database\Seeder;

class RuiSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            [
                'sezione' => 'A',
                'categoria' => 'Agenti',
                'descrizione' => 'Intermediari che agiscono in nome/per conto di una o più imprese di assicurazione o riassicurazione.'
            ],
            [
                'sezione' => 'B',
                'categoria' => 'Broker',
                'descrizione' => 'Intermediari che agiscono su incarico del cliente e non hanno poteri di rappresentanza di imprese.'
            ],
            [
                'sezione' => 'C',
                'categoria' => 'Produttori Diretti',
                'descrizione' => 'Produttori diretti di imprese di assicurazione (spesso monomandatari).'
            ],
            [
                'sezione' => 'D',
                'categoria' => 'Banche/Finanziarie',
                'descrizione' => 'Banche, intermediari finanziari, SIM e Poste Italiane - Divisione servizi di bancoposta.'
            ],
            [
                'sezione' => 'E',
                'categoria' => 'Collaboratori',
                'descrizione' => 'Collaboratori degli intermediari iscritti nelle sezioni A, B, D, F o nell\'Elenco annesso.'
            ],
            [
                'sezione' => 'F',
                'categoria' => 'Accessori',
                'descrizione' => 'Intermediari assicurativi a titolo accessorio che operano su incarico di una o più imprese.'
            ],
        ];

        foreach ($sections as $section) {
            RuiSection::create($section);
        }

        $this->command->info('Successfully seeded RUI sections.');
    }
}
