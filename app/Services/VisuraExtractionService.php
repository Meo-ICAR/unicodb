<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VisuraExtractionService
{
    /**
     * Estrae i dati da una Visura camerale PDF utilizzando Gemini.
     */
    public function extractFromPdf(string $pdfPath): ?array
    {
        $apiKey = config('services.gemini.api_key');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        if (!file_exists($pdfPath)) {
            Log::error("File PDF non trovato: {$pdfPath}");
            return null;
        }

        $pdfData = base64_encode(file_get_contents($pdfPath));
        $mimeType = 'application/pdf';

        $prompt = "Sei un assistente specializzato nell'estrazione dati da documenti ufficiali italiani. 
        Analizza questa Visura Camerale (PDF). Estrai le seguenti informazioni in formato JSON:
        - denominazione (Nome della società)
        - codice_fiscale
        - partita_iva
        - sede_legale (Indirizzo completo)
        - data_iscrizione (Formato YYYY-MM-DD)
        - stato_attivita (Es. ATTIVA, INATTIVA, IN LIQUIDAZIONE)
        - rea (Numero REA e provincia, es. RM-123456)
        - capitale_sociale (Valore numerico se presente)
        - forma_giuridica (Es. SRL, SPA, SNC)
        - oggetto_sociale (Breve sintesi)
        - amministratori (Array di oggetti con nome, cognome e carica)

        Restituisci SOLO un oggetto JSON valido.";

        try {
            $response = Http::post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $pdfData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->failed()) {
                Log::error('Errore API Gemini (Visura): ' . $response->body());
                return null;
            }

            $result = $response->json();
            $jsonString = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$jsonString) {
                return null;
            }

            return json_decode($jsonString, true);
        } catch (\Exception $e) {
            Log::error('Eccezione durante estrazione Visura: ' . $e->getMessage());
            return null;
        }
    }
}
