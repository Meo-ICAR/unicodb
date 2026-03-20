<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nomina Responsabile Esterno - {{ $partner->name ?? $partner->ragione_sociale }}</title>
    <style>
        @page { margin: 120px 40px 80px 40px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10pt; line-height: 1.4; color: #333; text-align: justify; }

        /* Header e Footer identici a prima per mantenere la carta intestata */
        header { position: fixed; top: -90px; left: 0px; right: 0px; height: 80px; border-bottom: 2px solid #0044cc; text-align: center; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 40px; font-size: 8pt; color: #666; text-align: center; border-top: 1px solid #ccc; padding-top: 5px; }
        .logo { max-height: 60px; width: auto; margin-bottom: 10px; }

        h1 { font-size: 13pt; text-align: center; text-transform: uppercase; margin-bottom: 20px; }
        h2 { font-size: 11pt; margin-top: 20px; margin-bottom: 8px; font-weight: bold; }
        .box { padding: 10px; border: 1px solid #ccc; background-color: #fafafa; margin-bottom: 15px; }
        .firma-container { margin-top: 40px; width: 100%; display: table; }
        .firma-box { display: table-cell; width: 50%; text-align: center; }
    </style>
</head>
<body>

    <header>
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" class="logo" alt="Logo">
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
        <h1>ATTO DI NOMINA A RESPONSABILE ESTERNO DEL TRATTAMENTO DEI DATI PERSONALI</h1>
        <p style="text-align: center; font-size: 9pt; margin-top: -15px;">
            <em>Ai sensi dell'art. 28 del Regolamento (UE) 2016/679 (GDPR)</em>
        </p>

        <p>Tra la società <strong>{{ $company->name }}</strong> (di seguito "Titolare"), e l'entità <strong>{{ $partner->name ?? $partner->ragione_sociale }}</strong> (di seguito "Responsabile"), operante come <em>{{ $profilo->name }}</em>.</p>

        <p>Premesso che tra il Titolare e il Responsabile è in essere un rapporto contrattuale o di collaborazione, il Titolare <strong>nomina</strong> la controparte quale Responsabile Esterno del Trattamento dei Dati Personali.</p>

        <h2>1. Oggetto del Trattamento</h2>
        <div class="box">
            <ul style="margin: 0; padding-left: 20px;">
                <li><strong>Finalità del Trattamento:</strong> {{ $profilo->purpose }}</li>
                <li><strong>Categorie di Interessati:</strong> {{ $profilo->data_subjects }}</li>
                <li><strong>Categorie di Dati Trattati:</strong> {{ $profilo->data_categories }}</li>
            </ul>
        </div>

        <h2>2. Obblighi del Responsabile</h2>
        <p>Ai sensi dell'art. 28 GDPR, il Responsabile si impegna a:</p>
        <ul style="padding-left: 20px;">
            <li>Trattare i dati personali soltanto su istruzione documentata del Titolare (incluso il presente atto).</li>
            <li>Garantire che le persone autorizzate al trattamento si siano impegnate alla riservatezza o abbiano un adeguato obbligo legale di riservatezza.</li>
            <li>Assistere il Titolare con misure tecniche e organizzative adeguate per evadere le richieste di esercizio dei diritti degli interessati.</li>
            <li>Assistere il Titolare nel garantire il rispetto degli obblighi in materia di sicurezza (Data Breach).</li>
        </ul>

        <h2>3. Misure di Sicurezza</h2>
        <p>Il Responsabile dichiara di aver adottato e di mantenere le seguenti misure di sicurezza minime per tutta la durata del trattamento:</p>
        <div class="box">
            <em>{{ $profilo->security_measures }}</em>
        </div>

        <h2>4. Trasferimento Dati e Sub-Responsabili</h2>
        <p>In merito al trasferimento Extra-UE, il profilo prevede: <strong>{{ $profilo->extra_eu_transfer }}</strong>. Qualora il Responsabile intenda avvalersi di sub-responsabili, dovrà richiedere preventiva autorizzazione scritta al Titolare.</p>

        <h2>5. Tempi di Conservazione e Cancellazione</h2>
        <p>
            I dati dovranno essere conservati per: <strong>{{ $profilo->retention_period }}</strong>.
            Al termine del contratto o delle finalità previste, il Responsabile dovrà cancellare o restituire tutti i dati personali al Titolare, cancellando le copie esistenti.
        </p>

        <div class="firma-container">
            <div class="firma-box">
                <p>Luogo e Data</p>
                <p>___________________________</p>
                <p style="font-size: 9pt; margin-top: 30px;"><strong>Il Titolare del Trattamento</strong><br>{{ $company->name }}</p>
            </div>
            <div class="firma-box">
                <p>Per accettazione della Nomina</p>
                <p>___________________________</p>
                <p style="font-size: 9pt; margin-top: 30px;"><strong>Il Responsabile Esterno</strong><br>{{ $partner->name ?? $partner->ragione_sociale }}</p>
            </div>
        </div>
    </main>
</body>
</html>
