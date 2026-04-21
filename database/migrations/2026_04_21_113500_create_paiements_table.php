<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->restrictOnDelete();
            $table->tinyInteger('periode_mois');
            $table->year('periode_annee');
            $table->decimal('montant', 10, 2);
            $table->enum('mode', ['mobile_money', 'virement', 'especes', 'cheque', 'autre'])->default('virement');
            $table->string('reference', 50)->unique();
            $table->enum('statut', ['simule_reussi', 'simule_echec'])->default('simule_reussi');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['contrat_id', 'periode_annee', 'periode_mois']);
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
