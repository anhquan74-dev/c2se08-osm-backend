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
        Schema::table('locations', function(Blueprint $table){
            $table->decimal('coords_latitude', 8 , 8 )->change();
            $table->decimal('coords_longitude', 8 , 8 )->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function(Blueprint $table){
            $table->decimal('coords_latitude')->change();
            $table->decimal('coords_longitude' )->change();
        });
    }
};
