<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE servicos
            MODIFY COLUMN status ENUM(
                'agendado',
                'aberto',
                'em_deslocamento',
                'em_execucao',
                'finalizado',
                'cancelado'
            ) NOT NULL DEFAULT 'agendado'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE servicos
            MODIFY COLUMN status ENUM(
                'agendado',
                'aberto',
                'em_execucao',
                'finalizado',
                'cancelado'
            ) NOT NULL DEFAULT 'agendado'
        ");
    }
};
