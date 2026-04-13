# Documento dei Requisiti

## Introduzione

Questa specifica descrive le attività di refactoring, documentazione e testing dell'applicazione Laravel 12 + Filament 5 che espone elenchi pubblici italiani (ABI, Comuni, RUI, IVASS) tramite un'interfaccia amministrativa. L'obiettivo è migliorare la qualità del codice rimuovendo il dead code, aggiungere documentazione PHPDoc e commenti significativi, e introdurre una suite di test automatizzati (unit e feature) che garantisca la correttezza del comportamento esistente.

## Glossario

- **ABI**: Associazione Bancaria Italiana — elenco ufficiale delle banche italiane con codice identificativo a 5 cifre.
- **Comune**: Ente territoriale italiano; l'elenco ufficiale ISTAT contiene codici, denominazioni e dati geografici.
- **RUI**: Registro Unico degli Intermediari assicurativi e riassicurativi, gestito da IVASS.
- **IVASS**: Istituto per la Vigilanza sulle Assicurazioni.
- **BankMatcherService**: Servizio che abbina nomi di banche liberi al codice ABI ufficiale tramite similarità testuale.
- **RuiCsvImportService**: Servizio che importa i file CSV del RUI dalla directory `public/RUI/` nelle tabelle del database.
- **Dead_Code**: Codice PHP non raggiungibile, non referenziato o usato esclusivamente per scopi di debug temporaneo.
- **PHPDoc**: Standard di documentazione inline per PHP (`/** ... */`) con tag `@param`, `@return`, `@throws`.
- **Pest**: Framework di testing PHP usato nel progetto (v3).
- **Filament**: Framework admin panel per Laravel usato nel progetto (v5).
- **Console_Command**: Classe Artisan che implementa un comando CLI.
- **Resource**: Classe Filament che definisce CRUD per un modello Eloquent.

---

## Requisiti

### Requisito 1: Rimozione del Dead Code

**User Story:** Come sviluppatore, voglio che il codebase sia privo di codice inutilizzato, così da ridurre la complessità cognitiva e il rischio di confusione durante la manutenzione.

#### Criteri di Accettazione

1. THE Codebase SHALL NOT contenere Console_Command classificati esclusivamente come debug (`DebugRuiImport`, `DebugRuiTenRecords`) registrati nel kernel di produzione.
2. THE Codebase SHALL NOT contenere metodi privati non referenziati all'interno della stessa classe (es. `calculateScore` in `BankMatcherService`).
3. THE Codebase SHALL NOT contenere metodi pubblici di debug con output `echo` diretti (es. `debugImportFirst100Rui` in `RuiCsvImportService`).
4. WHEN un file PHP non è referenziato da nessun altro file del progetto né registrato in alcun provider o route, THE Codebase SHALL segnalare tale file come candidato alla rimozione.
5. THE Codebase SHALL NOT contenere variabili dichiarate ma mai lette all'interno dello stesso scope (es. `$isDummy` in `ImportReportingPrincipalsCommand`).
6. WHEN un metodo duplica esattamente la logica di un altro metodo nella stessa classe, THE Codebase SHALL consolidare i due metodi in uno solo.

---

### Requisito 2: Documentazione PHPDoc dei Servizi

**User Story:** Come sviluppatore, voglio che tutti i servizi abbiano documentazione PHPDoc completa, così da capire immediatamente il contratto di ogni metodo senza leggere l'implementazione.

#### Criteri di Accettazione

1. THE BankMatcherService SHALL avere un blocco PHPDoc di classe che descriva lo scopo del servizio.
2. WHEN un metodo pubblico o protetto di BankMatcherService accetta parametri, THE BankMatcherService SHALL documentare ogni parametro con il tag `@param` e il tipo corretto.
3. WHEN un metodo pubblico o protetto di BankMatcherService restituisce un valore, THE BankMatcherService SHALL documentare il tipo di ritorno con il tag `@return`.
4. THE RuiCsvImportService SHALL avere un blocco PHPDoc di classe che descriva lo scopo del servizio e i file CSV attesi.
5. WHEN un metodo di RuiCsvImportService può lanciare un'eccezione, THE RuiCsvImportService SHALL documentare l'eccezione con il tag `@throws`.
6. THE Rui Model SHALL avere PHPDoc su tutti i metodi pubblici incluse le relazioni Eloquent, con descrizione del tipo di relazione e del modello correlato.

