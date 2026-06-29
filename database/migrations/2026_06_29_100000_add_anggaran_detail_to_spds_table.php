<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spds', function (Blueprint $table) {
            $table->bigInteger('uang_harian')->nullable()->after('anggaran');
            $table->bigInteger('uang_transport')->nullable()->after('uang_harian');
            $table->bigInteger('uang_hotel')->nullable()->after('uang_transport');
        });
    }

    public function down(): void
    {
        Schema::table('spds', function (Blueprint $table) {
            $table->dropColumn(['uang_harian', 'uang_transport', 'uang_hotel']);
        });
    }
};
