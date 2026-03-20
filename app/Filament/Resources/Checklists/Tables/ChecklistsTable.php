<?php

namespace App\Filament\Resources\Checklists\Tables;

use App\Models\Checklist;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ChecklistsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(function () {
                return Checklist::whereNull('target_id')->where('company_id', auth()->user()->company_id);
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'loan_management' => 'success',
                        'audit' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'loan_management' => 'Gestione Pratica',
                        'audit' => 'Audit',
                        default => $state,
                    })
                    ->sortable(),
                IconColumn::make('is_practice')
                    ->label('Pratica')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_audit')
                    ->label('Audit')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label('N. Domande')
                    ->counts('items')  // Conta le righe relazionate
                    ->badge(),
                TextColumn::make('updated_at')
                    ->label('Ultima Modifica')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filtra per Tipo')
                    ->options([
                        'loan_management' => 'Gestione Pratica',
                        'audit' => 'Audit',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                // L'azione Replicate è comodissima per creare variazioni di un template esistente
                ReplicateAction::make()
                    ->excludeAttributes(['name'])
                    ->beforeReplicaSaved(function (Model $replica): void {
                        $replica->name = $replica->name . ' (Copia)';
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