---

### Requisito 3: Documentazione PHPDoc dei Console Commands

**User Story:** Come sviluppatore, voglio che ogni Console Command abbia documentazione PHPDoc, così da capire le opzioni disponibili e il comportamento atteso senza eseguire `--help`.

#### Criteri di Accettazione

1. THE ImportRuiData Command SHALL avere un blocco PHPDoc sul metodo `handle` che descriva il flusso di esecuzione e i codici di ritorno.
2. THE MatchBanksCommand SHALL avere un blocco PHPDoc sul metodo `handle` che descriva il flusso di esecuzione.
3. THE PopulateRuiCollaboratoriNames Command SHALL avere PHPDoc che documenti le opzioni `--batch`, `--force` e `--dry-run` con i rispettivi effetti.
4. WHEN un Console_Command accetta argomenti o opzioni, THE Console_Command SHALL documentare ogni opzione con un commento inline nella definizione `$signature`.

---

### Requisito 4: Documentazione PHPDoc dei Modelli Eloquent

**User Story:** Come sviluppatore, voglio che i modelli Eloquent abbiano PHPDoc sulle proprietà e relazioni, così da avere autocompletamento corretto nell'IDE.

#### Criteri di Accettazione

1. THE Abi Model SHALL avere un blocco PHPDoc di classe con tag `@property` per ogni campo in `$fillable`.
2. THE Comune Model SHALL avere un blocco PHPDoc di classe con tag `@property` per ogni campo in `$fillable` e `@property` per gli accessor.
3. THE Rui Model SHALL avere un blocco PHPDoc di classe con tag `@property` per ogni campo in `$fillable` e tag `@property-read` per ogni relazione.
4. WHEN un modello definisce un accessor con `get{Name}Attribute`, THE Model SHALL documentare l'accessor con `@property-read` nel blocco PHPDoc di classe.

---

### Requisito 5: README dell'Applicazione

**User Story:** Come nuovo sviluppatore, voglio un README aggiornato e specifico per questo progetto, così da poter configurare e avviare l'applicazione senza dover leggere il codice sorgente.

#### Criteri di Accettazione

1. THE README SHALL descrivere lo scopo dell'applicazione (consultazione elenchi pubblici italiani: ABI, Comuni, RUI, IVASS).
2. THE README SHALL elencare i prerequisiti di sistema (PHP, Composer, Node, database).
3. THE README SHALL documentare i passi di installazione in ordine sequenziale.
4. THE README SHALL documentare tutti i Console Command disponibili con la loro firma e una descrizione dell'uso.
5. THE README SHALL descrivere la struttura delle directory CSV attese in `public/RUI/` per l'importazione RUI.
6. THE README SHALL documentare come eseguire la suite di test con il comando corretto.

---

### Requisito 6: Unit Test per BankMatcherService

**User Story:** Come sviluppatore, voglio unit test per `BankMatcherService`, così da verificare che la logica di normalizzazione e matching funzioni correttamente con input variabili.

#### Criteri di Accettazione

1. WHEN `normalize` riceve una stringa con stopword bancarie (es. "Banca Progetto S.p.A."), THE BankMatcherService SHALL restituire la stringa senza stopword e senza caratteri non alfanumerici.
2. WHEN `normalize` riceve una stringa vuota, THE BankMatcherService SHALL restituire una stringa vuota.
3. WHEN `findBestAbi` riceve un nome che corrisponde esattamente a una banca nel database, THE BankMatcherService SHALL restituire il codice ABI corretto.
4. WHEN `findBestAbi` riceve un nome che non corrisponde a nessuna banca con score >= 75, THE BankMatcherService SHALL restituire `null`.
5. FOR ALL stringhe di input valide, `normalize(normalize(x))` SHALL essere uguale a `normalize(x)` (proprietà di idempotenza).

---

### Requisito 7: Unit Test per RuiCsvImportService

**User Story:** Come sviluppatore, voglio unit test per `RuiCsvImportService`, così da verificare che la logica di mapping file→tabella e la gestione degli errori funzionino correttamente.

