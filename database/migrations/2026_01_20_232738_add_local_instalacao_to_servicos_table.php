<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            if (!Schema::hasColumn('servicos', 'local_instalacao')) {
                $table->string('local_instalacao')->nullable()->after('data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            if (Schema::hasColumn('servicos', 'local_instalacao')) {
                $table->dropColumn('local_instalacao');
            }
        });
    }
};
