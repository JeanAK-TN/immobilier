<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('nom');
            $table->enum('type', ['maison', 'appartement', 'terrain', 'bureau', 'commercial', 'autre'])->default('appartement');
            $table->string('adresse');
            $table->string('ville');
            $table->string('pays')->default('France');
            $table->text('description')->nullable();
            $table->enum('statut', ['disponible', 'occupe'])->default('disponible');
            $table->timestamps();

            $table->index(['user_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
