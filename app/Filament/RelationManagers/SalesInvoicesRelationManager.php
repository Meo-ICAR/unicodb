<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\SalesInvoices\SalesInvoiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class SalesInvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'salesInvoices';

    protected static ?string $relatedResource = salesInvoiceResource::class;

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
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('document_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('closed')
                    ->label('Closed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(function ($state, $record) {
                        if (!$state || $record->closed)
                            return null;
                        return $state->isPast() ? 'danger' : null;
                    }),
                Tables\Columns\TextColumn::make('invoiceable_type')
                    ->label('Attached To')
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return 'None';
                        return match ($state) {
                            'App\Models\Client' => 'Client',
                            'App\Models\Agent' => 'Agent',
                            'App\Models\Principal' => 'Principal',
                            default => class_basename($state),
                        };
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'App\Models\Client' => 'success',
                        'App\Models\Agent' => 'warning',
                        'App\Models\Principal' => 'info',
                        default => 'gray',
                    }),
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
                Tables\Filters\SelectFilter::make('invoiceable_type')
                    ->label('Attached To')
                    ->options([
                        'App\Models\Client' => 'Client',
                        'App\Models\Agent' => 'Agent',
                        'App\Models\Principal' => 'Principal',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('document_date', 'desc');
    }
}
