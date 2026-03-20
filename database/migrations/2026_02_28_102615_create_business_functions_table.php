<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('business_functions', function (Blueprint $table) {
            $table->comment('Funzioni aziendali per funzionogramma');
            $table->id()->comment('ID univoco funzione business');
            // Il nuovo campo code univoco
            $table->string('code')->unique()->comment('Codice identificativo univoco funzione');

            $table->enum('macro_area', [
                'Governance',
                'Business / Commerciale',
                'Supporto',
                'Controlli (II Livello)',
                'Controlli (III Livello)',
                'Controlli / Privacy'
            ])->comment('Macro area di appartenenza');

            $table->enum('name', [
                'Consiglio di Amministrazione / Direzione',
                'Direzione Commerciale',
                'Gestione Rete e Collaboratori',
                'Back Office / Istruttoria Pratiche',
                'Amministrazione e Contabilità',
                'IT e Sicurezza Dati',
                'Marketing e Comunicazione',
                'Gestione Reclami e Controversie',
                'Risorse Umane (HR) e Formazione',
                'Compliance (Conformità)',
                'Risk Management',
                'Antiriciclaggio (AML)',
                'Internal Audit (Revisione Interna)',
                'Data Protection Officer (DPO)'
            ])->comment('Nome specifico funzione business');

            $table->enum('type', [
                'Strategica',
                'Operativa',
                'Supporto',
                'Controllo'
            ])->comment('Tipologia funzione');

            $table->text('description')->nullable()->comment('Descrizione dettagliata funzione');

            $table->enum('outsourcable_status', ['si', 'no', 'parziale'])->default('no')->comment('Esternalizzabile: si/no/parziale');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_functions');
    }
};
