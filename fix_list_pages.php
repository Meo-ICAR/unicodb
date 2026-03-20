<?php

// Script migliorato per aggiungere HasRegolamentoAction trait a tutte le List pages

$basePath = __DIR__ . '/app/Filament/Resources';

// Trova tutti i file List*.php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
foreach ($iterator as $file) {
    if ($file->isFile() && preg_match('/List.*\.php$/', $file->getFilename())) {
        $files[] = $file->getPathname();
    }
}

echo "Trovati " . count($files) . " file List da processare\n\n";

$updated = 0;
$skipped = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Verifica se ha già la trait
    if (strpos($content, 'HasRegolamentoAction') !== false) {
        echo "SKIP: " . basename($file) . " (ha già la trait)\n";
        $skipped++;
        continue;
    }
    
    // Verifica se ha getHeaderActions method
    if (strpos($content, 'getHeaderActions') === false) {
        echo "SKIP: " . basename($file) . " (non ha getHeaderActions)\n";
        $skipped++;
        continue;
    }
    
    // Pattern per trovare e aggiornare il file
    $patterns = [
        // Aggiungi import della trait
        '/^(use\s+[^;]+;)\n^(class\s+\w+\s+extends\s+ListRecords)\n^{/m' => function($matches) {
            return $matches[1] . "\nuse App\\Filament\\Traits\\HasRegolamentoAction;\n" . $matches[2] . "\n{\n    use HasRegolamentoAction;";
        },
        // Aggiungi trait usage se manca
        '/^(class\s+\w+\s+extends\s+ListRecords)\n^\{\n(?!\s*use\s+HasRegolamentoAction)/m' => function($matches) {
            return $matches[0] . "\n    use HasRegolamentoAction;";
        },
        // Aggiungi l'azione nel getHeaderActions
        '/(return\s+\[)([^]]*?)(\];)/s' => function($matches) {
            $actions = $matches[2];
            
            // Se non ha già l'azione Regolamento, aggiungila
            if (strpos($actions, 'getRegolamentoAction') === false && strpos($actions, 'RegolamentoAction') === false) {
                // Rimuovi l'ultima virgola se presente e aggiungi la virgola + nuova azione
                $actions = rtrim($actions, ",\n ");
                $newActions = $actions . ",\n            \$this->getRegolamentoAction()";
                return $matches[1] . $newActions . $matches[3];
            }
            
            return $matches[0];
        }
    ];
    
    $newContent = $content;
    $changed = false;
    
    foreach ($patterns as $pattern => $callback) {
        $newContent = preg_replace_callback($pattern, $callback, $newContent, -1, $count);
        if ($count > 0) {
            $changed = true;
        }
    }
    
    if ($changed) {
        file_put_contents($file, $newContent);
        echo "UPDATED: " . basename($file) . "\n";
        $updated++;
    } else {
        echo "SKIP: " . basename($file) . " (nessuna modifica necessaria)\n";
        $skipped++;
    }
}

echo "\nCompletato!\n";
echo "Aggiornati: $updated file\n";
echo "Saltati: $skipped file\n";
