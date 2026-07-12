<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('form_perubahan_it', function (Blueprint $table) {
            $table->string('assigned_to')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('form_perubahan_it', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });
    }
};
