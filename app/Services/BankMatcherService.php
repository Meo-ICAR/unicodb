<?php

namespace App\Services;

use App\Models\Abi as Bank;  // La tua tabella ufficiale
use Illuminate\Support\Str;

/**
 * Servizio per il matching fuzzy di nomi di banche ai codici ABI ufficiali.
 *
 * Normalizza i nomi in input rimuovendo stopword bancarie comuni e caratteri
 * non alfanumerici, quindi confronta il risultato con l'elenco ufficiale ABI
 * tramite similarità testuale e controllo di contenimento, restituendo il
 * codice ABI con il punteggio più alto (soglia minima: 75%).
 */
class BankMatcherService
{
    /**
     * Normalizza il nome della banca per il confronto.
     *
     * Converte la stringa in minuscolo, rimuove le stopword bancarie comuni
     * (es. "banca", "spa", "s.p.a.", "credito") e tutti i caratteri non
     * alfanumerici, restituendo una stringa pulita adatta al confronto.
     *
     * @param  string $name Il nome della banca da normalizzare.
     * @return string       Il nome normalizzato, privo di stopword e caratteri speciali.
     */
    public function normalize(string $name): string
    {
        $name = Str::lower($name);

        // Rimuoviamo le forme punteggiate prima di rimuovere i caratteri speciali
        $dottedStopwords = ['s.p.a.', 's.c.p.a.'];
        $name = str_replace($dottedStopwords, ' ', $name);

        // Rimuoviamo termini comuni che sporcano il confronto (solo come parole intere)
        $stopwords = ['spa', 'scpa', 'srl', 'banca', 'banco', 'istituto', 'di', 'credito'];

        foreach ($stopwords as $stopword) {
            $name = preg_replace('/\b' . preg_quote($stopword, '/') . '\b/', ' ', $name);
        }

        // Rimuove tutto ciò che non è alfanumerico
        return preg_replace('/[^a-z0-9]/', '', $name);
    }

    /**
     * Cerca il codice ABI ufficiale che meglio corrisponde al nome fornito.
     *
     * Confronta il nome normalizzato con tutti i record della tabella ABI
     * usando `similar_text` e un controllo di contenimento. Restituisce il
     * codice ABI del match con punteggio più alto se questo è >= 75,
     * altrimenti restituisce `null`.
     *
     * @param  string      $inputName Il nome della banca da cercare.
     * @return string|null            Il codice ABI corrispondente, o `null` se nessun match supera la soglia.
     */
    public function findBestAbi(string $inputName): ?string
    {
        $inputOriginal = strtoupper(trim($inputName));
        $normalizedInput = $this->normalize($inputName);

        if (empty($normalizedInput))
            return null;

        $officialBanks = Bank::all();
        $bestMatch = null;
        $highestScore = 0;

        foreach ($officialBanks as $bank) {
            $officialClean = $this->normalize($bank->name);
            $ivassClean = $bank->ivass_name ? $this->normalize($bank->ivass_name) : '';

            // 1. Calcolo similitudine classica
            similar_text($normalizedInput, $officialClean, $scoreName);
            similar_text($normalizedInput, $ivassClean, $scoreIvass);
            $currentMax = max($scoreName, $scoreIvass);

            // 2. Controllo di "Contenimento" (MOLTO EFFICACE per nomi come "Banca Progetto")
            // Se il nome ufficiale contiene esattamente il nome input (o viceversa)
            if (str_contains($officialClean, $normalizedInput) || str_contains($normalizedInput, $officialClean)) {
                $currentMax = max($currentMax, 90);  // Forza uno score alto se una stringa è dentro l'altra
            }

            if ($currentMax > $highestScore) {
                $highestScore = $currentMax;
                $bestMatch = $bank;
            }
        }

        // Abbassiamo leggermente la soglia a 75% se usiamo il trucco del contenimento
        return ($highestScore >= 75) ? $bestMatch->abi : null;
    }

}
