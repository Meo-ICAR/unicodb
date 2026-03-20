#!/bin/bash

# Script per copiare i file del plugin da public/vendor/alizharb a vendor
# Uso: ./copia.sh [auto]
# Se passato "auto", esegue solo se ci sono modifiche git

echo "🔄 Inizio copia file plugin alizharb..."

# Verifica se esiste la directory sorgente
if [ ! -d "public/vendor/alizharb" ]; then
    echo "❌ Errore: Directory public/vendor/alizharb non trovata"
    exit 1
fi

# Verifica se esiste la directory di destinazione
if [ ! -d "vendor" ]; then
    echo "❌ Errore: Directory vendor non trovata"
    exit 1
fi

# Controllo se ci sono modifiche git (solo se passato "auto")
if [ "$1" = "auto" ]; then
    # Controlla se ci sono file modificati rispetto all'ultimo commit
    if ! git diff --name-only HEAD~1 HEAD | grep -q "public/vendor/alizharb"; then
        echo "ℹ️ Nessuna modifica trovata in public/vendor/alizharb, salto copia."
        exit 0
    fi
    echo "🔄 Rilevate modifiche in public/vendor/alizharb, procedo con la copia..."
fi



# Copia tutti i file ricorsivamente
echo "📁 Copia dei file da public/vendor/alizharb a vendor/alizharb..."
cp -r public/vendor/alizharb/* vendor/alizharb/

# Verifica se la copia è andata a buon fine
if [ $? -eq 0 ]; then
    echo "✅ Copia completata con successo!"
    echo "📊 Statistiche:"
    echo "   - File copiati: $(find vendor/alizharb -type f | wc -l)"
    echo "   - Directory create: $(find vendor/alizharb -type d | wc -l)"
    echo "   - Spazio occupato: $(du -sh vendor/alizharb | cut -f1)"
else
    echo "❌ Errore durante la copia dei file"
    exit 1
fi

# Pulizia cache se necessario
echo "🧹 Pulizia cache autoloader..."
composer dump-autoload --quiet

echo "🎉 Plugin alizharb aggiornato con successo!"
echo "📍 File copiati in: vendor/alizharb/"

# Se eseguito automaticamente, notifica il completamento
if [ "$1" = "auto" ]; then
    echo "🔔 Hook git completato: plugin pronto per l'uso!"
fi
