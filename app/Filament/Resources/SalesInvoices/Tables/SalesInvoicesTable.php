<?php

namespace App\Filament\Resources\SalesInvoices\Tables;

use App\Models\SalesInvoice;
use App\Services\SalesInvoiceImportService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SalesInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Numero')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registration_date')
                    ->label('Data Registrazione')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => $record->isOverdue() ? 'danger' : null),
                TextColumn::make('amount_including_vat')
                    ->label('Importo Totale')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('residual_amount')
                    ->label('Importo Residuo')
                    ->money('EUR')
                    ->sortable()
                    ->color(fn($record) => $record->residual_amount > 0 ? 'warning' : 'success'),
                TextColumn::make('document_type')
                    ->label('Tipo Doc.')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('closed')
                    ->label('Chiusa')
                    ->boolean(),
                IconColumn::make('cancelled')
                    ->label('Annullata')
                    ->boolean(),
                IconColumn::make('email_sent')
                    ->label('Email')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('document_type')
                    ->label('Tipo Documento')
                    ->options(function () {
                        return \App\Models\SalesInvoice::distinct('document_type')
                            ->whereNotNull('document_type')
                            ->pluck('document_type', 'document_type')
                            ->toArray();
                    }),
                Filter::make('registration_date')
                    ->label('Data Registrazione')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('registered_from')
                            ->label('Da'),
                        \Filament\Forms\Components\DatePicker::make('registered_until')
                            ->label('A'),
                    ])
                    ->query(function (array $data) {
                        return SalesInvoice::query()
                            ->when(
                                $data['registered_from'],
                                fn($query, $date) => $query->whereDate('registration_date', '>=', $date)
                            )
                            ->when(
                                $data['registered_until'],
                                fn($query, $date) => $query->whereDate('registration_date', '<=', $date)
                            );
                    }),
                Filter::make('overdue')
                    ->label('Scadute')
                    ->query(fn($query) => $query->where('due_date', '<', now())->where('residual_amount', '>', 0)),
                Filter::make('open')
                    ->label('Aperte')
                    ->query(fn($query) => $query->where('closed', false)),
                Filter::make('cancelled')
                    ->label('Annullate')
                    ->query(fn($query) => $query->where('cancelled', true)),
            ])
            ->actions([
                // ViewAction::make(),
                //  EditAction::make(),
            ])
            ->bulkActions([
                //   BulkActionGroup::make([
                //   DeleteBulkAction::make(),
                //   ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('import_sales_invoices')
                    ->label('Import Sales Invoices')
                    ->icon('heroicon-o-document-arrow-up')
                    ->form([
                        FileUpload::make('import_file')
                            ->label('CSV/Excel File')
                            ->helperText('Upload a CSV or Excel file containing sales invoice data')
                            ->acceptedFileTypes(['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                            ->maxSize(10240)  // 10MB
                            ->directory('sales-invoice-imports')
                            ->visibility('private')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        try {
                            $filePath = storage_path('app/public/' . $data['import_file']);
                            $companyId = Auth::user()->company_id;
                            $filename = basename($data['import_file']);

                            $importService = new SalesInvoiceImportService($filename);
                            $results = $importService->import($filePath, $companyId);

                            // Show success notification
                            Notification::make()
                                ->title('Import Completed')
                                ->body("Successfully processed import from {$filename}. Imported: {$results['imported']}, Updated: {$results['updated']}, Errors: {$results['errors']}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            // Show error notification
                            Notification::make()
                                ->title('Import Failed')
                                ->body('Error during import: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateActions([
                //  CreateAction::make(),
            ])
            ->defaultSort('registration_date', 'desc');
    }
}
