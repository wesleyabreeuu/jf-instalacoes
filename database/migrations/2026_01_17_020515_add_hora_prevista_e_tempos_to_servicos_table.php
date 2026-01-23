<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            // Horário previsto da execução (agendado)
            $table->time('hora_prevista')->nullable()->after('data');

            // tempos calculados (minutos)
            $table->unsignedInteger('tempo_deslocamento_min')->nullable()->after('hora_finalizado');
            $table->unsignedInteger('tempo_servico_min')->nullable()->after('tempo_deslocamento_min');
        });
    }

    public function down(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->dropColumn(['hora_prevista','tempo_deslocamento_min','tempo_servico_min']);
        });
    }
};
