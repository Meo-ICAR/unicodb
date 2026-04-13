# Pannello Amministrativo — Elenchi Pubblici Italiani

Applicazione Laravel 12 + Filament 5 per la consultazione e gestione degli elenchi pubblici italiani:

- **ABI** — Associazione Bancaria Italiana (codici banca a 5 cifre)
- **Comuni** — Elenco ufficiale ISTAT dei comuni italiani con codici e dati geografici
- **RUI** — Registro Unico degli Intermediari assicurativi e riassicurativi (gestito da IVASS)
- **IVASS** — Istituto per la Vigilanza sulle Assicurazioni

---

## Prerequisiti di sistema

- PHP >= 8.2 (con estensioni: `pdo`, `mbstring`, `xml`, `curl`)
- Composer
- Node.js + npm
- MySQL o MariaDB

---

## Installazione

Seguire i passi nell'ordine indicato:

```bash
# 1. Clonare il repository
git clone <repository-url>
cd <project-folder>

# 2. Installare le dipendenze PHP
composer install

# 3. Installare le dipendenze JavaScript
npm install

# 4. Configurare l'ambiente
cp .env.example .env
# Modificare .env con le credenziali del database e le altre variabili necessarie

# 5. Generare la chiave applicativa
php artisan key:generate

# 6. Eseguire le migrazioni del database
php artisan migrate

# 7. Compilare gli asset frontend
npm run build
```

---

## Console Commands disponibili

### Importazione RUI

**`rui:import`** — Importa tutti i file CSV del RUI dalla directory `public/RUI/`

```bash
php artisan rui:import
php artisan rui:import --clear          # Svuota i dati esistenti prima dell'import
php artisan rui:import --clear --force  # Svuota senza chiedere conferma
php artisan rui:import --stats          # Mostra statistiche prima e dopo l'import
```

---

**`rui:import-single`** — Importa una singola tabella RUI dal file CSV corrispondente

```bash
php artisan rui:import-single {table}
php artisan rui:import-single rui                    # Importa la tabella principale
php artisan rui:import-single rui --clear            # Svuota prima di importare
php artisan rui:import-single ELENCO_INTERMEDIARI    # Importa per nome file
php artisan rui:import-single --list                 # Elenca le 9 tabelle disponibili
```

---

**`rui:populate-collaboratori-names`** — Popola i campi nome in `rui_collaboratori` tramite join con la tabella `rui`

```bash
php artisan rui:populate-collaboratori-names
php artisan rui:populate-collaboratori-names --batch=500   # Dimensione batch (default: 1000)
php artisan rui:populate-collaboratori-names --force       # Aggiorna anche i record già popolati
php artisan rui:populate-collaboratori-names --dry-run     # Mostra cosa verrebbe aggiornato senza modificare
```

---

**`rui:sync-principals-from-collaboratori`** — Sincronizza i principal dai dati `rui_collaboratori` in base ai numeri di iscrizione RUI

```bash
php artisan rui:sync-principals-from-collaboratori
php artisan rui:sync-principals-from-collaboratori --company-id=1  # Solo una company specifica
php artisan rui:sync-principals-from-collaboratori --batch=500     # Dimensione batch (default: 1000)
php artisan rui:sync-principals-from-collaboratori --dry-run       # Simulazione senza modifiche
php artisan rui:sync-principals-from-collaboratori --force         # Forza aggiornamento anche se i nomi coincidono
```

---

### Matching ABI

**`banks:match-abi`** — Abbina i nomi banche dei Principal con la tabella ufficiale ABI tramite similarità testuale

```bash
php artisan banks:match-abi
php artisan banks:match-abi --force   # Sovrascrive anche gli ABI già presenti
```

---

### Importazione Principal

**`principals:import-reporting`** — Importa i principal mancanti dal file CSV di segnalazione con `is_reported=true`

```bash
php artisan principals:import-reporting
php artisan principals:import-reporting --file="percorso/al/file.csv"  # File CSV personalizzato
php artisan principals:import-reporting --dry-run                       # Simulazione senza import
```

---

### OAM

**`oam:import`** — Sincronizza i dati `practice_oams` per una company con filtro per data

```bash
php artisan oam:import
php artisan oam:import --company-id=1                    # Company specifica
php artisan oam:import --start-date=2024-01-01           # Data di inizio
php artisan oam:import --end-date=2024-06-30             # Data di fine
php artisan oam:import --stats                           # Solo statistiche, senza sincronizzazione
```

---

### AUI

**`aui:consolidamento`** — Consolida i log AUI in record ufficiali prima della scadenza dei 30 giorni

```bash
php artisan aui:consolidamento
```

---

## Struttura directory CSV per l'importazione RUI

I file CSV del RUI devono essere collocati nella directory `public/RUI/` con i seguenti nomi esatti:

```
public/RUI/
├── ELENCO_INTERMEDIARI.csv              # Intermediari assicurativi (tabella principale rui)
├── ELENCO_SEDI.csv                      # Sedi degli intermediari
├── ELENCO_MANDATI.csv                   # Mandati degli intermediari
├── ELENCO_CARICHE.csv                   # Cariche delle persone giuridiche
├── ELENCO_COLLABORATORI.csv             # Collaboratori degli intermediari
├── ELENCO_COLLABACCESSORI.csv           # Collaboratori accessori
├── ELENCO_AG_VEN_PROD_NONST_ISCR_S.csv # Agenti/venditori/produttori non stabiliti
├── ELENCO_RESP_DISTRIB_SEZ_D.csv        # Responsabili distribuzione sezione D
└── ELENCO_SITO_INTERNET.csv             # Siti internet degli intermediari
```

I file CSV sono disponibili per il download sul sito ufficiale IVASS.

---

## Eseguire i test

```bash
# Con Artisan
php artisan test

# Con Pest direttamente
./vendor/bin/pest

# Solo i test unitari
./vendor/bin/pest tests/Unit/

# Solo i test di feature
./vendor/bin/pest tests/Feature/
```
