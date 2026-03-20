<?php

namespace App\Filament\RelationManagers;

use App\Models\Website;
use App\Services\TransparencyScanService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class WebsitesRelationManager extends RelationManager
{
    protected static string $relationship = 'websites';
    protected static ?string $title = 'Siti Web';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome Sito')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('URL Sito')
                    ->required()
                    ->url()
                    ->maxLength(255),
                TextInput::make('url_transparency')
                    ->label('URL Trasparenza')
                    ->url()
                    ->maxLength(255)
                    ->helperText('URL della pagina trasparenza se diversa dal sito principale'),
                Textarea::make('description')
                    ->label('Descrizione')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Tipo Sito')
                    ->options([
                        'corporate' => 'Sito Corporate',
                        'institutional' => 'Sito Istituzionale',
                        'portal' => 'Portale',
                        'ecommerce' => 'E-commerce',
                        'blog' => 'Blog',
                        'other' => 'Altro',
                    ])
                    ->default('corporate'),
                Toggle::make('is_active')
                    ->label('Attivo')
                    ->default(true),
                Toggle::make('requires_transparency')
                    ->label('Richiede Trasparenza')
                    ->default(false)
                    ->helperText('Se true, il sito verrà scansionato per documenti di trasparenza'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Sito')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->limit(40)
                    ->copyable()
                    ->copyMessage('URL copiato!')
                    ->copyMessageDuration(1500),
                TextColumn::make('url_transparency')
                    ->label('URL Trasparenza')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('Non impostato')
                    ->copyable()
                    ->copyMessage('URL copiato!')
                    ->copyMessageDuration(1500),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'corporate' => 'primary',
                        'institutional' => 'success',
                        'portal' => 'info',
                        'ecommerce' => 'warning',
                        'blog' => 'secondary',
                        'other' => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                IconColumn::make('requires_transparency')
                    ->label('Richiede Trasparenza')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo Sito')
                    ->options([
                        'corporate' => 'Sito Corporate',
                        'institutional' => 'Sito Istituzionale',
                        'portal' => 'Portale',
                        'ecommerce' => 'E-commerce',
                        'blog' => 'Blog',
                        'other' => 'Altro',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Attivo'),
                TernaryFilter::make('requires_transparency')
                    ->label('Richiede Trasparenza'),
                Filter::make('has_transparency_url')
                    ->label('Ha URL Trasparenza')
                    ->query(fn($query) => $query->whereNotNull('url_transparency')),
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('run_transparency_scan')
                    ->label('Scansione Trasparenza')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Scansione Trasparenza')
                    ->modalDescription('Esegui la scansione dei siti web per trovare documenti di trasparenza')
                    ->modalSubmitActionLabel('Avvia Scansione')
                    ->action(function () {
                        $ownerRecord = $this->getOwnerRecord();
                        $scanService = new TransparencyScanService();

                        try {
                            // Get company ID based on owner type
                            $companyId = $this->getCompanyIdFromOwner($ownerRecord);

                            if (!$companyId) {
                                Notification::make()
                                    ->title('Errore Scansione')
                                    ->body("Impossibile determinare l'azienda associata")
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $results = $scanService->scanForCompany($companyId);

                            $message = "Scansione completata!\n"
                                . "Siti processati: {$results['processed_websites']}\n"
                                . "Pagine trasparenza trovate: {$results['found_transparency_pages']}\n"
                                . "Documenti estratti: {$results['extracted_documents']}";

                            Notification::make()
                                ->title('Scansione Trasparenza Completata')
                                ->body($message)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore Scansione')
                                ->body('Si è verificato un errore durante la scansione: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(function () {
                        $ownerRecord = $this->getOwnerRecord();
                        return $ownerRecord && $this->getWebsitesWithTransparency($ownerRecord)->isNotEmpty();
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('test_url')
                    ->label('Test URL')
                    ->icon('heroicon-o-globe-alt')
                    ->color('info')
                    ->url(fn(Website $record): string => $record->url)
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Get company ID from various owner types
     */
    private function getCompanyIdFromOwner($ownerRecord): ?string
    {
        return match ($ownerRecord::class) {
            'App\Models\Company' => $ownerRecord->id,
            'App\Models\Agent' => $ownerRecord->id,
            'App\Models\Client' => $ownerRecord->id,
            'App\Models\Principal' => $ownerRecord->id,
            default => null
        };
    }

    /**
     * Get websites that have transparency URL
     */
    private function getWebsitesWithTransparency($ownerRecord)
    {
        $websites = $ownerRecord->websites();

        if (method_exists($websites, 'whereNotNull')) {
            return $websites->whereNotNull('url_transparency')->get();
        }

        return collect();
    }
}
