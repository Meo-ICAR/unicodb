<?php

// Script per correggere tutti i nomi dei Resource nelle List pages

$basePath = __DIR__ . '/app/Filament/Resources';

// Trova tutti i file List*.php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
foreach ($iterator as $file) {
    if ($file->isFile() && preg_match('/List.*\.php$/', $file->getFilename())) {
        $files[] = $file->getPathname();
    }
}

echo "Correggo " . count($files) . " file List pages\n\n";

$fixed = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;
    
    // Estrai informazioni dal percorso
    $pathParts = explode('/', str_replace($basePath . '/', '', $file));
    $resourceFolder = $pathParts[0]; // es. 'Clients', 'AddressTypes', etc.
    
    // Trova tutti i possibili Resource files
    $resourceFiles = [];
    
    // Cerca nella cartella del resource
    $folderPath = dirname($file);
    if (is_dir($folderPath)) {
        $resourceFiles = array_merge($resourceFiles, glob($folderPath . '/*Resource.php'));
    }
    
    // Cerca anche nella cartella principale Resources
    $mainPath = $basePath;
    if (is_dir($mainPath)) {
        $resourceFiles = array_merge($resourceFiles, glob($mainPath . '/*Resource.php'));
    }
    
    // Rimuovi duplicati
    $resourceFiles = array_unique($resourceFiles);
    
    if (!empty($resourceFiles)) {
        foreach ($resourceFiles as $resourceFile) {
            $resourceName = basename($resourceFile, '.php');
            
            // Verifica se questo resource potrebbe essere quello giusto
            if (stripos($resourceName, str_replace('Types', 'Type', $resourceFolder)) !== false || 
                stripos($resourceName, rtrim($resourceFolder, 's')) !== false ||
                stripos($resourceName, $resourceFolder) !== false) {
                
                $resourceClass = "App\\Filament\\Resources\\{$resourceFolder}\\{$resourceName}";
                
                // Se il resource è nella cartella principale, aggiusta il namespace
                if (dirname($resourceFile) === $mainPath) {
                    $resourceClass = "App\\Filament\\Resources\\{$resourceName}";
                }
                
                // Correggi import e riferimento nel file
                $newContent = $content;
                
                // Correggi l'import
                $newContent = preg_replace(
                    '/use App\\\\Filament\\\\Resources\\\\[^;]+;/',
                    "use {$resourceClass};",
                    $newContent
                );
                
                // Correggi il riferimento alla classe
                $newContent = preg_replace(
                    '/protected static string \$resource = [^;]+;/',
                    "protected static string \$resource = {$resourceName}::class;",
                    $newContent
                );
                
                if ($newContent !== $content) {
                    file_put_contents($file, $newContent);
                    echo "FIXED: " . basename($file) . " -> {$resourceName}\n";
                    $fixed++;
                    break; // Correggi solo il primo match
                }
            }
        }
    } else {
        echo "WARNING: Nessun Resource trovato per " . basename($file) . "\n";
    }
}

echo "\nCompletato!\n";
echo "File corretti: $fixed\n";
