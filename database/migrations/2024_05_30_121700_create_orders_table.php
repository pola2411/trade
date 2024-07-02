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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->double('total');
            $table->double('subtotal');
            $table->integer('interest');
            $table->longText('program_details');
            $table->string('full_account_name');
            $table->string('account_number');

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('order_status');
            $table->unsignedBigInteger('peroid_globle');
            $table->unsignedBigInteger('bank_id');
            $table->unsignedBigInteger('program_type_id');

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('order_status')->references('id')->on('order_status')->onDelete('cascade');
            $table->foreign('peroid_globle')->references('id')->on('peroid_globels')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->foreign('program_type_id')->references('id')->on('program_types')->onDelete('cascade');

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
