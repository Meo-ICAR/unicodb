# Piano di Implementazione: app-cleanup-docs-tests

## Overview

Attività di cleanup, documentazione e testing dell'applicazione Laravel + Filament per la consultazione degli elenchi pubblici italiani (ABI, Comuni, RUI, IVASS). I task seguono un ordine incrementale: prima si rimuove il dead code, poi si aggiunge la documentazione PHPDoc, poi si aggiorna il README, infine si scrivono i test.

## Tasks

- [x] 1. Rimozione del dead code
  - [x] 1.1 Eliminare i comandi debug `DebugRuiImport` e `DebugRuiTenRecords`
    - Cancellare `app/Console/Commands/DebugRuiImport.php`
    - Cancellare `app/Console/Commands/DebugRuiTenRecords.php`
    - Verificare che non siano registrati in `app/Console/Kernel.php` o in alcun provider; rimuovere eventuali riferimenti
    - _Requirements: 1.1_

  - [x] 1.2 Rimuovere i metodi dead code in `RuiCsvImportService`
    - Eliminare il metodo pubblico `debugImportFirst100Rui()` (contiene `echo` diretti)
    - Eliminare i metodi `debugImportTenRecords()` e `debugImportRuiOnly()` (chiamati solo dai comandi debug rimossi al task 1.1)
    - _Requirements: 1.3_

  - [x] 1.3 Rimuovere il metodo privato inutilizzato `calculateScore()` da `BankMatcherService`
    - Verificare che `calculateScore` non sia referenziato internamente prima della rimozione
    - _Requirements: 1.2_

  - [x] 1.4 Rimuovere la variabile `$isDummy` non letta in `ImportReportingPrincipalsCommand`
    - Eliminare la riga `$isDummy = false;` nel metodo `processRow`
    - _Requirements: 1.5_

- [x] 2. Checkpoint — Verificare che l'applicazione non abbia errori di sintassi o riferimenti rotti
  - Eseguire `php artisan list` per confermare che i comandi rimasti si caricano correttamente
  - Assicurarsi che tutti i test esistenti passino, chiedere all'utente se sorgono dubbi

- [x] 3. Documentazione PHPDoc dei servizi
  - [x] 3.1 Aggiungere PHPDoc completo a `BankMatcherService`
    - Aggiungere blocco PHPDoc di classe che descriva lo scopo (matching fuzzy nome banca → codice ABI)
    - Documentare `normalize(string $name): string` con `@param` e `@return`
    - Documentare `findBestAbi(string $inputName): ?string` con `@param` e `@return`
    - _Requirements: 2.1, 2.2, 2.3_

  - [x] 3.2 Aggiungere PHPDoc completo a `RuiCsvImportService`
    - Aggiungere blocco PHPDoc di classe che descriva lo scopo e i file CSV attesi in `public/RUI/`
    - Completare `@param` e `@return` mancanti sui metodi pubblici
    - Aggiungere `@throws \Exception` sui metodi che possono lanciare eccezioni
    - _Requirements: 2.4, 2.5_

- [x] 4. Documentazione PHPDoc dei modelli Eloquent
  - [x] 4.1 Aggiungere PHPDoc al modello `Abi`
    - Aggiungere blocco PHPDoc di classe con `@property int $id`, `@property string $name`, `@property string $code`, `@property string $description`, `@property \Carbon\Carbon $created_at`, `@property \Carbon\Carbon $updated_at`
    - _Requirements: 4.1_

  - [x] 4.2 Aggiungere PHPDoc al modello `Comune`
    - Aggiungere blocco PHPDoc di classe con `@property` per ogni campo in `$fillable`
    - Aggiungere `@property-read string $display_name` per l'accessor `getDisplayNameAttribute`
    - _Requirements: 4.2, 4.4_

  - [x] 4.3 Aggiungere PHPDoc al modello `Rui`
    - Aggiungere blocco PHPDoc di classe con `@property` per ogni campo in `$fillable` e `@property bool $inoperativo`
    - Aggiungere `@property-read` per ogni relazione: `ruiSection`, `websites`, `carichePg`, `sedi`, `collaboratori`, `collaboratoriILiv`, `collaboratoriIILiv`
    - Documentare il metodo pubblico `calculateCodiceFiscale` con `@return string`
    - _Requirements: 2.6, 4.3, 4.4_

