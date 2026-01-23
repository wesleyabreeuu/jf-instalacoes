<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servico_materiais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('servico_id')
                ->constrained('servicos')
                ->cascadeOnDelete();

            $table->foreignId('material_id')
                ->constrained('materiais')
                ->restrictOnDelete(); // evita apagar material que já foi usado

            $table->decimal('quantidade_usada', 10, 2);

            $table->timestamps();

            $table->unique(['servico_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servico_materiais');
    }
};
