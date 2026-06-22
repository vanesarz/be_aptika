<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rekayasa_application_replications', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('service_type_id');
            $table->foreignId('service_type_id')
                ->constrained('service_types')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('institution_id')->constrained('general_institution_categories');
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->integer('total_replications')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('rekayasa_application_replications');
    }
};
