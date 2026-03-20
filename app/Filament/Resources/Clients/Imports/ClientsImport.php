<?php

namespace App\Filament\Resources\Clients\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?Client
    {
        return new Client([
            'company_id' => $row['company_id'] ?? null,
            'client_type_id' => $this->getClientTypeId($row['tipo_cliente'] ?? null),
            'first_name' => $row['nome'] ?? null,
            'last_name' => $row['cognome'] ?? null,
            'tax_code' => $row['codice_fiscale'] ?? null,
            'vat_number' => $row['partita_iva'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['telefono'] ?? null,
            'birth_date' => $this->parseDate($row['data_nascita'] ?? null),
            'birth_city' => $row['comune_nascita'] ?? null,
            'address' => $row['indirizzo'] ?? null,
            'city' => $row['citta'] ?? null,
            'province' => $row['provincia'] ?? null,
            'postal_code' => $row['cap'] ?? null,
            'country' => $row['stato'] ?? null,
            'income' => $row['reddito'] ?? null,
            'is_active' => $this->parseBoolean($row['attivo'] ?? true),
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|string|max:36',
            'tipo_cliente' => 'nullable|string|max:50',
            'nome' => 'required|string|max:100',
            'cognome' => 'required|string|max:100',
            'codice_fiscale' => 'nullable|string|max:16',
            'partita_iva' => 'nullable|string|max:11',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'data_nascita' => 'nullable|date',
            'comune_nascita' => 'nullable|string|max:100',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:100',
            'provincia' => 'nullable|string|max:2',
            'cap' => 'nullable|string|max:5',
            'stato' => 'nullable|string|max:2',
            'reddito' => 'nullable|numeric',
            'attivo' => 'required|boolean',
        ];
    }

    private function getClientTypeId($clientType): ?int
    {
        if (empty($clientType)) {
            return null;
        }

        $clientTypeMap = [
            'Privato Consumatore' => 1,
            'Autonomo' => 2,
            'Azienda' => 3,
            'Ditta Individuale' => 4,
            'Libero Professionista' => 5,
            'Pensionato' => 6,
        ];

        return $clientTypeMap[$clientType] ?? null;
    }

    private function parseDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            $dateObj = \DateTime::createFromFormat('d/m/Y', $date);
            return $dateObj ? $dateObj->format('Y-m-d') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseBoolean($value): bool
    {
        if (empty($value)) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
