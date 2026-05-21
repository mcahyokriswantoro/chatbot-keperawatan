<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('wilayah')) {
            return;
        }

        Schema::table('wilayah', function (Blueprint $table) {
            if ($this->foreignKeyExists('wilayah', 'wilayah_parent_kode_foreign')) {
                $table->dropForeign(['parent_kode']);
            }
        });

        Schema::table('wilayah', function (Blueprint $table) {
            if (Schema::hasColumn('wilayah', 'level')) {
                $this->dropIndexIfExists('wilayah', 'wilayah_level_index');
                $table->dropColumn('level');
            }

            if (Schema::hasColumn('wilayah', 'parent_kode')) {
                $this->dropIndexIfExists('wilayah', 'wilayah_parent_kode_index');
                $table->dropColumn('parent_kode');
            }

            if (Schema::hasColumn('wilayah', 'created_at')) {
                $table->dropTimestamps();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('wilayah')) {
            return;
        }

        Schema::table('wilayah', function (Blueprint $table) {
            if (! Schema::hasColumn('wilayah', 'parent_kode')) {
                $table->string('parent_kode', 13)->nullable()->after('nama');
            }
            if (! Schema::hasColumn('wilayah', 'level')) {
                $table->string('level', 20)->nullable()->after('parent_kode');
            }
            if (! Schema::hasColumn('wilayah', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    private function foreignKeyExists(string $table, string $name): bool
    {
        $database = Schema::getConnection()->getDatabaseName();

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $name)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        $database = Schema::getConnection()->getDatabaseName();

        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $name)
            ->exists();

        if ($exists) {
            Schema::table($table, function (Blueprint $blueprint) use ($name) {
                $blueprint->dropIndex($name);
            });
        }
    }
};
