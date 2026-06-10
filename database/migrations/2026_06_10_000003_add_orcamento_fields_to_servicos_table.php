<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            if (!Schema::hasColumn('servicos', 'orcamento_descricao')) {
                $table->text('orcamento_descricao')->nullable()->after('tipo_servico');
            }

            if (!Schema::hasColumn('servicos', 'orcamento_descricao_servico')) {
                $table->text('orcamento_descricao_servico')->nullable()->after('orcamento_descricao');
            }

            if (!Schema::hasColumn('servicos', 'orcamento_tempo_instalacao_min')) {
                $table->unsignedInteger('orcamento_tempo_instalacao_min')->nullable()->after('orcamento_descricao_servico');
            }

            if (!Schema::hasColumn('servicos', 'orcamento_data_pre_agendada')) {
                $table->date('orcamento_data_pre_agendada')->nullable()->after('orcamento_tempo_instalacao_min');
            }

            if (!Schema::hasColumn('servicos', 'orcamento_finalizado_em')) {
                $table->timestamp('orcamento_finalizado_em')->nullable()->after('orcamento_data_pre_agendada');
            }

            if (!Schema::hasColumn('servicos', 'orcamento_convertido_em')) {
                $table->timestamp('orcamento_convertido_em')->nullable()->after('orcamento_finalizado_em');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            $columns = [
                'orcamento_descricao',
                'orcamento_descricao_servico',
                'orcamento_tempo_instalacao_min',
                'orcamento_data_pre_agendada',
                'orcamento_finalizado_em',
                'orcamento_convertido_em',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('servicos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
