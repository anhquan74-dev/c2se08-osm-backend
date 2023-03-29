<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('address')->nullable();
            $table->bigInteger('province_id')->nullable();
            $table->string('province_name')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->string('district_name')->nullable();
            $table->bigInteger('ward_id')->nullable();
            $table->string('ward_name')->nullable();
            $table->decimal('coords_latitude')->nullable();
            $table->decimal('coords_longitude')->nullable();
            $table->boolean('is_primary_flag')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
};