<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('titulo');
            $table->text('descricao')->nullable();

            $table->enum('status', ['aberto','em_andamento','finalizado','cancelado'])->default('aberto');
            $table->enum('prioridade', ['baixa','media','alta'])->default('media');

            $table->date('data_servico')->nullable();
            $table->decimal('valor', 10, 2)->nullable();

            $table->timestamps();

            $table->index(['cliente_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
