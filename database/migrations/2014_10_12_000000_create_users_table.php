<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->timestamp('birthday')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('avatar')->nullable();
            $table->text('introduction')->nullable();
            $table->boolean('is_favorite')->nullable();
            $table->boolean('is_working')->nullable();
            $table->bigInteger('total_rate')->nullable();
            $table->bigInteger('total_star')->nullable();
            $table->float('avg_star')->nullable();
            $table->bigInteger('clicks')->nullable();
            $table->bigInteger('views')->nullable();
            $table->float('click_rate')->nullable();
            $table->boolean('is_valid')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
