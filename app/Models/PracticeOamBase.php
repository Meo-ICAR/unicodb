<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeOamBase extends Model
{
    protected $table = 'practice_oam_base';

    protected $fillable = [
        'company_id',
        'B_OAM',  // oam_name
        'C_Convenzionata',  // is_conventioned
        'D_Non_Convenzionata',  // is_notconventioned
        'E_Intermediate',  // is_perfected
        'F_Lavorazione',  // is_working
        'G_Erogato',  // erogato
        'H_Erogato_Lavorazione',  // erogato_lavorazione
        'I_Provvigione_Cliente',  // compenso_cliente
        'J_Provvigione_Istituto',  // compenso
        'K_Provvigione_Istituto_Lavorazione',  // compenso_lavorazione
        'O_Provvigione_Rete',  // provvigione
        'erogato',
        'erogato_lavorazione',
        'liquidato',
        'liquidato_lavorazione',
    ];

    protected $casts = [
        'company_id' => 'string',
        'C_Convenzionata' => 'integer',
        'D_Non_Convenzionata' => 'integer',
        'E_Intermediate' => 'integer',
        'F_Lavorazione' => 'integer',
        'G_Erogato' => 'decimal:2',
        'H_Erogato_Lavorazione' => 'decimal:2',
        'I_Provvigione_Cliente' => 'decimal:2',
        'J_Provvigione_Istituto' => 'decimal:2',
        'K_Provvigione_Istituto_Lavorazione' => 'decimal:2',
        'O_Provvigione_Rete' => 'decimal:2',
        'erogato' => 'decimal:2',
        'liquidato' => 'decimal:2',
        'erogato_lavorazione' => 'decimal:2',
        'liquidato_lavorazione' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
