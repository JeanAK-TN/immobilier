<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quittances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->restrictOnDelete();
            $table->foreignId('paiement_id')->constrained('paiements')->restrictOnDelete();
            $table->foreignId('generee_par_user_id')->constrained('users')->restrictOnDelete();
            $table->tinyInteger('periode_mois');
            $table->year('periode_annee');
            $table->string('numero_quittance')->unique();
            $table->timestamp('emise_le');
            $table->string('fichier_path')->nullable();
            $table->timestamps();

            $table->index(['contrat_id', 'periode_annee', 'periode_mois']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quittances');
    }
};
