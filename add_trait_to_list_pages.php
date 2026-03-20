<?php

// Script per aggiungere HasRegolamentoAction trait a tutte le List pages di Filament

$basePath = __DIR__ . '/app/Filament/Resources';
$traitImport = "use App\\Filament\\Traits\\HasRegolamentoAction;";
$traitUsage = "    use HasRegolamentoAction;";

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
    
    // Aggiungi import della trait dopo gli altri use
    $lines = explode("\n", $content);
    $newLines = [];
    $useAdded = false;
    $traitAdded = false;
    
    foreach ($lines as $line) {
        $newLines[] = $line;
        
        // Aggiungi import della trait dopo gli use statements
        if (!$useAdded && strpos($line, 'use ') === 0 && strpos($line, 'use App\\Filament\\Traits\\HasRegolamentoAction') === false) {
            // Cerca la fine degli use statements
            $nextLine = next($lines);
            prev($lines);
            
            if ($nextLine && (strpos($nextLine, 'use ') === 0 || strpos($nextLine, 'class ') === 0 || strpos($nextLine, 'abstract class ') === 0)) {
                if (strpos($nextLine, 'use ') === 0) {
                    continue; // Aspetta l'ultimo use
                }
            }
            
            if ($nextLine && (strpos($nextLine, 'class ') === 0 || strpos($nextLine, 'abstract class ') === 0)) {
                $newLines[] = $traitImport;
                $useAdded = true;
            }
        }
        
        // Aggiungi trait usage nella classe
        if (!$traitAdded && strpos($line, 'class ') === 0 && strpos($line, 'extends ListRecords') !== false) {
            $newLines[] = $traitUsage;
            $traitAdded = true;
        }
        
        // Modifica getHeaderActions per includere l'azione
        if (strpos($line, 'protected function getHeaderActions(): array') !== false) {
            // Leggi fino alla fine del metodo
            $methodLines = [$line];
            $braceCount = 0;
            $methodEnd = false;
            
            while (!$methodEnd && ($nextLine = next($lines))) {
                $methodLines[] = $nextLine;
                
                // Conta le parentesi graffe
                $braceCount += substr_count($nextLine, '{') - substr_count($nextLine, '}');
                
                if ($braceCount < 0) {
                    $methodEnd = true;
                    
                    // Rimuovi l'ultima riga (})
                    array_pop($methodLines);
                    
                    // Cerca il return array e aggiungi l'azione
                    $modifiedMethod = modifyHeaderActions(implode("\n", $methodLines));
                    $newLines = array_slice($newLines, 0, -count($methodLines));
                    $newLines = array_merge($newLines, explode("\n", $modifiedMethod));
                    $newLines[] = '}';
                    break;
                }
            }
        }
    }
    
    // Scrivi il file aggiornato
    file_put_contents($file, implode("\n", $newLines));
    echo "UPDATED: " . basename($file) . "\n";
    $updated++;
}

echo "\nCompletato!\n";
echo "Aggiornati: $updated file\n";
echo "Saltati: $skipped file\n";

function modifyHeaderActions($methodContent) {
    // Cerca il return array e aggiungi l'azione Regolamento
    if (preg_match('/(return\s+\[)(.*?)(\];)/s', $methodContent, $matches)) {
        $actions = $matches[2];
        
        // Se non ha già l'azione Regolamento, aggiungila
        if (strpos($actions, 'getRegolamentoAction') === false && strpos($actions, 'RegolamentoAction') === false) {
            // Rimuovi l'ultima virgola se presente
            $actions = rtrim($actions, ",\n");
            
            // Aggiungi l'azione
            $newActions = $actions . ",\n            \$this->getRegolamentoAction(),";
            
            return $matches[1] . $newActions . $matches[3];
        }
    }
    
    return $methodContent;
}
