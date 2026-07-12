<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('form_perubahan_it', function (Blueprint $table) {
        $table->text('catatan_admin')
              ->nullable();

        $table->string('dokumen_final')
              ->nullable();
    });
}

};