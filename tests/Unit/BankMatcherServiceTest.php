<?php

use App\Services\BankMatcherService;
use Illuminate\Database\Eloquent\Collection;

// Helper to create a fake Abi object with name and abi properties
function makeFakeBank(string $name, string $abi): object
{
    return new class($name, $abi) {
        public string $name;
        public string $abi;
        public ?string $ivass_name = null;

        public function __construct(string $name, string $abi)
        {
            $this->name = $name;
            $this->abi  = $abi;
        }
    };
}

// --- normalize tests ---

it('normalize rimuove le stopword bancarie e i caratteri non alfanumerici', function () {
    $service = new BankMatcherService();

    $result = $service->normalize('Banca Progetto S.p.A.');

    expect($result)->toBe('progetto');
});

it('normalize con stringa vuota restituisce stringa vuota', function () {
    $service = new BankMatcherService();

    expect($service->normalize(''))->toBe('');
});

// --- findBestAbi tests ---

it('findBestAbi con match esatto restituisce il codice ABI corretto', function () {
    $fakeBank = makeFakeBank('BANCA PROGETTO', '03228');

    $collection = new Collection([$fakeBank]);

    // Mock App\Models\Abi::all() via Mockery
    \Mockery::mock('alias:App\Models\Abi')
        ->shouldReceive('all')
        ->once()
        ->andReturn($collection);

    $service = new BankMatcherService();

    expect($service->findBestAbi('Banca Progetto'))->toBe('03228');
})->tearDown(fn () => \Mockery::close());

it('findBestAbi con nome sconosciuto restituisce null', function () {
    $collection = new Collection([]);

    \Mockery::mock('alias:App\Models\Abi')
        ->shouldReceive('all')
        ->once()
        ->andReturn($collection);

    $service = new BankMatcherService();

    expect($service->findBestAbi('Banca Inesistente XYZ'))->toBeNull();
})->tearDown(fn () => \Mockery::close());

// Feature: app-cleanup-docs-tests, Property 1: Idempotenza di normalize
it('normalize è idempotente per qualsiasi stringa di input', function () {
    // Validates: Requirements 6.5
    $service = new BankMatcherService();

    for ($i = 0; $i < 100; $i++) {
        $s = fake()->words(rand(1, 5), true);
        expect($service->normalize($service->normalize($s)))->toBe($service->normalize($s));
    }
});

// Feature: app-cleanup-docs-tests, Property 2: Output pulito di normalize
it('normalize produce output privo di stopword e caratteri non alfanumerici', function () {
    // Validates: Requirements 6.1
    $service = new BankMatcherService();

    $stopwords = ['banca', 'spa', 's.p.a.', 'srl', 'credito'];

    for ($i = 0; $i < 100; $i++) {
        // Pick a random stopword and inject it as a standalone word
        $stopword = $stopwords[array_rand($stopwords)];
        $base     = fake()->words(rand(1, 4), true);
        $input    = $base . ' ' . $stopword . ' ' . fake()->word();

        $result = $service->normalize($input);

        // Output must match only lowercase alphanumeric chars (no spaces, no special chars)
        expect($result)->toMatch('/^[a-z0-9]*$/');

        // The stopword alone must normalize to empty string (it is a whole-word stopword)
        expect($service->normalize($stopword))->toBe('');
    }
});
