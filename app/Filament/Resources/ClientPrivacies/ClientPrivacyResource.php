<?php

namespace App\Filament\Resources\ClientPrivacies;

use App\Filament\Resources\Checklists\ChecklistResource;
use App\Filament\Resources\ClientPrivacies\Pages\ManageClientPrivacies;
use App\Filament\Traits\HasChecklistAction;  // 1. Importa il namespace
use App\Models\Client;
use App\Models\ClientPrivacy;
use App\Services\ChecklistService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use BackedEnum;
use UnitEnum;

class ClientPrivacyResource extends Resource
{
    use HasChecklistAction;

    protected static ?string $model = ClientPrivacy::class;

    protected static bool $isScopedToTenant = false;

    public static function getTenantOwnershipRelationship(Model $record): Relation
    {
        return $record->client();
    }

    protected static ?int $navigationSort = 6;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static string|UnitEnum|null $navigationGroup = 'Compliance';
    protected static ?string $navigationLabel = 'Privacy Clienti';
    protected static ?string $modelLabel = 'Privacy Cliente';
    protected static ?string $pluralModelLabel = 'Privacy Clienti';
    protected static ?string $recordTitleAttribute = 'request_type';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->required(),
                Select::make('request_type')
                    ->label('Tipo Richiesta')
                    ->options([
                        'Accesso' => 'Accesso',
                        'Rettifica' => 'Rettifica',
                        'Cancellazione' => 'Cancellazione',
                        'Portabilità' => 'Portabilità',
                    ])
                    ->required(),
                Select::make('status')
                    ->label('Stato')
                    ->options([
                        'Ricevuta' => 'Ricevuta',
                        'In lavorazione' => 'In lavorazione',
                        'Evasa' => 'Evasa',
                    ])
                    ->required(),
                DateTimePicker::make('completed_at')
                    ->label('Data della risposta definitiva'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('request_type')
            ->columns([
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('request_type')
                    ->label('Tipo Richiesta')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Ricevuta' => 'info',
                        'In lavorazione' => 'warning',
                        'Evasa' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                IconColumn::make('has_checklist')
                    ->label('Checklist')
                    ->boolean()
                    ->getStateUsing(function (ClientPrivacy $record): bool {
                        return $record
                            ->checklist()
                            ->where('code', 'GDPR_ACCESS_REQ')
                            ->exists();
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('completed_at')
                    ->label('Data Evasione')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Data Richiesta')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ...self::getChecklistActions(
                    code: 'GDPR_ACCESS_REQ',  // <-- Il 'code' esatto presente nel tuo DB
                    label: 'Checklist Privacy',
                    // icon: 'heroicon-o-clipboard-document-check'
                ),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClientPrivacies::route('/'),
        ];
    }
}
