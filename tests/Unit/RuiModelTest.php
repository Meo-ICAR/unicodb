<?php

use App\Models\Rui;
use Carbon\Carbon;

// --- calculateCodiceFiscale tests ---

it('calculateCodiceFiscale con cognome_nome vuoto restituisce stringa vuota', function () {
    $rui = new Rui();
    $rui->cognome_nome = '';

    expect($rui->calculateCodiceFiscale())->toBe('');
});

// --- cast tests ---

it('data_iscrizione come stringa ISO viene castata a istanza Carbon', function () {
    $rui = new Rui();
    $rui->setRawAttributes(['data_iscrizione' => '2020-01-15']);

    expect($rui->data_iscrizione)->toBeInstanceOf(Carbon::class);
});

it('inoperativo = 1 viene castato a true booleano', function () {
    $rui = new Rui();
    $rui->inoperativo = 1;

    expect($rui->inoperativo)->toBe(true);
});

// Feature: app-cleanup-docs-tests, Property 4: calculateCodiceFiscale non vuoto per dati completi
it('calculateCodiceFiscale restituisce stringa non vuota per dati completi (Property 4)', function () {
    // Validates: Requirements 8.1
    for ($i = 0; $i < 100; $i++) {
        $rui = new Rui();
        $cognomeNome   = fake()->lastName() . ' ' . fake()->firstName();
        $comuneNascita = fake()->city();
        $dataNascita   = fake()->date('Y-m-d', '2000-01-01');

        $rui->setRawAttributes([
            'cognome_nome'   => $cognomeNome,
            'comune_nascita' => $comuneNascita,
            'data_nascita'   => $dataNascita,
        ]);

        $result = $rui->calculateCodiceFiscale();

        expect($result)->toBeString()->not->toBe('');
    }
});
