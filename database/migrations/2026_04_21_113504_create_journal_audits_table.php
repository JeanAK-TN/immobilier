<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('modele_type')->nullable();
            $table->unsignedBigInteger('modele_id')->nullable();
            $table->string('adresse_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['modele_type', 'modele_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_audits');
    }
};