- [x] 5. Documentazione PHPDoc dei Console Commands
  - [x] 5.1 Aggiungere PHPDoc a `ImportRuiData::handle()`
    - Descrivere il flusso di esecuzione (clear opzionale → import CSV → display risultati)
    - Documentare i codici di ritorno: `0` = successo, `1` = errore
    - _Requirements: 3.1_

  - [x] 5.2 Aggiungere PHPDoc a `MatchBanksCommand::handle()`
    - Descrivere il flusso di esecuzione (chunk Principal → findBestAbi → aggiorna ABI)
    - _Requirements: 3.2_

  - [x] 5.3 Aggiungere PHPDoc a `PopulateRuiCollaboratoriNames::handle()`
    - Documentare gli effetti delle opzioni `--batch`, `--force` e `--dry-run`
    - _Requirements: 3.3, 3.4_

- [x] 6. Aggiornamento README
  - Riscrivere `README.md` sostituendo il contenuto generico di Laravel con documentazione specifica del progetto
  - Includere: scopo dell'applicazione (ABI, Comuni, RUI, IVASS), prerequisiti di sistema (PHP, Composer, Node, database), passi di installazione in ordine sequenziale
  - Documentare tutti i Console Command disponibili con firma e descrizione d'uso
  - Descrivere la struttura delle directory CSV attese in `public/RUI/`
  - Documentare come eseguire la suite di test (`php artisan test` o `./vendor/bin/pest`)
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6_

- [x] 7. Unit test per `BankMatcherService`
  - [x] 7.1 Creare `tests/Unit/BankMatcherServiceTest.php` con test di esempio
    - Test: `normalize` con stopword bancarie (es. "Banca Progetto S.p.A.") rimuove le stopword
    - Test: `normalize` con stringa vuota restituisce stringa vuota
    - Test: `findBestAbi` con match esatto restituisce il codice ABI corretto (mock `Abi::all()`)
    - Test: `findBestAbi` con nome sconosciuto restituisce `null` (mock `Abi::all()`)
    - _Requirements: 6.1, 6.2, 6.3, 6.4_

  - [x] 7.2 Scrivere property test per idempotenza di `normalize` (Property 1)
    - **Property 1: Idempotenza di normalize**
    - Eseguire 100 iterazioni con stringhe casuali generate da `fake()->words(rand(1,5), true)`
    - Asserire che `normalize(normalize($s)) === normalize($s)` per ogni input
    - Annotare: `// Feature: app-cleanup-docs-tests, Property 1: Idempotenza di normalize`
    - **Validates: Requirements 6.5**

  - [x] 7.3 Scrivere property test per output pulito di `normalize` (Property 2)
    - **Property 2: Output di normalize privo di stopword e caratteri non alfanumerici**
    - Eseguire 100 iterazioni iniettando stopword bancarie casuali (banca, spa, s.p.a., srl, credito) in stringhe generate da Faker
    - Asserire che l'output non contenga stopword e corrisponda a `[a-z0-9 ]*`
    - Annotare: `// Feature: app-cleanup-docs-tests, Property 2: Output pulito di normalize`
    - **Validates: Requirements 6.1**

- [x] 8. Checkpoint — Eseguire i test unitari di `BankMatcherService`
  - Eseguire `./vendor/bin/pest tests/Unit/BankMatcherServiceTest.php` e verificare che tutti i test passino
  - Chiedere all'utente se sorgono dubbi

- [x] 9. Unit test per `RuiCsvImportService`
  - [x] 9.1 Creare `tests/Unit/RuiCsvImportServiceTest.php` con test di esempio
    - Test: `getImportClass` con ciascuno dei 9 nomi file validi restituisce la classe corretta (9 asserzioni)
    - Test: `getTableNameFromFileName` con ciascuno dei 9 nomi file validi restituisce la tabella corretta (9 asserzioni)
    - Test: `clearAllRuiData` su tabelle vuote restituisce `['success' => true]` (usa `RefreshDatabase`)
    - Test: `getAvailableRuiTables` restituisce esattamente 9 voci
    - _Requirements: 7.1, 7.3, 7.4, 7.5_

  - [x] 9.2 Scrivere property test per `getImportClass` con nomi non riconosciuti (Property 3)
    - **Property 3: getImportClass restituisce null per nomi file non riconosciuti**
    - Eseguire 100 iterazioni con stringhe casuali che non corrispondono ai 9 nomi RUI noti
    - Asserire che `getImportClass($randomName) === null`
    - Annotare: `// Feature: app-cleanup-docs-tests, Property 3: getImportClass null per nomi non riconosciuti`
    - **Validates: Requirements 7.2**

