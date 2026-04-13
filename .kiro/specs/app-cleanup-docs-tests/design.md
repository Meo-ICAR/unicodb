# Design Document — app-cleanup-docs-tests

## Overview

Questo documento descrive il design tecnico per le attività di cleanup, documentazione e testing dell'applicazione Laravel 12 + Filament 5 che espone elenchi pubblici italiani (ABI, Comuni, RUI, IVASS).

L'intervento si articola in cinque aree:

1. **Rimozione del dead code** — eliminazione di comandi debug, metodi privati inutilizzati, variabili non lette e metodi con `echo` diretti.
2. **Documentazione PHPDoc** — aggiunta di blocchi `/** ... */` completi su servizi, comandi Artisan e modelli Eloquent.
3. **README aggiornato** — sostituzione del README generico di Laravel con uno specifico per il progetto.
4. **Unit test** — copertura di `BankMatcherService`, `RuiCsvImportService` e del modello `Rui`.
5. **Feature/Integration test** — verifica dei comandi Artisan di importazione e delle risorse Filament degli elenchi pubblici.

L'obiettivo non è aggiungere funzionalità, ma aumentare la qualità, la leggibilità e la verificabilità del codice esistente.

---

## Architecture

L'applicazione segue l'architettura standard Laravel con Filament come admin panel. I componenti rilevanti per questa spec sono:

```
app/
├── Console/Commands/          # Comandi Artisan (importazione, matching, debug)
├── Services/
│   ├── BankMatcherService.php # Logica di matching nome banca → codice ABI
│   └── RuiCsvImportService.php# Logica di importazione CSV RUI
├── Models/
│   ├── Abi.php                # Modello tabella ABI
│   ├── Comune.php             # Modello tabella Comuni
│   └── Rui.php                # Modello tabella RUI intermediari
└── Filament/Resources/
    ├── Abis/                  # Resource Filament per ABI
    ├── Comunes/               # Resource Filament per Comuni
    └── Ruis/                  # Resource Filament per RUI

tests/
├── Unit/                      # Test unitari (servizi, modelli)
└── Feature/                   # Test di integrazione (comandi, risorse Filament)
```

Il flusso di dipendenze è unidirezionale: i comandi Artisan dipendono dai servizi, i servizi dipendono dai modelli Eloquent, le risorse Filament dipendono dai modelli.

---

## Components and Interfaces

### Dead Code da rimuovere

| File | Elemento | Motivo rimozione |
|------|----------|-----------------|
| `app/Console/Commands/DebugRuiImport.php` | Intero file | Comando esclusivamente debug, non utile in produzione |
| `app/Console/Commands/DebugRuiTenRecords.php` | Intero file | Comando esclusivamente debug, non utile in produzione |
| `app/Services/BankMatcherService.php` | Metodo privato `calculateScore()` | Non referenziato internamente; la logica è inline in `findBestAbi` |
| `app/Services/RuiCsvImportService.php` | Metodo pubblico `debugImportFirst100Rui()` | Contiene `echo` diretti, usato solo per debug manuale |
| `app/Console/Commands/ImportReportingPrincipalsCommand.php` | Variabile `$isDummy` | Dichiarata ma mai letta nello scope |

**Nota**: i metodi `debugImportTenRecords()` e `debugImportRuiOnly()` in `RuiCsvImportService` sono chiamati dai comandi debug che vengono rimossi; una volta rimossi i comandi, anche questi metodi diventano dead code e vanno rimossi.

### PHPDoc da aggiungere

**BankMatcherService**
- Blocco di classe: descrive lo scopo (matching fuzzy nome banca → ABI)
- `normalize(string $name): string` — `@param`, `@return`
- `findBestAbi(string $inputName): ?string` — `@param`, `@return`

**RuiCsvImportService**
- Blocco di classe: descrive lo scopo e i file CSV attesi in `public/RUI/`
- Metodi che possono lanciare eccezioni: `@throws \Exception`
- Metodi pubblici già parzialmente documentati: completare con `@param` e `@return` mancanti

**Modello Rui**
- Blocco di classe con `@property` per ogni campo `$fillable`
- `@property-read` per ogni relazione (`ruiSection`, `websites`, `carichePg`, `sedi`, `collaboratori`, `collaboratoriILiv`, `collaboratoriIILiv`)
- PHPDoc su tutti i metodi pubblici (`calculateCodiceFiscale`)

**Modello Abi**
- Blocco di classe con `@property` per `name`, `code`, `description`

**Modello Comune**
- Blocco di classe con `@property` per tutti i campi `$fillable`
- `@property-read string $display_name` per l'accessor `getDisplayNameAttribute`

**Console Commands**
- `ImportRuiData::handle()` — flusso di esecuzione, codici di ritorno 0/1
- `MatchBanksCommand::handle()` — flusso di esecuzione
- `PopulateRuiCollaboratoriNames::handle()` — effetti di `--batch`, `--force`, `--dry-run`

---

## Data Models

### Modello Abi

