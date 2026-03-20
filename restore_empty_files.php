<?php

// Script per identificare e ripristinare i file List pages vuoti o corrotti

$basePath = __DIR__ . '/app/Filament/Resources';

// Trova tutti i file List*.php
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
foreach ($iterator as $file) {
    if ($file->isFile() && preg_match('/List.*\.php$/', $file->getFilename())) {
        $files[] = $file->getPathname();
    }
}

echo "Verifica di " . count($files) . " file List pages\n\n";

$restored = 0;
$checked = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $checked++;
    
    // Verifica se il file è vuoto, corrotto o troppo corto
    if (empty($content) || strlen($content) < 50 || !strpos($content, 'class') || !strpos($content, 'extends ListRecords')) {
        
        // Estrai il nome del modello dal percorso
        $pathParts = explode('/', str_replace($basePath . '/', '', $file));
        $resourceName = $pathParts[0]; // es. 'Abis', 'Agents', etc.
        $className = basename($file, '.php'); // es. 'ListAbis'
        
        // Determina il namespace e il resource name
        $modelName = str_replace('List', '', $className); // es. 'Abis'
        $resourceClass = "App\\Filament\\Resources\\{$resourceName}\\{$modelName}Resource";
        
        // Crea il contenuto template
        $template = "<?php\n\nnamespace App\\Filament\\Resources\\{$resourceName}\\Pages;\n\nuse App\\Filament\\Resources\\{$resourceName}\\{$modelName}Resource;\nuse App\\Filament\\Traits\\HasRegolamentoAction;\nuse Filament\\Actions\\CreateAction;\nuse Filament\\Resources\\Pages\\ListRecords;\n\nclass {$className} extends ListRecords\n{\n    use HasRegolamentoAction;\n    \n    protected static string \$resource = {$modelName}Resource::class;\n\n    protected function getHeaderActions(): array\n    {\n        return [\n            CreateAction::make(),\n            \$this->getRegolamentoAction(),\n        ];\n    }\n}\n";
        
        file_put_contents($file, $template);
        echo "RESTORED: " . basename($file) . " (Resource: {$resourceName})\n";
        $restored++;
    } else {
        echo "OK: " . basename($file) . "\n";
    }
}

echo "\nVerifica completata!\n";
echo "File controllati: $checked\n";
echo "File ripristinati: $restored\n";
