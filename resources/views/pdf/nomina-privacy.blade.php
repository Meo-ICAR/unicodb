<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nomina Privacy - {{ $dipendente->name }}</title>
    <style>
        @page {
            margin: 120px 40px 80px 40px; /* Margini: Su, Destra, Giù, Sinistra */
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }
        /* Configurazione Carta Intestata (Header e Footer fissi) */
        header {
            position: fixed;
            top: -90px;
            left: 0px;
            right: 0px;
            height: 80px;
            border-bottom: 2px solid #0044cc; /* Colore personalizzabile */
            text-align: center;
        }
        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 40px;
            font-size: 8pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        .logo {
            max-height: 60px;
            width: auto;
            margin-bottom: 10px;
        }
        /* Tipografia */
        h1 { font-size: 14pt; text-align: center; text-transform: uppercase; margin-bottom: 20px; }
        h2 { font-size: 12pt; text-decoration: underline; margin-top: 25px; margin-bottom: 10px; }
        .highlight { font-weight: bold; background-color: #f0f0f0; padding: 2px 5px; }

        /* Firme */
        .firma-container { margin-top: 60px; width: 100%; display: table; }
        .firma-box { display: table-cell; width: 50%; text-align: center; }
    </style>
</head>
<body>

    <header>
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo" alt="Logo Aziendale">
        @endif

        @if($company->header_page)
            <div style="font-size: 9pt;">{!! $company->header_page !!}</div>
        @endif
    </header>

    <footer>
        @if($company->footer_page)
            {!! $company->footer_page !!}
        @endif
    </footer>

    <main>
        <h1>Lettera di Autorizzazione al Trattamento dei Dati Personali</h1>
        <p style="text-align: center; font-size: 9pt;">
            <em>Ai sensi dell'Art. 29 del Regolamento (UE) 2016/679 (GDPR) e dell'Art. 2-quaterdecies del D.Lgs. 196/2003</em>
        </p>

        <p>
            La scrivente società <strong>{{ $company->name }}</strong>, in qualità di Titolare del Trattamento dei dati, con la presente
        </p>
        <p style="text-align: center; font-size: 12pt; font-weight: bold; margin: 20px 0;">
            NOMINA E AUTORIZZA
        </p>
        <p>
            Il/La Sig./Sig.ra <span class="highlight">{{ $dipendente->name }}</span> (di seguito "Autorizzato"),
            in qualità di dipendente/collaboratore con mansione di <span class="highlight">{{ $ruolo->name }}</span>,
            a compiere le operazioni di trattamento dei dati personali strettamente connesse all'esecuzione dei propri compiti lavorativi.
        </p>

        <h2>1. Finalità e Categorie di Dati</h2>
        <p>In base al Suo ruolo ({{ $ruolo->privacy_role }}), Lei è formalmente autorizzato a trattare i dati per le seguenti finalità:</p>
        <ul>
            <li><strong>Finalità:</strong> {{ $ruolo->purpose }}</li>
            <li><strong>Categorie di Interessati:</strong> {{ $ruolo->data_subjects }}</li>
            <li><strong>Categorie di Dati Trattati:</strong> {{ $ruolo->data_categories }}</li>
        </ul>

        <h2>2. Trasferimento e Conservazione</h2>
        <p>
            I dati trattati nell'ambito delle Sue mansioni sono soggetti alle policy aziendali di conservazione
            ({{ $ruolo->retention_period }}). Riguardo al trasferimento dei dati al di fuori dello Spazio Economico Europeo (Extra-UE),
            il Suo profilo prevede: <strong>{{ $ruolo->extra_eu_transfer }}</strong>.
        </p>

        <h2>3. Istruzioni Operative e Misure di Sicurezza</h2>
        <p>Nello svolgimento delle Sue mansioni quotidiane, Lei dovrà attenersi scrupolosamente alle seguenti misure di sicurezza specifiche per il Suo profilo:</p>
        <div style="padding: 10px; border-left: 3px solid #0044cc; background-color: #fafafa; margin-bottom: 20px;">
            <em>{{ $ruolo->security_measures }}</em>
        </div>

        <p>Inoltre, si ricorda l'obbligo generale di:</p>
        <ul style="font-size: 10pt;">
            <li>Custodire con diligenza le credenziali di autenticazione al gestionale.</li>
            <li>Adottare la politica della "scrivania pulita" (Clean Desk Policy) e bloccare lo schermo del PC in caso di allontanamento.</li>
            <li>Non effettuare copie, stampe o salvataggi su dispositivi personali di dati appartenenti ai clienti.</li>
        </ul>

        <div class="firma-container">
            <div class="firma-box">
                <p>Luogo e Data</p>
                <p>___________________________</p>
                <p style="font-size: 9pt; margin-top: 30px;"><strong>Il Titolare del Trattamento</strong><br>{{ $company->name }}</p>
            </div>
            <div class="firma-box">
                <p>Per ricevuta, presa visione e accettazione</p>
                <p>___________________________</p>
                <p style="font-size: 9pt; margin-top: 30px;"><strong>L'Autorizzato al Trattamento</strong><br>{{ $dipendente->name }}</p>
            </div>
        </div>
    </main>
</body>
</html>
