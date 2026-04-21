<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->restrictOnDelete();
            $table->foreignId('locataire_id')->constrained('locataires')->restrictOnDelete();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->boolean('reconduction_auto')->default(false);
            $table->decimal('loyer_mensuel', 10, 2);
            $table->decimal('depot_garantie', 10, 2)->default(0);
            $table->decimal('charges', 10, 2)->default(0);
            $table->tinyInteger('jour_paiement')->default(1)->comment('Jour du mois pour le paiement');
            $table->enum('statut', ['brouillon', 'en_attente_signature', 'actif', 'termine', 'resilie'])->default('brouillon');
            $table->string('document_path')->nullable();
            $table->timestamp('signe_le')->nullable();
            $table->string('signe_nom')->nullable();
            $table->string('signe_ip', 45)->nullable();
            $table->timestamps();

            $table->index(['bien_id', 'statut']);
            $table->index(['locataire_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
