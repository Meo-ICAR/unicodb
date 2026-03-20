# Agent VAT Data Matching

Questo comando permette di fare il matching tra gli agenti e le fatture di acquisto per aggiornare i dati VAT degli agenti.

## Comando

```bash
php artisan agents:match-vat-data [options]
```

## Opzioni

- `--company=` : Filtra per company ID specifico
- `--dry-run` : Mostra i match senza aggiornare (utile per test)
- `--threshold=` : Soglia di similarità in percentuale (default: 80)

## Esempi di utilizzo

### 1. Dry run per testare i match (soglia 70%)
```bash
php artisan agents:match-vat-data --dry-run --threshold=70
```

### 2. Esecuzione effettiva con soglia 70%
```bash
php artisan agents:match-vat-data --threshold=70
```

### 3. Solo per una company specifica
```bash
php artisan agents:match-vat-data --company=9f5b0a17-f03d-401e-9bf3-625768ee58b2
```

### 4. Soglia più bassa per più match
```bash
php artisan agents:match-vat-data --threshold=50
```

## Algoritmo di Similarità

Il sistema usa un algoritmo combinato:

1. **Normalizzazione delle stringhe**:
   - Conversione in minuscolo
   - Trim degli spazi
   - Rimozione spazi multipli

2. **Calcolo similarità Levenshtein**:
   - Distanza di Levenshtein tra le stringhe
   - Convertita in percentuale di similarità

3. **Bonus per substring**:
   - Se una stringa è contenuta nell'altra, bonus del 20%

## Logica di Aggiornamento

Il sistema aggiorna solo se:
- `agent->vat_number` è vuoto o diverso da `purchase_invoice->vat_number`
- `agent->vat_name` è vuoto o diverso da `purchase_invoice->supplier`

## Risultati

Il comando mostra:
- Numero totale di agenti processati
- Agenti con match trovati
- Agenti aggiornati
- Agenti senza match
- Top 10 match con score di similarità

## Logging

Tutti gli aggiornamenti sono loggati in `storage/logs/laravel.log` per audit trail.
