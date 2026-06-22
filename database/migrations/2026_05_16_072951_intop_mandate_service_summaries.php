<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intop_mandate_service_summaries', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('year')->unsigned();
            $table->enum('category', ['administrasi', 'publik'])
                ->comment('Administrasi Pemerintahan atau Layanan Publik');
            $table->string('service_name')
                ->comment('Nama layanan, contoh: Keuangan, Kepegawaian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intop_mandate_service_summaries');
    }
};
