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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('address');
            $table->bigInteger('province_id');
            $table->string('province_name');
            $table->bigInteger('district_id');
            $table->string('district_name');
            $table->bigInteger('ward_id');
            $table->string('ward_name');
            $table->decimal('coords_latitude');
            $table->decimal('coords_longitude');
            $table->boolean('primary_flag');
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
        Schema::dropIfExists('locations');
    }
};
