<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->double('value');
            $table->longText('description_ar');
            $table->longText('description_en');
            $table->unsignedBigInteger('program_type_id');
            $table->string('calender_ar')->nullable();
            $table->string('calender_en')->nullable();
            $table->string('interest_ar')->nullable();
            $table->string('interest_en')->nullable();
            $table->softDeletes();
            $table->foreign('program_type_id')->references('id')->on('program_types')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
