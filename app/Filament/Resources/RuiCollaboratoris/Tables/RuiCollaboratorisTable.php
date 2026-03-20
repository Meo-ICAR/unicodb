<?php

namespace App\Filament\Resources\RuiCollaboratoris\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RuiCollaboratorisTable
{
    /*
     * create view vwruicollaboratori as select
     *   concat(r.cognome_nome, r.ragione_sociale) as intermediario,
     *   concat(rc.cognome_nome, rc.ragione_sociale) as collaboratore,
     *   concat(rd.cognome_nome, rd.ragione_sociale) as dipendente,
     *  c.num_iscr_intermediario, c.num_iscr_collaboratori_i_liv, c.num_iscr_collaboratori_ii_liv
     *   from rui_collaboratori c
     * left outer join rui r on r.numero_iscrizione_rui = c.num_iscr_intermediario
     * left outer join rui rc on rc.numero_iscrizione_rui = c.num_iscr_collaboratori_i_liv
     * left outer join rui rd on rd.numero_iscrizione_rui = c.num_iscr_collaboratori_ii_liv
     *
     * ALTER TABLE rui ADD PRIMARY KEY (numero_iscrizione_rui);
     * -- Oppure, se esiste già una PK diversa:
     * CREATE UNIQUE INDEX idx_rui_num_iscrizione ON rui(numero_iscrizione_rui);
     *
     * CREATE INDEX idx_intermediario ON rui_collaboratori(num_iscr_intermediario);
     * CREATE INDEX idx_collab_i ON rui_collaboratori(num_iscr_collaboratori_i_liv);
     * CREATE INDEX idx_collab_ii ON rui_collaboratori(num_iscr_collaboratori_ii_liv);
     *
     * CREATE INDEX idx_rui_fast_lookup
     * ON rui(numero_iscrizione_rui, cognome_nome, ragione_sociale);
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('intermediario')
                    ->searchable(),
                TextColumn::make('collaboratore')
                    ->searchable(),
                TextColumn::make('dipendente')
                    ->searchable(),
                TextColumn::make('qualifica_rapporto')
                    ->searchable(),
                TextColumn::make('oss')
                    ->searchable(),
                TextColumn::make('livello')
                    ->searchable(),
                TextColumn::make('num_iscr_collaboratori_i_liv')
                    ->searchable(),
                TextColumn::make('num_iscr_collaboratori_ii_liv')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
