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
        if (Schema::hasColumn('posts', 'image')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
        if (Schema::hasColumn('categories', 'logo')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('logo');
            });
        }
        if (Schema::hasColumn('users', 'avatar')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('avatar');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table){
            $table->string('image')->nullable();
        });
        Schema::table('categories', function (Blueprint $table){
            $table->string('logo')->nullable();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
        });
    }
};
