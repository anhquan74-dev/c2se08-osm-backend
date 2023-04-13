<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained('services')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('price')->nullable();
            $table->bigInteger('total_rate')->nullable();
            $table->bigInteger('total_star')->nullable();
            $table->float('avg_star')->nullable();
            $table->boolean('is_negotiable')->nullable();
            $table->bigInteger('view_priority')->nullable();
            $table->boolean('is_valid')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
};
