<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materiais';

    protected $fillable = [
        'equipamento',
        'marca',
        'quantidade',
        'unidade',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'quantidade' => 'integer',
    ];

    public function servicos()
{
    return $this->belongsToMany(\App\Models\Servico::class, 'servico_materiais', 'material_id', 'servico_id')
        ->withPivot(['quantidade_usada'])
        ->withTimestamps();
}

}
