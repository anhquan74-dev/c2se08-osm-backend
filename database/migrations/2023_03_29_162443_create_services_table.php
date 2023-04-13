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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('provider_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('avg_price')->nullable();
            $table->bigInteger('max_price')->nullable();
            $table->bigInteger('min_price')->nullable();
            $table->boolean('is_negotiable')->nullable();
            $table->bigInteger('total_rate')->nullable();
            $table->bigInteger('total_star')->nullable();
            $table->float('avg_star')->nullable();
            $table->bigInteger('number_of_packages')->nullable();
            $table->boolean('is_valid')->nullable();
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
        Schema::dropIfExists('services');
    }
};
