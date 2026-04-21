<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locataires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('cree_par_user_id')->constrained('users')->restrictOnDelete();
            $table->string('prenom');
            $table->string('nom');
            $table->string('telephone')->nullable();
            $table->string('email');
            $table->string('piece_identite_path')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('cree_par_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locataires');
    }
};
