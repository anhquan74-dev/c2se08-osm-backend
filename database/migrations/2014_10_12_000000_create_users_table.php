<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->bigInteger('role_id');
            $table->timestamp('birthday');
            $table->string('gender');
            $table->string('phone_number');
            $table->string('banner_photos');
            $table->text('introduction');
            $table->boolean('favorite');
            $table->boolean('appointment_schedule');
            $table->bigInteger('total_rate');
            $table->bigInteger('total_star');
            $table->float('avg_star');
            $table->bigInteger('clicks');
            $table->bigInteger('views');
            $table->float('click_rate');
            $table->boolean('valid_flag');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
