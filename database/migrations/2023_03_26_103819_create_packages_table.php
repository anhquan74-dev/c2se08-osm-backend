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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_id');
            $table->string('name');
            $table->text('description');
            $table->bigInteger('price');
            $table->bigInteger('total_rate');
            $table->bigInteger('total_star');
            $table->float('avg_star');
            $table->boolean('is_negotiable');
            $table->bigInteger('view_priority');
            $table->boolean('valid_flag');
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
        Schema::dropIfExists('packages');
    }
};
