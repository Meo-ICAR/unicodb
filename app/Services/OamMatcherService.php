<?php

namespace App\Services;

use App\Models\Oam;  // La tua tabella ufficiale
use Illuminate\Support\Str;

class OamMatcherService
{
    /**
     * Normalizza il nome della banca per il confronto.
     */
    public function normalize(string $name): string
    {
        $name = Str::lower($name);

        // Rimuoviamo termini comuni che sporcano il confronto
        $stopwords = ['spa', 's.p.a.', 'scpa', 'srl', 'banca', 'banco', 'istituto', 'di', 'credito'];

        $name = str_replace($stopwords, '', $name);

        // Rimuove tutto ciò che non è alfanumerico
        return trim(preg_replace('/[^a-z0-9]/', '', $name));
    }

    /**
     * Cerca il match migliore tra le banche ufficiali.
     */
    public function findBestOam(string $inputName): ?string
    {
        $inputOriginal = strtoupper(trim($inputName));
        $normalizedInput = $this->normalize($inputName);

        if (empty($normalizedInput))
            return null;

        $officialOams = Oam::all();
        $bestMatch = null;
        $highestScore = 0;

        foreach ($officialOams as $Oam) {
            $officialClean = $this->normalize($Oam->name);
            $ivassClean = $Oam->ivass_name ? $this->normalize($Oam->ivass_name) : '';

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
                $bestMatch = $Oam;
            }
        }

        // Abbassiamo leggermente la soglia a 75% se usiamo il trucco del contenimento
        return ($highestScore >= 75) ? $bestMatch->abi : null;
    }

    private function calculateScore($str1, $str2): float
    {
        similar_text($str1, $str2, $percent);
        return $percent;
    }
}
