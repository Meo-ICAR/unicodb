<?php

namespace App\Contracts;

use App\Models\Document;
use App\Models\User;

interface SignatureServiceInterface
{
    /**
     * Invia un documento per la firma e restituisce l'ID della transazione o un link.
     */
    public function sendForSignature(Document $document, User $signer): string;

    /**
     * Controlla lo stato attuale della pratica di firma.
     */
    public function checkStatus(string $transactionId): string;

    /**
     * Scarica il documento firmato.
     */
    public function downloadSignedDocument(string $transactionId): string;  // Ritorna il path o il contenuto
}
