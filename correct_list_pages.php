<?php

// Script per correggere la sintassi delle trait nelle List pages

$basePath = __DIR__ . '/app/Filament/Resources';

// Trova tutti i file List*.php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
foreach ($iterator as $file) {
    if ($file->isFile() && preg_match('/List.*\.php$/', $file->getFilename())) {
        $files[] = $file->getPathname();
    }
}

echo 'Trovati ' . count($files) . " file List da correggere\n\n";

$corrected = 0;
$skipped = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);

    // Verifica se ha la trait con sintassi errata
    if (strpos($content, 'use HasRegolamentoAction;') !== false && strpos($content, 'use App\Filament\Traits\HasRegolamentoAction;') === false) {
        // Correggi la sintassi
        $newContent = preg_replace(
            [
                '/^(class\s+\w+\s+extends\s+ListRecords)\s+use\s+HasRegolamentoAction;\s*{/m',
                '/use\s+HasRegolamentoAction;/'
            ],
            [
                '$1\n{\n    use HasRegolamentoAction;',
                'use App\Filament\Traits\HasRegolamentoAction;'
            ],
            $content
        );

        // Aggiungi l'azione nel getHeaderActions se mancante
        if (strpos($newContent, 'getRegolamentoAction()') === false) {
            $newContent = preg_replace_callback(
                '/(return\s+\[)([^]]*?)(\];)/s',
                function ($matches) {
                    $actions = $matches[2];
                    $actions = rtrim($actions, ",\n ");
                    $newActions = $actions . ",\n            \$this->getRegolamentoAction()";
                    return $matches[1] . $newActions . $matches[3];
                },
                $newContent
            );
        }

        file_put_contents($file, $newContent);
        echo 'CORRECTED: ' . basename($file) . "\n";
        $corrected++;
    } else {
        echo 'SKIP: ' . basename($file) . " (sintassi corretta o nessuna trait)\n";
        $skipped++;
    }
}

echo "\nCompletato!\n";
echo "Corretti: $corrected file\n";
echo "Saltati: $skipped file\n";
