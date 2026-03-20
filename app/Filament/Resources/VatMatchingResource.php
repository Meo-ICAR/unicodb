<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VatMatchingResource\Pages\ListVatMatchings;
use App\Models\PracticeCommission;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VatMatchingResource extends Resource
{
    protected static ?string $model = PracticeCommission::class;
    
    protected static ?string $navigationLabel = 'Matching VAT';
    
    protected static ?string $modelLabel = 'Commissione VAT Match';
    
    protected static ?string $pluralModelLabel = 'Commissioni VAT Match';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('principal.name')
                    ->label('Principal')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('alternative_number_invoice')
                    ->label('Alternative Invoice')
                    ->searchable()
                    ->sortable()
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('invoice_at')
                    ->label('Data Commissione')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('has_alternative')
                    ->label('VAT Match')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record) => !is_null($record->alternative_number_invoice)),
            ])
            ->filters([
                Tables\Filters\Filter::make('has_alternative')
                    ->label('Ha VAT Match')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('alternative_number_invoice')),
                    
                Tables\Filters\Filter::make('no_alternative')
                    ->label('Senza VAT Match')
                    ->query(fn (Builder $query): Builder => $query->whereNull('alternative_number_invoice')),
                    
                Tables\Filters\SelectFilter::make('principal_id')
                    ->label('Principal')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('invoice_at', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVatMatchings::route('/'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tipo', 'Istituto')
            ->with(['principal'])
            ->where(function (Builder $query) {
                $query->whereNotNull('invoice_number')
                      ->orWhereNotNull('alternative_number_invoice');
            });
    }
}
