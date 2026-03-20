<?php

// Script finale per correggere completamente la sintassi delle trait nelle List pages

$basePath = __DIR__ . '/app/Filament/Resources';

// Trova tutti i file List*.php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
foreach ($iterator as $file) {
    if ($file->isFile() && preg_match('/List.*\.php$/', $file->getFilename())) {
        $files[] = $file->getPathname();
    }
}

echo "Trovati " . count($files) . " file List da correggere definitivamente\n\n";

$fixed = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Pattern per correggere vari problemi di sintassi
    $corrections = [
        // Correggi la dichiarazione della classe con trait mal posizionata
        '/class\s+(\w+)\s+extends\s+ListRecords\\\\n\\{\\\\n\s+use\s+App\\\\Filament\\\\Traits\\\\HasRegolamentoAction;/' => "class $1 extends ListRecords\n{\n    use HasRegolamentoAction;",
        
        // Correggi use trait senza import
        '/use\s+App\\\\Filament\\\\Traits\\\\HasRegolamentoAction;/' => 'use App\Filament\Traits\HasRegolamentoAction;',
        
        // Correggi parentesi graffe e virgole mal posizionate
        '/\$this->getRegolamentoAction(\]\s*;/' => '$this->getRegolamentoAction(),',
        
        // Correggi la chiusura dell'array
        '/\$this->getRegolamentoAction(\]\s*}/' => '$this->getRegolamentoAction(),
        ];',
        
        // Assicurati che ci sia l'import della trait
        '/^(use\s+[^;]+;)\n^(class\s+\w+\s+extends\s+ListRecords)/m' => function($matches) {
            if (strpos($matches[1], 'HasRegolamentoAction') === false) {
                return $matches[1] . "\nuse App\\Filament\\Traits\\HasRegolamentoAction;\n" . $matches[2];
            }
            return $matches[0];
        },
    ];
    
    $newContent = $content;
    $changed = false;
    
    foreach ($corrections as $pattern => $replacement) {
        if (is_callable($replacement)) {
            $newContent = preg_replace_callback($pattern, $replacement, $newContent, -1, $count);
        } else {
            $newContent = preg_replace($pattern, $replacement, $newContent, -1, $count);
        }
        if ($count > 0) {
            $changed = true;
        }
    }
    
    // Correggi manualmente i problemi più comuni
    $newContent = str_replace('\\n', "\n", $newContent);
    $newContent = str_replace('\\\\n', "\n", $newContent);
    
    // Assicurati che l'array delle azioni sia ben formattato
    $newContent = preg_replace(
        '/(return\s+\[)([^]]*?)(\];)/s',
        function($matches) {
            $actions = $matches[2];
            // Rimuovi virgole duplicate e formatta correttamente
            $actions = preg_replace('/,\s*,/', ',', $actions);
            $actions = rtrim($actions, ",\n ");
            return $matches[1] . $actions . $matches[3];
        },
        $newContent
    );
    
    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        echo "FIXED: " . basename($file) . "\n";
        $fixed++;
    }
}

echo "\nCompletato!\n";
echo "File corretti: $fixed\n";
