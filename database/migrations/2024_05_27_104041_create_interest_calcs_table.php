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
        Schema::create('interest_calcs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('period_globle_id')->unique();
            $table->double('percent');
            $table->softDeletes();
            $table->foreign('period_globle_id')->references('id')->on('peroid_globels')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interest_calcs');
    }
};
