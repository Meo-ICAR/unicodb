<?php

namespace App\Filament\Resources\SalesInvoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

class SalesInvoiceForm
{
    public static function configure(array $components): array
    {
        return [
            Section::make('Informazioni Generali')
                ->description('Dati principali della fattura')
                ->schema([
                    TextInput::make('number')
                        ->label('Numero Fattura')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('order_number')
                        ->label('Numero Ordine')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('customer_number')
                        ->label('Numero Cliente')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('customer_name')
                        ->label('Nome Cliente')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('vat_number')
                        ->label('Partita IVA')
                        ->maxLength(255)
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('Importi e Valuta')
                ->description('Dati finanziari della fattura')
                ->schema([
                    TextInput::make('amount')
                        ->label('Imponibile')
                        ->numeric()
                        ->prefix('€')
                        ->step(0.01)
                        ->required(),
                    TextInput::make('amount_including_vat')
                        ->label('Imponibile + IVA')
                        ->numeric()
                        ->prefix('€')
                        ->step(0.01)
                        ->required(),
                    TextInput::make('residual_amount')
                        ->label('Importo Residuo')
                        ->numeric()
                        ->prefix('€')
                        ->step(0.01)
                        ->nullable(),
                    TextInput::make('document_residual_amount')
                        ->label('Importo Residuo Documento')
                        ->numeric()
                        ->prefix('€')
                        ->step(0.01)
                        ->nullable(),
                    TextInput::make('exchange_rate')
                        ->label('Tasso di Cambio')
                        ->numeric()
                        ->step(0.0001)
                        ->default(1.0000)
                        ->nullable(),
                    Select::make('currency_code')
                        ->label('Valuta')
                        ->options([
                            'EUR' => 'Euro (EUR)',
                            'USD' => 'Dollaro USA (USD)',
                            'GBP' => 'Sterlina (GBP)',
                        ])
                        ->default('EUR')
                        ->required(),
                ])
                ->columns(3),

            Section::make('Date e Scadenze')
                ->description('Temporistiche della fattura')
                ->schema([
                    DatePicker::make('registration_date')
                        ->label('Data Registrazione')
                        ->required(),
                    DatePicker::make('due_date')
                        ->label('Data Scadenza')
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('Informazioni Spedizione')
                ->description('Dati di spedizione e indirizzo')
                ->schema([
                    TextInput::make('ship_to_code')
                        ->label('Codice Destinazione')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('ship_to_address')
                        ->label('Indirizzo Spedizione')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('ship_to_city')
                        ->label('Città Spedizione')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('ship_to_cap')
                        ->label('CAP Spedizione')
                        ->maxLength(10)
                        ->nullable(),
                    TextInput::make('bill_to_address')
                        ->label('Indirizzo Fatturazione')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('bill_to_city')
                        ->label('Città Fatturazione')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('bill_to_province')
                        ->label('Provincia Fatturazione')
                        ->maxLength(2)
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('Codici e Riferimenti')
                ->description('Codici di sistema e riferimenti')
                ->schema([
                    TextInput::make('agent_code')
                        ->label('Codice Agente')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('cdc_code')
                        ->label('Codice CDC')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('dimensional_link_code')
                        ->label('Codice Link Dimensionale')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('location_code')
                        ->label('Codice Location')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('payment_condition_code')
                        ->label('Condizioni Pagamento')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('payment_method_code')
                        ->label('Metodo Pagamento')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('customer_category')
                        ->label('Categoria Cliente')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('document_type')
                        ->label('Tipo Documento')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('bank_account')
                        ->label('Conto Bancario')
                        ->maxLength(255)
                        ->nullable(),
                ])
                ->columns(3),

            Section::make('Stati e Flag')
                ->description('Stati della fattura')
                ->schema([
                    Toggle::make('closed')
                        ->label('Chiusa')
                        ->default(false),
                    Toggle::make('cancelled')
                        ->label('Annullata')
                        ->default(false),
                    Toggle::make('corrected')
                        ->label('Corretta')
                        ->default(false),
                    Toggle::make('email_sent')
                        ->label('Email Inviata')
                        ->default(false),
                    Toggle::make('in_order')
                        ->label('In Ordine')
                        ->default(false),
                    Toggle::make('sent_to_sdi')
                        ->label('Inviata a SDI')
                        ->default(false),
                ])
                ->columns(3),

            Section::make('Dati Aggiuntivi')
                ->description('Informazioni supplementari')
                ->schema([
                    TextInput::make('printed_copies')
                        ->label('Copie Stampate')
                        ->numeric()
                        ->default(0)
                        ->nullable(),
                    TextInput::make('supplier_number')
                        ->label('Numero Fornitore')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('supplier_description')
                        ->label('Descrizione Fornitore')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('purchase_invoice_origin')
                        ->label('Origine Fattura Acquisto')
                        ->maxLength(255)
                        ->nullable(),
                    TextInput::make('credit_note_linked')
                        ->label('Nota di Credito Collegata')
                        ->maxLength(255)
                        ->nullable(),
                ])
                ->columns(2),
        ];
    }
}
