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
        Schema::create('customer_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('field_country_id');
            $table->unsignedBigInteger('customer_id');
            $table->mediumText('value');
            $table->boolean('is_vervication')->default(false);
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('field_country_id')->references('id')->on('fields_countries')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_verifications');
    }
};