#### Criteri di Accettazione

1. WHEN `getImportClass` riceve un nome file valido (es. `ELENCO_INTERMEDIARI`), THE RuiCsvImportService SHALL restituire il nome della classe di import corrispondente.
2. WHEN `getImportClass` riceve un nome file non riconosciuto, THE RuiCsvImportService SHALL restituire `null`.
3. WHEN `getTableNameFromFileName` riceve un nome file valido, THE RuiCsvImportService SHALL restituire il nome della tabella corrispondente.
4. WHEN `clearAllRuiData` viene chiamato e tutte le tabelle sono vuote, THE RuiCsvImportService SHALL restituire `['success' => true]`.
5. WHEN `getAvailableRuiTables` viene chiamato, THE RuiCsvImportService SHALL restituire un array con esattamente 9 voci, una per ogni tabella RUI.

---

### Requisito 8: Unit Test per il Modello Rui

**User Story:** Come sviluppatore, voglio unit test per il modello `Rui`, così da verificare che le relazioni Eloquent e i metodi di business siano corretti.

#### Criteri di Accettazione

1. WHEN un record `Rui` viene creato con `cognome_nome`, `data_nascita` e `comune_nascita` valorizzati, THE Rui Model SHALL restituire una stringa non vuota da `calculateCodiceFiscale`.
2. WHEN un record `Rui` ha `cognome_nome` vuoto, THE Rui Model SHALL restituire una stringa vuota da `calculateCodiceFiscale`.
3. WHEN un record `Rui` viene salvato con `data_iscrizione` come stringa ISO, THE Rui Model SHALL restituire un'istanza `Carbon` per l'attributo `data_iscrizione`.
4. WHEN un record `Rui` viene salvato con `inoperativo = 1`, THE Rui Model SHALL restituire `true` (booleano) per l'attributo `inoperativo`.

---

### Requisito 9: Feature Test per i Comandi di Importazione RUI

**User Story:** Come sviluppatore, voglio feature test per i comandi Artisan di importazione RUI, così da verificare che il flusso completo di importazione funzioni correttamente in un ambiente di test.

#### Criteri di Accettazione

1. WHEN `rui:import` viene eseguito senza opzioni su un database vuoto, THE ImportRuiData Command SHALL terminare con codice di uscita `0`.
2. WHEN `rui:import --clear --force` viene eseguito, THE ImportRuiData Command SHALL svuotare le tabelle RUI prima dell'importazione.
3. WHEN `rui:import-single rui` viene eseguito con un file CSV non esistente, THE ImportSingleRuiTableCommand SHALL terminare con codice di uscita `1` e mostrare un messaggio di errore.
4. WHEN `rui:import-single --list` viene eseguito, THE ImportSingleRuiTableCommand SHALL mostrare le 9 tabelle disponibili nell'output.
5. WHEN `banks:match-abi` viene eseguito su un database senza record `Principal`, THE MatchBanksCommand SHALL terminare con codice di uscita `0` senza errori.

---

### Requisito 10: Feature Test per le Risorse Filament degli Elenchi Pubblici

**User Story:** Come sviluppatore, voglio feature test per le risorse Filament di ABI, Comuni e RUI, così da verificare che le pagine di listing siano accessibili e funzionanti.

#### Criteri di Accettazione

1. WHEN un utente autenticato accede alla pagina index di `AbiResource`, THE AbiResource SHALL rispondere con HTTP 200.
2. WHEN un utente autenticato accede alla pagina index di `ComuneResource`, THE ComuneResource SHALL rispondere con HTTP 200.
3. WHEN un utente autenticato accede alla pagina index di `RuiResource`, THE RuiResource SHALL rispondere con HTTP 200.
4. WHEN la tabella `abis` contiene almeno un record, THE AbiResource SHALL mostrare il nome della banca nella pagina index.
5. WHEN la tabella `comunes` contiene almeno un record, THE ComuneResource SHALL mostrare la denominazione del comune nella pagina index.
6. IF un utente non autenticato tenta di accedere a qualsiasi Resource Filament, THEN THE Filament Panel SHALL reindirizzare l'utente alla pagina di login.
