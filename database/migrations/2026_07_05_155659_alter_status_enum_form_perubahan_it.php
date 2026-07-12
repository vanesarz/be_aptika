<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE form_perubahan_it
            MODIFY status ENUM(
                'menunggu',
                'disetujui',
                'pengerjaan',
                'selesai',
                'ditolak'
            )
            NOT NULL
            DEFAULT 'menunggu'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE form_perubahan_it
            MODIFY status ENUM(
                'menunggu',
                'disetujui',
                'ditolak'
            )
            NOT NULL
            DEFAULT 'menunggu'
        ");
    }
};