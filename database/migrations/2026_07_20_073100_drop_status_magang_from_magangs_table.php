<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropColumn('status_magang');
        });
    }

    public function down(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->enum('status_magang', [
                'Belum mulai',
                'Sedang magang',
                'Selesai magang'
            ])->default('Belum mulai')->after('cv_magang');
        });
    }
};
