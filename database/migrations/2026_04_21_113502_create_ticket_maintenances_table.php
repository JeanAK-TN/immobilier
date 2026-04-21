<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->restrictOnDelete();
            $table->foreignId('soumis_par_user_id')->constrained('users')->restrictOnDelete();
            $table->string('titre');
            $table->enum('categorie', ['plomberie', 'electricite', 'chauffage', 'menuiserie', 'serrurerie', 'peinture', 'autre'])->default('autre');
            $table->enum('priorite', ['basse', 'moyenne', 'haute'])->default('moyenne');
            $table->text('description');
            $table->enum('statut', ['ouvert', 'en_cours', 'en_attente_locataire', 'resolu', 'ferme'])->default('ouvert');
            $table->timestamps();

            $table->index(['contrat_id', 'statut']);
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_maintenances');
    }
};
