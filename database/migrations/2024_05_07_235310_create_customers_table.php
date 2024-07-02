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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('gender', array('Male','Female'))->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable()->unique();
            $table->string('avtar',300)->default('l60Hf.png')->nullable();
            $table->longText('address')->nullable();
            $table->longText('image_id_front')->nullable();
            $table->longText('image_id_back')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_approve_id')->default(false);

            $table->date('birthday')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
