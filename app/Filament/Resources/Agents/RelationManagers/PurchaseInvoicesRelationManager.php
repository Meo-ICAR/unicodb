<?php

namespace App\Filament\Resources\Agents\RelationManagers;

use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class PurchaseInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseInvoices';

    protected static ?string $relatedResource = PurchaseInvoiceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('number')
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('supplier')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_including_vat')
                    ->label('Amount incl. VAT')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_date')
                    ->label('Document Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(function ($state, $record) {
                        if (!$state || $record->closed)
                            return null;
                        return $state->isPast() ? 'danger' : null;
                    }),
                Tables\Columns\IconColumn::make('closed')
                    ->label('Closed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('supplier_category')
                    ->label('Category')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('closed')
                    ->options([
                        '1' => 'Closed',
                        '0' => 'Open',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(function ($query) {
                        return $query
                            ->where('closed', false)
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', now());
                    }),
                Tables\Filters\SelectFilter::make('supplier_category')
                    ->label('Supplier Category')
                    ->options(function () {
                        return \App\Models\PurchaseInvoice::whereNotNull('supplier_category')
                            ->distinct()
                            ->pluck('supplier_category', 'supplier_category')
                            ->toArray();
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('document_date', 'desc');
    }
}
