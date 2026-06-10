<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            if (!Schema::hasColumn('servicos', 'tipo_servico')) {
                $table->enum('tipo_servico', ['instalacao', 'manutencao'])
                    ->nullable()
                    ->after('local_instalacao');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            if (Schema::hasColumn('servicos', 'tipo_servico')) {
                $table->dropColumn('tipo_servico');
            }
        });
    }
};
