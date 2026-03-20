<?php

namespace App\Services;

use App\Contracts\SignatureServiceInterface;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Exception;

class YousignSignatureService implements SignatureServiceInterface
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        // Recuperiamo le chiavi dal file .env o dalla configurazione
        $this->apiKey = config('services.yousign.key');
        $this->baseUrl = config('services.yousign.url');
    }

    public function sendForSignature(Document $document, User $signer): string
    {
        // Chiamata API reale verso Yousign
        $response = Http::withToken($this->apiKey)
            ->baseUrl($this->baseUrl)
            ->post('/signature_requests', [
                'name' => 'Firma Contratto Mediatore',
                'signers' => [
                    [
                        'info' => [
                            'first_name' => $signer->first_name,
                            'last_name' => $signer->last_name,
                            'email' => $signer->email,
                        ],
                        'signature_level' => 'electronic_signature'
                    ]
                ]
            ]);

        if ($response->failed()) {
            throw new Exception("Errore durante l'invio a Yousign: " . $response->body());
        }

        // Restituiamo l'ID univoco della transazione generato dal provider
        return $response->json('id');
    }

    public function checkStatus(string $transactionId): string
    {
        // Logica per interrogare l'API di Yousign sullo stato...
        return 'pending';  // es: pending, completed, rejected
    }

    public function downloadSignedDocument(string $transactionId): string
    {
        // Logica per scaricare il PDF...
        return '/storage/app/signed/document.pdf';
    }
}
