<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $table = 'servicos';

    protected $fillable = [
        'cliente_id',
        'colaborador_id',
        'local_instalacao', 
        'data',
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

}
