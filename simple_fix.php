<?php

// Script semplice per correggere i problemi principali nelle List pages

$basePath = __DIR__ . '/app/Filament/Resources';

// Trova tutti i file List*.php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
foreach ($iterator as $file) {
    if ($file->isFile() && preg_match('/List.*\.php$/', $file->getFilename())) {
        $files[] = $file->getPathname();
    }
}

echo "Trovati " . count($files) . " file da correggere\n\n";

$fixed = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;
    
    // Correggi i problemi più comuni
    $content = str_replace('\\n', "\n", $content);
    $content = str_replace('\\\\n', "\n", $content);
    
    // Correggi la dichiarazione della classe
    $content = preg_replace(
        '/class\s+(\w+)\s+extends\s+ListRecords\s*\{\\s*use\s+App\\\\Filament\\\\Traits\\\\HasRegolamentoAction;/',
        "class $1 extends ListRecords\n{\n    use HasRegolamentoAction;",
        $content
    );
    
    // Correggi l'import
    $content = str_replace('use App\\Filament\\Traits\\HasRegolamentoAction;', 'use App\Filament\Traits\HasRegolamentoAction;', $content);
    
    // Correggi le virgole negli array
    $content = preg_replace('/\$this->getRegolamentoAction(\]\s*;/', '$this->getRegolamentoAction(),', $content);
    $content = preg_replace('/\$this->getRegolamentoAction(\]\s*}/', '$this->getRegolamentoAction(),
        ];', $content);
    
    // Formatta correttamente l'array delle azioni
    $content = preg_replace_callback(
        '/(return\s+\[)([^]]*?)(\];)/s',
        function($matches) {
            $actions = trim($matches[2]);
            $actions = rtrim($actions, ",\n ");
            return $matches[1] . $actions . $matches[3];
        },
        $content
    );
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "FIXED: " . basename($file) . "\n";
        $fixed++;
    } else {
        echo "SKIP: " . basename($file) . " (già corretto)\n";
    }
}

echo "\nCompletato!\n";
echo "File corretti: $fixed\n";
