<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('telefone', 30)->nullable();
        $table->string('rua')->nullable();
        $table->string('numero', 20)->nullable();
        $table->string('bairro')->nullable();
        $table->string('cidade')->nullable();
        $table->string('uf', 2)->nullable();
        $table->timestamps();
    });
}

};
