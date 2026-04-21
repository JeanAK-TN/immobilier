<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piece_jointes', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');
            $table->foreignId('uploade_par_user_id')->constrained('users')->restrictOnDelete();
            $table->string('nom_fichier');
            $table->string('nom_original');
            $table->string('chemin');
            $table->string('type_mime');
            $table->unsignedBigInteger('taille');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piece_jointes');
    }
};
