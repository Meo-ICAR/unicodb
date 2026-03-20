<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Compilio - La Piattaforma Definitiva per i Mediatori Creditizi</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Fallback se Vite non è compilato - Tailwind Play CDN per dev veloce -->
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
        <style>
            body { font-family: 'Inter', sans-serif; }
            .bg-grid-pattern {
                background-image: linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px);
                background-size: 40px 40px;
            }
        </style>
    </head>
    <body class="antialiased bg-slate-50 text-slate-800 selection:bg-blue-600 selection:text-white">

        <!-- Navigation -->
        <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-600/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <span class="font-extrabold text-2xl tracking-tight text-slate-900">Compilio</span>
                    </div>
                    <div class="hidden md:flex space-x-8">
                        <a href="#compliance" class="text-slate-600 hover:text-blue-600 font-medium transition">Compliance</a>
                        <a href="#audit" class="text-slate-600 hover:text-blue-600 font-medium transition">Audit & Documenti</a>
                        <a href="#integrazioni" class="text-slate-600 hover:text-blue-600 font-medium transition">Integrazioni</a>
                    </div>
                    <div class="flex items-center gap-4">
                        @if (Route::has('filament.admin.auth.login'))
                            @auth
                                <a href="{{ url('/admin') }}" class="text-sm font-semibold text-slate-900 hover:text-blue-600 transition">Dashboard</a>
                            @else
                                <a href="{{ route('filament.admin.auth.login') }}" class="hidden sm:inline-block text-sm font-semibold text-slate-700 hover:text-blue-600 transition">Accedi</a>
                                <a href="{{ route('filament.admin.auth.login') }}" class="inline-flex justify-center items-center py-2.5 px-5 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/30 transition-all active:scale-95">Area Riservata &rarr;</a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-slate-900 text-white">
            <div class="absolute inset-0 bg-grid-pattern opacity-20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
            <div class="absolute right-0 top-0 -mr-48 -mt-48 w-96 h-96 rounded-full bg-blue-600/20 blur-3xl"></div>
            <div class="absolute left-0 bottom-0 -ml-48 -mb-48 w-96 h-96 rounded-full bg-emerald-500/20 blur-3xl"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-sm font-semibold mb-8 border border-blue-500/20">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                    La prima piattaforma OAM Compliance Ready
                </div>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight mb-8">
                    Mediazione Creditizia,<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">Senza Pensieri.</span>
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-slate-300 mb-10">
                    Un'unica app per gestire tutta la tua rete strutturata. Pienamente conforme alle normative <strong>Privacy</strong>, <strong>Adeguata Verifica (AML)</strong> e linee guida <strong>OAM</strong>.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    @auth
                        <a href="{{ url('/admin') }}" class="inline-flex justify-center items-center py-4 px-8 text-lg font-semibold rounded-xl bg-blue-600 text-white hover:bg-blue-500 shadow-xl shadow-blue-600/30 transition-all hover:-translate-y-1">Vai alla tua Dashboard</a>
                    @else
                        <a href="{{ route('filament.admin.auth.login') }}" class="inline-flex justify-center items-center py-4 px-8 text-lg font-semibold rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 text-white hover:from-blue-500 hover:to-blue-400 shadow-xl shadow-blue-600/30 transition-all hover:-translate-y-1">Inizia Ora</a>
                    @endauth
                    <a href="#features" class="inline-flex justify-center items-center py-4 px-8 text-lg font-semibold rounded-xl bg-slate-800 text-white hover:bg-slate-700 border border-slate-700 transition-all">Scopri le Funzioni</a>
                </div>
            </div>
        </div>

        <!-- Logos/Trust Section -->
        <div class="py-10 bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm font-semibold text-slate-500 mb-6 uppercase tracking-wider">Conforme ai requisiti richiesti da</p>
                <div class="flex justify-center items-center gap-8 md:gap-16 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                    <div class="text-2xl font-black text-slate-800">OAM</div>
                    <div class="text-2xl font-black text-slate-800">Antiriciclaggio</div>
                    <div class="text-2xl font-black text-slate-800">Garante Privacy</div>
                </div>
            </div>
        </div>

        <!-- Core Features -->
        <div id="features" class="py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Tutto in uno</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                        Il motore della tua Società di Mediazione
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1: Compliance -->
                    <div id="compliance" class="bg-white rounded-2xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 hover:border-blue-100 hover:shadow-blue-100/50 transition-all group">
                        <div class="w-14 h-14 bg-blue-50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Compliance Totale</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Strutturata per rispettare nativamente il <strong>GDPR</strong>, la normativa Antiriciclaggio (<strong>Adeguata Verifica</strong>) e i rigidi protocolli di <strong>Vigilanza OAM</strong>. Un'unica piattaforma sicura per blindare la tua agenzia.
                        </p>
                    </div>

                    <!-- Feature 2: Audit -->
                    <div id="audit" class="bg-white rounded-2xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 hover:border-emerald-100 hover:shadow-emerald-100/50 transition-all group">
                        <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Gestione Audit a 360°</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Gestisci con serenità gli audit imposti dalle <strong>Banche Mandanti</strong> e conduci ispezioni accurate sulla <strong>tua rete di Agenti</strong>. Compilio tiene traccia di ogni conformità o anomalia.
                        </p>
                    </div>

                    <!-- Feature 3: Documenti e Scadenze -->
                    <div class="bg-white rounded-2xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 hover:border-amber-100 hover:shadow-amber-100/50 transition-all group">
                        <div class="w-14 h-14 bg-amber-50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">Documenti & Scadenze</h3>
                        <p class="text-slate-600 leading-relaxed">
                            Archiviazione documentale avanzata per Clienti e Pratiche. Il sistema ti notifica prima della scadenza di carte d'identità, moduli OAM e certificazioni formative obbligatorie.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Integrations Banner -->
        <div id="integrazioni" class="bg-white py-20 border-t border-slate-200 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
                    <div>
                        <h2 class="text-3xl font-extrabold text-slate-900 mb-6">Perfettamente Interfacciato con il Tuo Ecosistema</h2>
                        <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                            Compilio non è un'isola isolata. Le nostre API avanzate permettono di sincronizzare i dati delle Pratiche e dei Proforma direttamente con i tuoi sistemi di <strong>Contabilità</strong> e con il tuo <strong>CRM</strong> aziendale, azzerando il data-entry manuale.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-center text-slate-700 font-medium">
                                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Fatturazione Automatica dai Proforma
                            </li>
                            <li class="flex items-center text-slate-700 font-medium">
                                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Allineamento Anagrafiche Clienti nel CRM
                            </li>
                            <li class="flex items-center text-slate-700 font-medium">
                                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Webhooks per aggiornamenti in Tempo Reale
                            </li>
                        </ul>
                    </div>
                    <div class="mt-12 lg:mt-0 relative">
                        <!-- Abstract illustration of APIs -->
                        <div class="relative rounded-2xl bg-slate-900 p-8 shadow-2xl overflow-hidden aspect-video flex items-center justify-center">
                            <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
                            <div class="flex items-center justify-center gap-6 relative z-10 w-full">
                                <!-- CRM Block -->
                                <div class="bg-blue-600/20 border border-blue-500/30 w-1/3 rounded-xl p-4 text-center backdrop-blur-sm animate-pulse">
                                    <div class="text-blue-400 font-bold mb-1">CRM</div>
                                    <div class="text-xs text-blue-300/60">Anagrafiche</div>
                                </div>
                                <!-- Compilio Core -->
                                <div class="bg-white w-1/3 rounded-xl p-6 text-center shadow-[0_0_30px_rgba(255,255,255,0.2)] z-20 transform scale-110">
                                    <div class="text-slate-900 font-black text-xl mb-1">Compilio</div>
                                    <div class="text-xs text-slate-500 font-semibold text-[10px] tracking-widest uppercase">API Core</div>
                                </div>
                                <!-- ERP Block -->
                                <div class="bg-emerald-600/20 border border-emerald-500/30 w-1/3 rounded-xl p-4 text-center backdrop-blur-sm animate-pulse" style="animation-delay: 500ms">
                                    <div class="text-emerald-400 font-bold mb-1">ERP</div>
                                    <div class="text-xs text-emerald-300/60">Contabilità</div>
                                </div>
                            </div>
                            <!-- Connection lines -->
                            <div class="absolute top-1/2 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-blue-500/50 via-white to-emerald-500/50 z-0 border-t border-dashed border-white/20"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-blue-600 relative overflow-hidden">
            <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center relative z-10">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl mb-6">
                    Metti al sicuro la tua rete di Mediazione
                </h2>
                <p class="text-xl text-blue-100 mb-10">
                    Smetti di rincorrere le scadenze su fogli Excel. Digitalizza, automatizza e proteggi il tuo business oggi stesso.
                </p>
                <a href="{{ route('filament.admin.auth.login') }}" class="inline-flex justify-center items-center py-4 px-10 text-lg font-bold rounded-xl bg-white text-blue-700 hover:bg-slate-50 shadow-2xl transition-all hover:scale-105">
                    Accedi all'Area Riservata
                </a>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-slate-900 py-12 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2 text-white">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="font-bold text-xl tracking-tight">Compilio</span>
                </div>
                <p class="text-slate-400 text-sm">
                    &copy; {{ date('Y') }} Compilio. Multi-Tenant Credit Mediation App. All rights reserved.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="text-slate-400 hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="text-slate-400 hover:text-white transition">Termini di Servizio</a>
                </div>
            </div>
        </footer>
    </body>
</html>
