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
            $table->bigInteger('service_id');
            $table->bigInteger('package_id');
            $table->bigInteger('provider_id');
            $table->bigInteger('customer_id');
            $table->string('full_name');
            $table->string('phone_number');
            $table->string('attach_photos');
            $table->string('note_for_provider');
            $table->string('location');
            $table->timestamp('date');
            $table->bigInteger('price');
            $table->string('price_unit');
            $table->string('status');
            $table->timestamp('offer_date');
            $table->timestamp('complete_date');
            $table->timestamp('cancel_date');
            $table->bigInteger('feedback_id');
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
