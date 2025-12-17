<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw SQL because renameColumn requires doctrine/dbal or newer MariaDB/MySQL
        // and strict RENAME COLUMN support might be missing.
        DB::statement('ALTER TABLE warehouses CHANGE address location TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE warehouses CHANGE location address TEXT NULL');
    }
};
