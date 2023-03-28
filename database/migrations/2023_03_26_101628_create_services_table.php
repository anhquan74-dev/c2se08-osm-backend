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
            $table->bigInteger("avg_price");
            $table->bigInteger("max_price");
            $table->bigInteger("min_price");
            $table->boolean("is_negotiable");
            $table->bigInteger("total_rate");
            $table->bigInteger("total_star");
            $table->float("avg_star");
            $table->bigInteger("number_of_packages");
            $table->boolean("valid_flag");
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
