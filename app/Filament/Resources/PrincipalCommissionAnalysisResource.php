<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrincipalCommissionAnalysisResource\Pages\ListPrincipalCommissionAnalyses;
use App\Models\PrincipalCommissionGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PrincipalCommissionAnalysisResource extends Resource
{
    protected static ?string $model = PrincipalCommissionGroup::class;

    protected static ?string $navigationLabel = 'Analisi Commissioni Principal';

    protected static ?string $modelLabel = 'Analisi Commissione Principal';

    protected static ?string $pluralModelLabel = 'Analisi Commissioni Principal';

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
                Tables\Columns\TextColumn::make('invoice_at')
                    ->label('Data Fattura')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_commission_amount')
                    ->label('Commissione')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_invoice_amount')
                    ->label('Importo Fattura')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('commission_percentage')
                    ->label('Percentuale')
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 1) . '%' : 'N/A')
                    ->color(function ($state) {
                        if (!$state)
                            return 'gray';

                        if ($state > 200)
                            return 'danger';
                        if ($state > 100)
                            return 'warning';
                        if ($state > 50)
                            return 'success';
                        return 'info';
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_matched')
                    ->label('Match')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('number_invoice')
                    ->label('Nr. Fattura')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('salesInvoice.number')
                    ->label('Nr. Fattura (Rel)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Note')
                    ->limit(50)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_matched')
                    ->label('Stato Match')
                    ->options([
                        '1' => 'Matched',
                        '0' => 'Unmatched',
                    ]),
                Tables\Filters\Filter::make('high_percentage')
                    ->label('Percentuale > 200%')
                    ->query(fn(Builder $query): Builder => $query->where('commission_percentage', '>', 200)),
                Tables\Filters\Filter::make('low_percentage')
                    ->label('Percentuale < 50%')
                    ->query(fn(Builder $query): Builder => $query->where('commission_percentage', '<', 50)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => ListPrincipalCommissionAnalyses::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['principal', 'salesInvoice'])
            ->matched();
    }
}
