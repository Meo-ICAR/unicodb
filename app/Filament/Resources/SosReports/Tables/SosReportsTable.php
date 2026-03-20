<?php

namespace App\Filament\Resources\SosReports\Tables;

use App\Filament\Resources\Checklists\ChecklistResource;
use App\Filament\Traits\HasChecklistAction;  // 1. Importa il namespace
use App\Models\SosReport;
use App\Services\ChecklistService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

class SosReportsTable
{
    use HasChecklistAction;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->placeholder('Non assegnato'),
                TextColumn::make('stato')
                    ->label('Stato')
                    ->badge()
                    ->color(fn($record): string => $record->stato_color)
                    ->formatStateUsing(fn($record): string => $record->stato_label),
                TextColumn::make('grado_sospetto')
                    ->label('Grado Sospetto')
                    ->badge()
                    ->color(fn($record): string => $record->grado_sospetto_color)
                    ->formatStateUsing(fn($record): string => $record->grado_sospetto_label),
                TextColumn::make('motivo_sospetto')
                    ->label('Motivo')
                    //  ->limitWords(10)
                    ->searchable(),
                TextColumn::make('data_segnalazione_uif')
                    ->label('Data Segnalazione UIF')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Non segnalata'),
                TextColumn::make('responsabile.name')
                    ->label('Responsabile')
                    ->searchable()
                    ->placeholder('Non assegnato'),
                IconColumn::make('has_checklist')
                    ->label('Checklist')
                    ->boolean()
                    ->getStateUsing(function (SosReport $record): bool {
                        return $record
                            ->checklist()
                            ->where('code', 'SOS_WORKFLOW')
                            ->exists();
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aggiornato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('stato')
                    ->label('Stato')
                    ->options([
                        'istruttoria' => 'Istruttoria',
                        'archiviata' => 'Archiviata',
                        'segnalata_uif' => 'Segnalata UIF',
                    ]),
                SelectFilter::make('grado_sospetto')
                    ->label('Grado Sospetto')
                    ->options([
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                    ]),
            ])
            ->recordActions([
                ...self::getChecklistActions(
                    code: 'SOS_WORKFLOW',  // <-- Il 'code' esatto presente nel tuo DB
                    label: 'Op. Sospetta'
                    // icon: 'heroicon-o-clipboard-document-check'
                ),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