```php
/**
 * Rappresenta una banca dell'elenco ufficiale ABI.
 *
 * @property int    $id
 * @property string $name         Nome ufficiale della banca
 * @property string $code         Codice ABI a 5 cifre
 * @property string $description  Descrizione aggiuntiva
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Abi extends Model { ... }
```

### Modello Comune

```php
/**
 * Rappresenta un comune italiano dall'elenco ufficiale ISTAT.
 *
 * @property int    $id
 * @property string $codice_comune_alfanumerico
 * @property string $denominazione
 * @property string $denominazione_italiano
 * @property string $denominazione_altra_lingua
 * @property string $denominazione_regione
 * @property string $sigla_automobilistica
 * @property bool   $capoluogo_provincia
 * ... (tutti i campi $fillable)
 * @property-read string $display_name  Denominazione preferita (italiano o default)
 */
class Comune extends Model { ... }
```

### Modello Rui

```php
/**
 * Rappresenta un intermediario assicurativo iscritto al RUI (IVASS).
 *
 * @property int    $id
 * @property string $numero_iscrizione_rui
 * @property string $cognome_nome
 * @property string $ragione_sociale
 * @property \Carbon\Carbon $data_iscrizione
 * @property \Carbon\Carbon $data_nascita
 * @property bool   $inoperativo
 * ... (tutti i campi $fillable)
 *
 * @property-read \App\Models\RuiSection        $ruiSection
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\RuiWebsite>       $websites
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\RuiCariche>       $carichePg
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\RuiSedi>          $sedi
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\RuiCollaboratori> $collaboratori
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\RuiCollaboratori> $collaboratoriILiv
 * @property-read \Illuminate\Database\Eloquent\Collection<\App\Models\RuiCollaboratori> $collaboratoriIILiv
 */
class Rui extends Model { ... }
```

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

Le proprietà di correttezza si concentrano sulla logica pura dei servizi, dove l'input varia significativamente e 100+ iterazioni rivelano edge case non coperti da esempi singoli.

### Property 1: Idempotenza di normalize

*For any* stringa di input valida `$s`, applicare `normalize` due volte deve produrre lo stesso risultato di applicarla una volta: `normalize(normalize($s)) === normalize($s)`.

**Validates: Requirements 6.5**

### Property 2: Output di normalize privo di stopword e caratteri non alfanumerici

*For any* stringa di input contenente stopword bancarie (es. "banca", "spa", "s.p.a.", "srl", "credito") e/o caratteri non alfanumerici, `normalize` deve restituire una stringa che non contiene nessuna di quelle stopword e non contiene caratteri al di fuori di `[a-z0-9]`.

**Validates: Requirements 6.1**

### Property 3: getImportClass restituisce null per nomi file non riconosciuti

*For any* stringa che non corrisponde a nessuno dei 9 nomi file RUI noti, `getImportClass` deve restituire `null`.

**Validates: Requirements 7.2**

### Property 4: calculateCodiceFiscale restituisce stringa non vuota per dati completi

*For any* istanza `Rui` con `cognome_nome`, `data_nascita` e `comune_nascita` non vuoti, `calculateCodiceFiscale` deve restituire una stringa non vuota.

**Validates: Requirements 8.1**

### Property 5: Redirect al login per utenti non autenticati

*For any* URL di una risorsa Filament registrata nel panel, una richiesta GET senza autenticazione deve risultare in un redirect (HTTP 302) verso la pagina di login.

**Validates: Requirements 10.6**

---

**Property Reflection:**

- Property 1 (idempotenza) e Property 2 (output pulito) sono distinte: la prima verifica la stabilità della funzione, la seconda verifica il contenuto dell'output. Non sono ridondanti.
- Property 3 è l'unica proprietà universale per `getImportClass`; i casi positivi (file noti) sono meglio coperti da esempi.
- Property 4 è distinta dall'edge case 8.2 (stringa vuota): la property verifica il caso positivo su input variabili, l'edge case verifica il caso negativo su input specifico.
- Property 5 è genuinamente universale: vale per tutte le risorse, non solo per ABI/Comune/Rui.

---

## Error Handling

### Rimozione dead code

- La rimozione dei comandi debug non richiede gestione errori aggiuntiva; i comandi non sono referenziati da altri componenti.
- Prima della rimozione, verificare che nessun test esistente li referenzi.

### PHPDoc

- L'aggiunta di PHPDoc è non-breaking: non modifica il comportamento runtime.
- I tag `@throws` su `RuiCsvImportService` documentano eccezioni già lanciate implicitamente.

### Test

- I test unitari di `BankMatcherService` che chiamano `findBestAbi` devono mockare `Abi::all()` per evitare dipendenze dal database.
- I feature test dei comandi Artisan usano `RefreshDatabase` per garantire isolamento.
- I feature test delle risorse Filament usano `RefreshDatabase` e creano un utente autenticato tramite `actingAs`.
- Il panel Filament in test non usa tenant (le risorse ABI/Comune/RUI hanno `$isScopedToTenant = false`).

---

## Testing Strategy

### Approccio duale

