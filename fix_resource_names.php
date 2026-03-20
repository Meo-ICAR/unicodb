<?php

// Script per correggere i nomi dei Resource nelle List pages

$basePath = __DIR__ . '/app/Filament/Resources';

// Trova tutti i file List*.php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
foreach ($iterator as $file) {
    if ($file->isFile() && preg_match('/List.*\.php$/', $file->getFilename())) {
        $files[] = $file->getPathname();
    }
}

echo "Verifica di " . count($files) . " file per correggere i nomi dei Resource\n\n";

$fixed = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Estrai il nome del resource dal percorso
    $pathParts = explode('/', str_replace($basePath . '/', '', $file));
    $resourceFolder = $pathParts[0]; // es. 'AddressTypes', 'Agents', etc.
    
    // Trova il Resource file reale in quella cartella
    $resourceFiles = glob(dirname($file) . '/*Resource.php');
    
    if (!empty($resourceFiles)) {
        $resourceFile = basename($resourceFiles[0], '.php');
        $resourceClass = "App\\Filament\\Resources\\{$resourceFolder}\\{$resourceFile}";
        
        // Correggi import e riferimento nel file
        $newContent = $content;
        
        // Correggi l'import
        $newContent = preg_replace(
            '/use App\\\\Filament\\\\Resources\\\\' . preg_quote($resourceFolder) . '\\\\[^;]+;/',
            "use App\\Filament\\Resources\\{$resourceFolder}\\{$resourceFile};",
            $newContent
        );
        
        // Correggi il riferimento alla classe
        $newContent = preg_replace(
            '/protected static string \$resource = [^;]+;/',
            "protected static string \$resource = {$resourceFile}::class;",
            $newContent
        );
        
        if ($newContent !== $content) {
            file_put_contents($file, $newContent);
            echo "FIXED: " . basename($file) . " -> {$resourceFile}\n";
            $fixed++;
        } else {
            echo "OK: " . basename($file) . "\n";
        }
    } else {
        echo "WARNING: Nessun Resource trovato per " . basename($file) . "\n";
    }
}

echo "\nCompletato!\n";
echo "File corretti: $fixed\n";
