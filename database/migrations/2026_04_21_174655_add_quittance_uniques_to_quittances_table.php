<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quittances', function (Blueprint $table) {
            $table->unique('paiement_id');
            $table->unique(['contrat_id', 'periode_annee', 'periode_mois'], 'quittances_contrat_periode_unique');
        });
    }

    public function down(): void
    {
        Schema::table('quittances', function (Blueprint $table) {
            $table->dropUnique(['paiement_id']);
            $table->dropUnique('quittances_contrat_periode_unique');
        });
    }
};
