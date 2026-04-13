<?php

use App\Services\RuiCsvImportService;

uses(\Tests\TestCase::class, \Illuminate\Foundation\Testing\RefreshDatabase::class);

// --- getImportClass tests (via Reflection) ---

it('getImportClass restituisce la classe corretta per ciascuno dei 9 nomi file validi', function () {
    $service = new RuiCsvImportService();
    $method  = new ReflectionMethod($service, 'getImportClass');
    $method->setAccessible(true);

    $expected = [
        'ELENCO_SITO_INTERNET'              => 'App\Imports\RuiWebsitesImport',
        'ELENCO_MANDATI'                    => 'App\Imports\RuiMandatiImport',
        'ELENCO_COLLABORATORI'              => 'App\Imports\RuiCollaboratoriImport',
        'ELENCO_COLLABACCESSORI'            => 'App\Imports\RuiAccessorisImport',
        'ELENCO_INTERMEDIARI'               => 'App\Imports\RuiIntermediariImport',
        'ELENCO_SEDI'                       => 'App\Imports\RuiSediImport',
        'ELENCO_AG_VEN_PROD_NONST_ISCR_S'  => 'App\Imports\RuiAgentisImport',
        'ELENCO_RESP_DISTRIB_SEZ_D'         => 'App\Imports\RuiSezdsImport',
        'ELENCO_CARICHE'                    => 'App\Imports\RuiCaricheImport',
    ];

    foreach ($expected as $fileName => $importClass) {
        expect($method->invoke($service, $fileName))->toBe($importClass);
    }
});

// --- getTableNameFromFileName tests (via Reflection) ---

it('getTableNameFromFileName restituisce la tabella corretta per ciascuno dei 9 nomi file validi', function () {
    $service = new RuiCsvImportService();
    $method  = new ReflectionMethod($service, 'getTableNameFromFileName');
    $method->setAccessible(true);

    $expected = [
        'ELENCO_SITO_INTERNET'              => 'rui_websites',
        'ELENCO_MANDATI'                    => 'rui_mandati',
        'ELENCO_COLLABORATORI'              => 'rui_collaboratori',
        'ELENCO_COLLABACCESSORI'            => 'rui_accessoris',
        'ELENCO_INTERMEDIARI'               => 'rui',
        'ELENCO_SEDI'                       => 'rui_sedi',
        'ELENCO_AG_VEN_PROD_NONST_ISCR_S'  => 'rui_agentis',
        'ELENCO_RESP_DISTRIB_SEZ_D'         => 'rui_sezds',
        'ELENCO_CARICHE'                    => 'rui_cariche',
    ];

    foreach ($expected as $fileName => $tableName) {
        expect($method->invoke($service, $fileName))->toBe($tableName);
    }
});

// --- clearAllRuiData test ---
// Uses Laravel TestCase with RefreshDatabase to test against SQLite in-memory

it('clearAllRuiData su tabelle vuote restituisce success true', function () {
    $service = new RuiCsvImportService();
    $result  = $service->clearAllRuiData();

    expect($result)->toHaveKey('success')
        ->and($result['success'])->toBeTrue();
});

// --- getAvailableRuiTables test ---

it('getAvailableRuiTables restituisce esattamente 9 voci', function () {
    $service = new RuiCsvImportService();

    $tables = $service->getAvailableRuiTables();

    expect($tables)->toHaveCount(9);
});

// Feature: app-cleanup-docs-tests, Property 3: getImportClass null per nomi non riconosciuti
// Validates: Requirements 7.2

it('Property 3: getImportClass restituisce null per nomi file non riconosciuti', function () {
    $knownNames = [
        'ELENCO_SITO_INTERNET',
        'ELENCO_MANDATI',
        'ELENCO_COLLABORATORI',
        'ELENCO_COLLABACCESSORI',
        'ELENCO_INTERMEDIARI',
        'ELENCO_SEDI',
        'ELENCO_AG_VEN_PROD_NONST_ISCR_S',
        'ELENCO_RESP_DISTRIB_SEZ_D',
        'ELENCO_CARICHE',
    ];

    $service = new RuiCsvImportService();
    $method  = new ReflectionMethod($service, 'getImportClass');
    $method->setAccessible(true);

    for ($i = 0; $i < 100; $i++) {
        // Use uuid or word — these will never match any known RUI file name
        $randomName = fake()->uuid();

        // Ensure it's not accidentally a known name (extremely unlikely with uuid, but be safe)
        if (in_array($randomName, $knownNames, true)) {
            continue;
        }

        expect($method->invoke($service, $randomName))->toBeNull();
    }
});
