<?php

namespace App\Filament\Resources\Agents\Tables;

use App\Filament\Imports\AgentsImporter;
use App\Filament\Traits\HasChecklistAction;  // 1. Importa il namespace
use App\Models\Agent;
use App\Services\ChecklistService;
use App\Services\GeminiVisionService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Maatwebsite\Excel\Excel;

class AgentsTable
{
    // 2. Usa il Trait nella classe della Risorsa

    use HasChecklistAction;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Agente')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable(),
                TextColumn::make('coordinatedBy.name')
                    ->label('Coordinato da (Dip.)')
                    ->searchable()
                    ->placeholder('Nessuno'),
                TextColumn::make('coordinatedByAgent.name')
                    ->label('Coordinato da (Agente)')
                    ->searchable()
                    ->placeholder('Nessuno'),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->searchable(),
                TextColumn::make('supervisor_type')
                    ->label('Tipo Supervisore')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'no' => 'gray',
                        'si' => 'green',
                        'filiale' => 'blue',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'no' => 'No',
                        'si' => 'Sì',
                        'filiale' => 'Filiale',
                        default => $state,
                    }),
                TextColumn::make('oam')
                    ->label('Numero OAM')
                    ->searchable(),
                TextColumn::make('oam_at')
                    ->label('Data OAM')
                    ->date()
                    ->sortable(),
                TextColumn::make('oam_name')
                    ->label('Nome OAM')
                    ->searchable(),
                TextColumn::make('ivass')
                    ->label('Codice IVASS')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('ivass_name')
                    ->label('Nome IVASS')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('ivass_section')
                    ->label('Sezione IVASS')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'A' => 'blue',
                        'B' => 'green',
                        'C' => 'yellow',
                        'D' => 'orange',
                        'E' => 'purple',
                        default => 'gray',
                    })
                    ->toggleable(),
                TextColumn::make('ivass_at')
                    ->label('Data IVASS')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('stipulated_at')
                    ->label('Stipula')
                    ->date()
                    ->sortable(),
                TextColumn::make('dismissed_at')
                    ->label('Cessazione')
                    ->date()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->searchable(),
                TextColumn::make('contribute')
                    ->label('Contributo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contributeFrequency')
                    ->label('Frequenza')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('contributeFrom')
                    ->label('Valido dal')
                    ->date()
                    ->sortable(),
                TextColumn::make('remburse')
                    ->label('Rimborso')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('vat_number')
                    ->label('CF / Partita IVA')
                    ->searchable(),
                TextColumn::make('vat_name')
                    ->label('Ragione Sociale')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                IconColumn::make('is_art108')
                    ->label('Esente art. 108')
                    ->boolean()
                    ->trueIcon('heroicon-s-shield-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->color(fn($state) => $state ? 'success' : 'gray'),
                TextColumn::make('updated_at')
                    ->label('Aggiornato')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ...self::getChecklistActions(
                    code: 'AUDIT_RETE_AGENTI',  // <-- Il 'code' esatto presente nel tuo DB
                    label: 'Audit',
                    // icon: 'heroicon-o-clipboard-document-check'
                ),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
