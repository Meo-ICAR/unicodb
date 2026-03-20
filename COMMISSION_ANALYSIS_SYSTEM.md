# Sistema di Analisi Commissioni Agenti

Questo sistema permette di raggruppare le commissioni degli agenti e confrontarle con le fatture di acquisto per analizzare le performance.

## Componenti del Sistema

### 1. Tabella `agent_commission_groups`
- **Scopo**: Raggruppare le commissioni per agente e data fattura
- **Campi principali**:
  - `agent_id`: ID dell'agente
  - `invoice_at`: Data della fattura
  - `total_commission_amount`: Somma delle commissioni
  - `total_invoice_amount`: Importo della fattura di acquisto
  - `commission_percentage`: Percentuale di commissione
  - `purchase_invoice_id`: Link alla fattura di acquisto
  - `is_matched`: Se è stato trovato un match con fattura

### 2. Modello `AgentCommissionGroup`
- Relazioni con `Agent` e `PurchaseInvoice`
- Scopes per filtrare (matched, unmatched, forAgent, forDate)
- Metodi per calcolare percentuali

### 3. Comandi Laravel

#### Raggruppamento Commissioni
```bash
# Raggruppa tutte le commissioni
php artisan commissions:group-agent

# Per una company specifica
php artisan commissions:group-agent --company=company-id

# Per una data specifica
php artisan commissions:group-agent --date=2025-12-15

# Ricrea i gruppi (cancella e ricrea)
php artisan commissions:group-agent --recreate
```

#### Analisi dei Match
```bash
# Analizza tutti i match
php artisan commissions:analyze-matches

# Per company specifica
php artisan commissions:analyze-matches --company=company-id

# Esporta risultati in CSV
php artisan commissions:analyze-matches --export
```

## Processo di Matching

1. **Raggruppamento**: Le commissioni con `tipo = 'Agente'` sono raggruppate per:
   - `agent_id`
   - `company_id` 
   - `invoice_at`

2. **Matching con Fatture**: Per ogni gruppo, il sistema cerca una fattura di acquisto con:
   - Stessa `company_id`
   - `document_date` = `invoice_at`

3. **Calcolo Percentuale**: `(commission_amount / invoice_amount) * 100`

## Risultati dell'Analisi

### Statistiche Generali
- **128 match** trovati su 159 gruppi (80.5%)
- **€355,247.85** totali in commissioni
- **€415,058.47** totali in fatture
- **Media 139.90%** di commissione

### Distribuzione Percentuali
- **38.3%** dei gruppi: 100-200%
- **21.1%** dei gruppi: 50-100%
- **19.5%** dei gruppi: 200%+
- **7.8%** dei gruppi: 10-25% e 25-50%
- **5.5%** dei gruppi: 0-10%

### Pattern Interessanti

#### Commissioni Elevate (200%+)
- **Agent 25 - 2025-04-24**: €13,030.74 vs €500.00 (2606.2%)
- **Agent 9 - 2025-03-31**: €871.20 vs €40.00 (2178.0%)
- **Agent 45 - 2025-11-18**: €19,505.88 vs €1,345.68 (1449.5%)

#### Andamento Mensile
- **Febbraio**: €28,758 commission vs €18,341 fatture (250.8%)
- **Aprile**: €30,741 commission vs €23,106 fatture (502.3%)
- **Settembre**: €15,039 commission vs €5,377 fatture (509.2%)

## Interpretazione dei Dati

### Perché le commissioni sono spesso più alte delle fatture?

1. **Commissioni su Pratiche Multiple**: Una fattura può coprire commissioni per più pratiche
2. **Costi Aggiuntivi**: Le commissioni possono includere costi non presenti in fattura
3. **Differenze Temporali**: Commissioni e fatture potrebbero non essere perfettamente allineate
4. **Agent ID Mismatch**: Alcuni agent_id potrebbero non corrispondere agli agenti attuali

### Raccomandazioni

1. **Verifica Agent ID**: Controllare se gli agent_id nelle commissioni corrispondono agli agenti attuali
2. **Analisi per Prodotto**: Segmentare le commissioni per tipo di pratica/prodotto
3. **Allineamento Temporale**: Considerare finestre di tempo più ampie per il matching
4. **Validazione Dati**: Verificare la qualità dei dati sia delle commissioni che delle fatture

## Export e Reporting

Il sistema supporta l'esportazione dei risultati in formato CSV per ulteriori analisi in Excel o altri strumenti.

```bash
# Esporta analisi completa
php artisan commissions:analyze-matches --export
```

Il file CSV contiene:
- Agent ID
- Data fattura
- Importo commissione
- Importo fattura
- Percentuale
- Numero fattura

## Manutenzione

### Pulizia Dati
```bash
# Ricrea tutti i gruppi
php artisan commissions:group-agent --recreate
```

### Monitoring
- Controllare regolarmente il tasso di match (obiettivo: >80%)
- Monitorare le percentuali anomale (>500%)
- Verificare gruppi senza match

## Troubleshooting

### Problemi Comuni
1. **Agent ID non validi**: Gli agent_id nelle commissioni non esistono nella tabella agents
2. **Date non allineate**: Le date delle commissioni non corrispondono esattamente alle date delle fatture
3. **Percentuali estreme**: Valori >1000% indicano probabili errori di matching

### Soluzioni
1. **Validazione Agent ID**: Implementare mapping tra vecchi e nuovi agent ID
2. **Matching Fuzzy**: Usare finestre di tempo di ±3 giorni
3. **Threshold Analysis**: Impostare soglie per identificare match anomali
