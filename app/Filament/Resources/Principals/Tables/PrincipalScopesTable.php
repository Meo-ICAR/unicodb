<?php

namespace App\Filament\Resources\Principals\Tables;

use App\Models\Principal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PrincipalScopesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                // Partiamo da Principal ma joiniamo Practices
                Principal::query()
                    ->select([
                        'principals.id',
                        'principals.name',
                        'practices.tipo_prodotto',
                        'principals.stipulated_at',  // Serve per il toggle
                        DB::raw('COUNT(*) as n_pratiche'),
                        DB::raw('MIN(practices.inserted_at) as dal'),
                        DB::raw('MAX(practices.perfected_at) as al'),
                    ])
                    ->join('practices', 'practices.principal_id', '=', 'principals.id')
                    ->whereNotIn('practices.tipo_prodotto', ['PIGNORAMENTO'])
                    ->where('practices.tipo_prodotto', 'not like', 'Alime%')
                    ->where('practices.tipo_prodotto', '>', 'A')
                    ->groupBy('principals.id', 'principals.name', 'practices.tipo_prodotto', 'principals.stipulated_at')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Mandante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tipo_prodotto')
                    ->label('Prodotto'),
                TextColumn::make('n_pratiche')
                    ->label('N. Pratiche')
                    ->badge(),
                TextColumn::make('dal')
                    ->label('Data Inizio')
                    ->date('d/m/Y'),
                TextColumn::make('al')
                    ->label('Data Fine')
                    ->date('d/m/Y'),
                IconColumn::make('convenzionato')
                    ->label('Convenzionato')
                    // Logica visualizzazione: ON se "al" non è null
                    ->getStateUsing(fn($record) => filled($record->al))
                // Logica di salvataggio
                //       ->updateStateUsing(function ($record, $state) {
                //    $record->update([
                //         'stipulated_at' => $state ? $record->dal : null,
                //       ]);
                //    })
            ])
            ->filters([
                SelectFilter::make('convenzionato')
                    ->label('Convenzionato')
                    ->options([
                        '1' => 'Convenzionato',
                        '0' => 'Non Convenzionato',
                    ]),
                // Filtro per Mandante (Principal)

                /*
                 * SelectFilter::make('principal_id')
                 *     ->label('Mandante')
                 *     ->relationship('principal', 'name')  // Assumendo che esista la relazione 'principal' nel model Practice
                 *     ->searchable()
                 *     ->preload(),
                 */
                // Filtro testuale per il Tipo Prodotto
                Filter::make('tipo_prodotto')
                    ->form([
                        TextInput::make('tipo_prodotto')
                            ->label('Cerca Prodotto'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tipo_prodotto'],
                            fn(Builder $query, $value): Builder => $query->where('practices.tipo_prodotto', 'like', "%{$value}%"),
                        );
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    protected function getTableRecordKey($record): string
    {
        // Unisce l'ID del mandante al nome prodotto per rendere la riga univoca nella tabella
        return (string) $record->id . '-' . $record->tipo_prodotto;
    }
}
