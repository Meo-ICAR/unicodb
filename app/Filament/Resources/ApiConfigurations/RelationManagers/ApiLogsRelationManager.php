<?php

namespace App\Filament\Resources\ApiConfigurations\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ApiLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'apiLogs';

    protected static ?string $title = 'Log API';

    protected static ?string $modelLabel = 'Log';

    protected static ?string $pluralModelLabel = 'Log';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Forms\Components\TextInput::make('method')
                    ->label('Metodo')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('endpoint')
                    ->label('Endpoint')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('status_code')
                    ->label('Codice Stato')
                    ->numeric()
                    ->required(),
                \Filament\Forms\Components\Textarea::make('request_body')
                    ->label('Request Body')
                    ->rows(3),
                \Filament\Forms\Components\Textarea::make('response_body')
                    ->label('Response Body')
                    ->rows(3),
                \Filament\Forms\Components\TextInput::make('response_time')
                    ->label('Tempo Risposta (ms)')
                    ->numeric()
                    ->nullable(),
                \Filament\Forms\Components\Textarea::make('error_message')
                    ->label('Messaggio Errore')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('endpoint')
            ->columns([
                Tables\Columns\TextColumn::make('method')
                    ->label('Metodo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'GET' => 'success',
                        'POST' => 'info',
                        'PUT' => 'warning',
                        'DELETE' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('endpoint')
                    ->label('Endpoint')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('status_code')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'warning',
                        $state >= 400 => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('response_time')
                    ->label('Tempo (ms)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('error_message')
                    ->label('Errore')
                    ->limit(30)
                    ->color('danger')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data/Ora')
                    ->dateTime()
                    ->sortable()
                    ->dateTimeFormat('d/m/Y H:i:s'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('method')
                    ->label('Metodo')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'DELETE' => 'DELETE',
                    ]),
                Tables\Filters\SelectFilter::make('status_code')
                    ->label('Codice Stato')
                    ->options([
                        '200' => '200 OK',
                        '201' => '201 Created',
                        '400' => '400 Bad Request',
                        '401' => '401 Unauthorized',
                        '404' => '404 Not Found',
                        '500' => '500 Server Error',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
