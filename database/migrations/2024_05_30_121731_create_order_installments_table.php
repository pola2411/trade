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
        Schema::create('order_installments', function (Blueprint $table) {
            $table->id();
            $table->date('piad_date');
            $table->double('value');
            $table->tinyInteger('status')->default(false);
            $table->unsignedBigInteger('order_id');

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_installments');
    }
};