- [x] 10. Unit test per il modello `Rui`
  - [x] 10.1 Creare `tests/Unit/RuiModelTest.php` con test di esempio
    - Usare `RefreshDatabase` con SQLite in-memory e factory `Rui`
    - Test: `calculateCodiceFiscale` con `cognome_nome` vuoto restituisce stringa vuota
    - Test: `data_iscrizione` come stringa ISO viene castata a istanza `Carbon`
    - Test: `inoperativo = 1` viene castato a `true` booleano
    - _Requirements: 8.2, 8.3, 8.4_

  - [x] 10.2 Scrivere property test per `calculateCodiceFiscale` con dati completi (Property 4)
    - **Property 4: calculateCodiceFiscale restituisce stringa non vuota per dati completi**
    - Eseguire 100 iterazioni creando istanze `Rui` con `cognome_nome`, `data_nascita` e `comune_nascita` generati da Faker (non vuoti)
    - Asserire che `calculateCodiceFiscale()` restituisca una stringa non vuota
    - Annotare: `// Feature: app-cleanup-docs-tests, Property 4: calculateCodiceFiscale non vuoto per dati completi`
    - **Validates: Requirements 8.1**

- [ ] 11. Checkpoint — Eseguire tutti i test unitari
  - Eseguire `./vendor/bin/pest tests/Unit/` e verificare che tutti i test passino
  - Chiedere all'utente se sorgono dubbi

- [ ] 12. Feature test per i comandi Artisan RUI
  - Creare `tests/Feature/RuiCommandsTest.php` con `RefreshDatabase`
  - Test: `rui:import` su DB vuoto termina con exit code `0`
  - Test: `rui:import --clear --force` svuota le tabelle RUI prima dell'importazione
  - Test: `rui:import-single rui` con file CSV non esistente termina con exit code `1` e mostra messaggio di errore
  - Test: `rui:import-single --list` mostra le 9 tabelle disponibili nell'output
  - Test: `banks:match-abi` su DB senza record `Principal` termina con exit code `0`
  - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

- [ ] 13. Feature test per le risorse Filament degli elenchi pubblici
  - [ ] 13.1 Creare `tests/Feature/FilamentPublicListsTest.php` con `RefreshDatabase`
    - Creare un utente autenticato tramite factory e `actingAs`
    - Test: utente autenticato accede a `AbiResource` index → HTTP 200
    - Test: utente autenticato accede a `ComuneResource` index → HTTP 200
    - Test: utente autenticato accede a `RuiResource` index → HTTP 200
    - Test: `AbiResource` mostra il nome della banca se la tabella `abis` contiene almeno un record
    - Test: `ComuneResource` mostra la denominazione del comune se la tabella `comunes` contiene almeno un record
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

  - [ ] 13.2 Scrivere property test per redirect al login degli utenti non autenticati (Property 5)
    - **Property 5: Redirect al login per utenti non autenticati**
    - Iterare su tutti gli URL index delle risorse Filament registrate (ABI, Comune, Rui e altre)
    - Per ogni URL, asserire che una richiesta GET senza autenticazione restituisca HTTP 302 verso la pagina di login
    - Annotare: `// Feature: app-cleanup-docs-tests, Property 5: Redirect al login per utenti non autenticati`
    - **Validates: Requirements 10.6**

- [ ] 14. Checkpoint finale — Eseguire l'intera suite di test
  - Eseguire `./vendor/bin/pest` e verificare che tutti i test passino
  - Chiedere all'utente se sorgono dubbi prima di considerare la spec completata

## Note

- I task contrassegnati con `*` sono opzionali e possono essere saltati per un MVP più rapido
- Ogni task referenzia i requisiti specifici per la tracciabilità
- I checkpoint garantiscono la validazione incrementale
- I property test validano proprietà universali di correttezza (idempotenza, output pulito, comportamento su input arbitrari)
- I test unitari validano esempi specifici ed edge case
- I test di `BankMatcherService` che chiamano `findBestAbi` devono mockare `Abi::all()` per evitare dipendenze dal database
- I feature test Filament usano `actingAs` con un utente creato tramite factory; il panel non usa tenant (`$isScopedToTenant = false`)
