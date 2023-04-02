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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained('packages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('attach_photos')->nullable();
            $table->string('note_for_provider')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('date')->nullable();
            $table->bigInteger('price')->nullable();
            $table->string('price_unit')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('complete_date')->nullable();
            $table->timestamp('cancel_date')->nullable();
            $table->bigInteger('feedback_id')->nullable();
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
        Schema::dropIfExists('appointments');
    }
};
