<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiVisionService
{
    /**
     * Invia il documento d'identità a Gemini e restituisce i dati strutturati.
     */
    public function extractIdentityData(string $imagePath): ?array
    {
        $apiKey = config('services.gemini.api_key');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";

        // 1. Leggiamo l'immagine e la convertiamo in Base64
        if (!file_exists($imagePath)) {
            Log::error("Immagine non trovata: {$imagePath}");
            return null;
        }

        $imageData = base64_encode(file_get_contents($imagePath));
        $mimeType = mime_content_type($imagePath);

        // 2. Prepariamo il prompt specifico per l'IA
        $prompt = "Sei un assistente specializzato nell'estrazione dati. Analizza questo documento d'identità italiano. Estrai esattamente le seguenti informazioni: nome, cognome, data_di_nascita (nel formato YYYY-MM-DD), numero_documento, e data_scadenza. Restituisci SOLO un oggetto JSON valido.";

        // 3. Eseguiamo la chiamata tramite l'HTTP Client di Laravel
        $response = Http::post($endpoint, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData
                            ]
                        ]
                    ]
                ]
            ],
            // QUESTO È IL SEGRETO: Forza Gemini a rispondere in JSON nativo
            'generationConfig' => [
                'responseMimeType' => 'application/json',
            ]
        ]);

        // 4. Gestione degli errori
        if ($response->failed()) {
            Log::error('Errore API Gemini: ' . $response->body());
            return null;
        }

        $result = $response->json();

        // 5. Estrazione del testo dalla struttura di risposta di Gemini
        $jsonString = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$jsonString) {
            return null;
        }

        // Decodifichiamo la stringa JSON in un array associativo PHP
        return json_decode($jsonString, true);
    }
}
