<?php

namespace App\Filament\Resources\PurchaseInvoices\Schemas;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Company;
use App\Models\Principal;
use App\Services\PurchaseInvoiceImportService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PurchaseInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('number')
                                    ->label('Invoice Number')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('supplier_invoice_number')
                                    ->label('Supplier Invoice Number')
                                    ->maxLength(255),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('supplier')
                                    ->label('Supplier Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('supplier_number')
                                    ->label('Supplier Number')
                                    ->maxLength(255),
                            ]),
                    ]),
                Section::make('Relationships')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('company_id')
                                    ->label('Company')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('invoiceable_type')
                                    ->label('Attach To')
                                    ->options([
                                        Client::class => 'Client',
                                        Agent::class => 'Agent',
                                        Principal::class => 'Principal',
                                    ])
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set) => $set('invoiceable_id', null)),
                                Select::make('invoiceable_id')
                                    ->label('Select Record')
                                    ->required()
                                    ->reactive()
                                    ->getSearchResultsUsing(function (callable $get) {
                                        $type = $get('invoiceable_type');
                                        if (!$type)
                                            return [];

                                        $search = request()->get('search');
                                        switch ($type) {
                                            case Client::class:
                                                return Client::where('name', 'like', "%{$search}%")
                                                    ->orWhere('first_name', 'like', "%{$search}%")
                                                    ->limit(50)
                                                    ->pluck('name', 'id')
                                                    ->toArray();
                                            case Agent::class:
                                                return Agent::where('name', 'like', "%{$search}%")
                                                    ->orWhere('first_name', 'like', "%{$search}%")
                                                    ->limit(50)
                                                    ->pluck('name', 'id')
                                                    ->toArray();
                                            case Principal::class:
                                                return Principal::where('name', 'like', "%{$search}%")
                                                    ->limit(50)
                                                    ->pluck('name', 'id')
                                                    ->toArray();
                                            default:
                                                return [];
                                        }
                                    })
                                    ->getOptionLabelUsing(function (callable $get) {
                                        $type = $get('invoiceable_type');
                                        $id = $get('invoiceable_id');
                                        if (!$type || !$id)
                                            return null;

                                        switch ($type) {
                                            case Client::class:
                                                return Client::find($id)?->name;
                                            case Agent::class:
                                                return Agent::find($id)?->name;
                                            case Principal::class:
                                                return Principal::find($id)?->name;
                                            default:
                                                return null;
                                        }
                                    }),
                            ]),
                    ]),
                Section::make('Financial Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01),
                                TextInput::make('amount_including_vat')
                                    ->label('Amount Including VAT')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01),
                                TextInput::make('residual_amount')
                                    ->label('Residual Amount')
                                    ->numeric()
                                    ->prefix('€')
                                    ->step(0.01),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('currency_code')
                                    ->label('Currency Code')
                                    ->maxLength(3)
                                    ->default('EUR'),
                                TextInput::make('exchange_rate')
                                    ->label('Exchange Rate')
                                    ->numeric()
                                    ->step(0.0001)
                                    ->default(1.0),
                                TextInput::make('supplier_category')
                                    ->label('Supplier Category')
                                    ->maxLength(255),
                            ]),
                    ]),
                Section::make('Dates')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('document_date')
                                    ->label('Document Date'),
                                DatePicker::make('registration_date')
                                    ->label('Registration Date'),
                                DatePicker::make('due_date')
                                    ->label('Due Date'),
                            ]),
                    ]),
                Section::make('Payment & Address Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('payment_condition_code')
                                    ->label('Payment Condition Code')
                                    ->maxLength(255),
                                TextInput::make('payment_method_code')
                                    ->label('Payment Method Code')
                                    ->maxLength(255),
                                TextInput::make('pay_to_address')
                                    ->label('Payment Address')
                                    ->maxLength(255),
                                TextInput::make('pay_to_city')
                                    ->label('Payment City')
                                    ->maxLength(255),
                                TextInput::make('pay_to_cap')
                                    ->label('Payment CAP')
                                    ->maxLength(10),
                                TextInput::make('pay_to_country_code')
                                    ->label('Payment Country Code')
                                    ->maxLength(2),
                            ]),
                    ]),
                Section::make('Supplier Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('vat_number')
                                    ->label('VAT Number')
                                    ->maxLength(255),
                                TextInput::make('fiscal_code')
                                    ->label('Fiscal Code')
                                    ->maxLength(255),
                                TextInput::make('document_type')
                                    ->label('Document Type')
                                    ->maxLength(255),
                                TextInput::make('location_code')
                                    ->label('Location Code')
                                    ->maxLength(255),
                            ]),
                    ]),
                Section::make('Status')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('closed')
                                    ->label('Closed')
                                    ->default(false),
                                Toggle::make('cancelled')
                                    ->label('Cancelled')
                                    ->default(false),
                                Toggle::make('corrected')
                                    ->label('Corrected')
                                    ->default(false),
                            ]),
                        TextInput::make('printed_copies')
                            ->label('Printed Copies')
                            ->numeric()
                            ->default(0),
                    ]),
                Section::make('Import Purchase Invoices')
                    ->description('Upload a CSV file to import purchase invoices. The file will be processed using the import service.')
                    ->schema([
                        FileUpload::make('import_file')
                            ->label('CSV File')
                            ->helperText('Upload a CSV file containing purchase invoice data')
                            ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(10240)  // 10MB
                            ->directory('purchase-invoice-imports')
                            ->visibility('private')
                            ->required(),
                        Placeholder::make('import_info')
                            ->label('Import Information')
                            ->content(function ($get) {
                                $file = $get('import_file');
                                if (!$file) {
                                    return 'Please select a file to import.';
                                }
                                return "Selected file: {$file}";
                            }),
                    ])
                    ->afterStateUpdated(function ($state, $set, $livewire) {
                        if ($state && $livewire instanceof \Livewire\Component) {
                            // Process the import when file is uploaded
                            try {
                                $filePath = storage_path('app/public/' . $state);
                                $companyId = Auth::user()->company_id ?? Company::first()->id;
                                $filename = basename($state);

                                $importService = new PurchaseInvoiceImportService($companyId, $filename);
                                $results = $importService->import($filePath, $companyId);

                                // Show success notification
                                Notification::make()
                                    ->title('Import Completed')
                                    ->body("Successfully processed import from {$filename}. Imported: {$results['imported']}, Updated: {$results['updated']}, Errors: {$results['errors']}")
                                    ->success()
                                    ->send();

                                // Clear the file input
                                $set('import_file', null);

                                // Refresh the page to show new data
                                $livewire->dispatch('refresh');
                            } catch (\Exception $e) {
                                // Show error notification
                                Notification::make()
                                    ->title('Import Failed')
                                    ->body('Error during import: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }
                    }),
            ]);
    }
}
