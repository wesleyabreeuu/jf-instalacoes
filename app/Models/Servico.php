<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $table = 'servicos';

    protected $fillable = [
        'cliente_id',
        'colaborador_id',
        'usuario_id',
        'titulo',
        'local_instalacao', 
        'tipo_servico',
        'orcamento_descricao',
        'orcamento_descricao_servico',
        'orcamento_tempo_instalacao_min',
        'orcamento_data_pre_agendada',
        'orcamento_finalizado_em',
        'orcamento_convertido_em',
        'data',
        'data_servico',
        'hora_prevista',
        'status',
        'hora_deslocamento',     // DATETIME (hora de abertura)
        'hora_execucao',         // DATETIME (hora de início)
        'hora_finalizado',       // DATETIME (hora de fim)
        'tempo_deslocamento_min',
        'tempo_servico_min',
    ];

    protected $casts = [
        'data' => 'date',
        'hora_deslocamento' => 'datetime',
        'hora_execucao' => 'datetime',
        'hora_finalizado' => 'datetime',
        'orcamento_data_pre_agendada' => 'date',
        'orcamento_finalizado_em' => 'datetime',
        'orcamento_convertido_em' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function colaborador()
    {
        return $this->belongsTo(User::class, 'colaborador_id');
    }

    public function materiais()
        {
            return $this->belongsToMany(\App\Models\Material::class, 'servico_materiais', 'servico_id', 'material_id')
                ->withPivot(['quantidade_usada'])
                ->withTimestamps();
        }

    public function getTipoServicoLabelAttribute(): string
    {
        return match ($this->tipo_servico) {
            'instalacao' => 'Instalação',
            'manutencao' => 'Manutenção',
            'orcamento' => 'Orçamento',
            default => '-',
        };
    }

    public function getOrcamentoTempoInstalacaoTextoAttribute(): string
    {
        if (!$this->orcamento_tempo_instalacao_min) {
            return '-';
        }

        $minutos = (int) $this->orcamento_tempo_instalacao_min;
        $horas = intdiv($minutos, 60);
        $resto = $minutos % 60;

        if ($horas <= 0) {
            return $resto . ' min';
        }

        return $horas . 'h ' . str_pad((string) $resto, 2, '0', STR_PAD_LEFT) . 'min';
    }

}
