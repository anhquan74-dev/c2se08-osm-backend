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
	    Schema::create('images', function (Blueprint $table) {
		    $table->id();
		    $table->string('asset_type');
		    $table->string('delivery_type');
		    $table->string('public_id');
			$table->string('file_name');
		    $table->string('mime');
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
	       Schema::dropIfExists('images');
    }
};
