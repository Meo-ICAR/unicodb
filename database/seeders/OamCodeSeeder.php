<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OamCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            // FASE 1: 1-Procacciamento
            [
                'code' => '1A',
                'fase' => '1-Procacciamento',
                'name' => 'contatto con soggetti non iscritti',
            ],
            [
                'code' => '1B',
                'fase' => '1-Procacciamento',
                'name' => 'utilizzo materiale pubblicitario non adeguato',
            ],
            [
                'code' => '1C',
                'fase' => '1-Procacciamento',
                'name' => 'utilizzo sito web non autorizzato',
            ],
            [
                'code' => '1D',
                'fase' => '1-Procacciamento',
                'name' => 'inosservanza delle policies aziendali in materia',
            ],
            // FASE 2: 2-Trasparenza
            [
                'code' => '2A',
                'fase' => '2-Trasparenza',
                'name' => "documentazione informativa e di trasparenza non presente nell'espositore o non liberamente asportabile",
            ],
            [
                'code' => '2B',
                'fase' => '2-Trasparenza',
                'name' => 'carenza generalizzata di informazioni presso la sede/uffici del collaboratore',
            ],
            [
                'code' => '2C',
                'fase' => '2-Trasparenza',
                'name' => 'consultazione delle informazioni creditizie del cliente in assenza di consenso dello stesso al fine di vincolarlo dal punto di vista commerciale',
            ],
            [
                'code' => '2D',
                'fase' => '2-Trasparenza',
                'name' => 'incompleta/parziale rappresentazione alla clientela delle caratteristiche del servizio di mediazione creditizia',
            ],
            // FASE 3: 3-Mediazione
            [
                'code' => '3A',
                'fase' => '3-Mediazione',
                'name' => "omessa consegna al cliente della documentazione precontrattuale e, ove richiesto, dello schema di contratto relativi al rapporto di mediazione, in tempo utile per consentire un consapevole e informato conferimento dell'incarico (inosservanza delle procedure aziendali)",
            ],
            [
                'code' => '3B',
                'fase' => '3-Mediazione',
                'name' => 'incompleta/imparziale compilazione della modulistica afferente al servizio di mediazione creditizia',
            ],
            [
                'code' => '3C',
                'fase' => '3-Mediazione',
                'name' => 'incompleta identificazione e verifica dei clienti/titolare effettivo/coobbligati - alterazione documentazione acquisita',
            ],
            [
                'code' => '3D',
                'fase' => '3-Mediazione',
                'name' => "mancato/imparziale utilizzo degli strumenti aziendali finalizzati alla gestione/trasmissione della documentazione alla societa'",
            ],
        ];

        DB::table('oam_codes')->insert($records);
    }
}