La suite di test combina:
- **Unit test** (Pest, `tests/Unit/`) — logica pura di servizi e modelli, senza database reale
- **Feature/Integration test** (Pest, `tests/Feature/`) — comandi Artisan e risorse Filament con database SQLite in-memory

### Libreria per Property-Based Testing

Il progetto usa **Pest v3** con il plugin `pestphp/pest-plugin-laravel`. Per i property-based test si usa **[eris/eris](https://github.com/giorgiosironi/eris)** oppure, preferibilmente, si implementano i generatori direttamente con Faker (già presente come dipendenza dev) in loop da 100+ iterazioni, dato che le proprietà da testare sono su funzioni pure senza dipendenze esterne costose.

**Alternativa consigliata**: usare un semplice helper `forAll(int $n, callable $generator, callable $assertion)` che esegue `$n` iterazioni con input generati da Faker, senza dipendenze aggiuntive.

### Unit Test — BankMatcherService (`tests/Unit/BankMatcherServiceTest.php`)

| Test | Tipo | Requisito |
|------|------|-----------|
| `normalize` con stopword bancarie rimuove le stopword | PROPERTY (100 iter.) | 6.1 |
| `normalize` è idempotente | PROPERTY (100 iter.) | 6.5 |
| `normalize` con stringa vuota restituisce stringa vuota | EDGE_CASE | 6.2 |
| `findBestAbi` con match esatto restituisce ABI corretto | EXAMPLE (mock DB) | 6.3 |
| `findBestAbi` con nome sconosciuto restituisce null | EXAMPLE (mock DB) | 6.4 |

### Unit Test — RuiCsvImportService (`tests/Unit/RuiCsvImportServiceTest.php`)

| Test | Tipo | Requisito |
|------|------|-----------|
| `getImportClass` con nome file valido restituisce classe corretta | EXAMPLE (9 casi) | 7.1 |
| `getImportClass` con nome file non riconosciuto restituisce null | PROPERTY (100 iter.) | 7.2 |
| `getTableNameFromFileName` con nome file valido restituisce tabella corretta | EXAMPLE (9 casi) | 7.3 |
| `clearAllRuiData` su tabelle vuote restituisce `['success' => true]` | EXAMPLE | 7.4 |
| `getAvailableRuiTables` restituisce esattamente 9 voci | EXAMPLE | 7.5 |

### Unit Test — Modello Rui (`tests/Unit/RuiModelTest.php`)

| Test | Tipo | Requisito |
|------|------|-----------|
| `calculateCodiceFiscale` con dati completi restituisce stringa non vuota | PROPERTY (100 iter.) | 8.1 |
| `calculateCodiceFiscale` con `cognome_nome` vuoto restituisce stringa vuota | EDGE_CASE | 8.2 |
| `data_iscrizione` come stringa ISO viene castata a Carbon | EXAMPLE | 8.3 |
| `inoperativo = 1` viene castato a `true` booleano | EXAMPLE | 8.4 |

### Feature Test — Comandi Artisan (`tests/Feature/RuiCommandsTest.php`)

| Test | Tipo | Requisito |
|------|------|-----------|
| `rui:import` su DB vuoto termina con exit code 0 | INTEGRATION | 9.1 |
| `rui:import --clear --force` svuota le tabelle RUI | INTEGRATION | 9.2 |
| `rui:import-single rui` con CSV mancante termina con exit code 1 | INTEGRATION | 9.3 |
| `rui:import-single --list` mostra 9 tabelle nell'output | INTEGRATION | 9.4 |
| `banks:match-abi` su DB senza Principal termina con exit code 0 | INTEGRATION | 9.5 |

### Feature Test — Risorse Filament (`tests/Feature/FilamentPublicListsTest.php`)

| Test | Tipo | Requisito |
|------|------|-----------|
| Utente autenticato accede a AbiResource index → HTTP 200 | INTEGRATION | 10.1 |
| Utente autenticato accede a ComuneResource index → HTTP 200 | INTEGRATION | 10.2 |
| Utente autenticato accede a RuiResource index → HTTP 200 | INTEGRATION | 10.3 |
| AbiResource mostra nome banca se tabella non vuota | INTEGRATION | 10.4 |
| ComuneResource mostra denominazione comune se tabella non vuota | INTEGRATION | 10.5 |
| Utente non autenticato viene reindirizzato al login | PROPERTY (tutte le risorse) | 10.6 |

### Configurazione property test

Ogni property test deve:
- Eseguire **minimo 100 iterazioni**
- Essere annotato con un commento: `// Feature: app-cleanup-docs-tests, Property N: <testo>`
- Usare `fake()` di Laravel (Faker) per generare input casuali

### Note sull'isolamento

- I test unitari di `BankMatcherService` e `RuiCsvImportService` usano Mockery per isolare le dipendenze Eloquent.
- I test del modello `Rui` usano `RefreshDatabase` con SQLite in-memory e factory.
- I feature test dei comandi usano `RefreshDatabase`; i CSV non vengono letti da disco (il test verifica il comportamento del comando, non l'importazione reale).
- I feature test Filament usano `RefreshDatabase`, creano un `User` con factory e chiamano `actingAs`.
