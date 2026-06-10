<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('servicos', 'tipo_servico')) {
            return;
        }

        DB::statement("ALTER TABLE servicos MODIFY tipo_servico ENUM('instalacao', 'manutencao', 'orcamento') NULL");
    }

    public function down(): void
    {
        if (!Schema::hasColumn('servicos', 'tipo_servico')) {
            return;
        }

        DB::statement("ALTER TABLE servicos MODIFY tipo_servico ENUM('instalacao', 'manutencao') NULL");
    }
};
