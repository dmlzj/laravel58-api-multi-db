<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->table('user', function (Blueprint $table) {
            //
            $table->string('mobile', 11)->nullable();
            $table->string('nickname', 12)->nullable();
        });
        Schema::connection('mongodb')->table('user', function (Blueprint $table) {
            //
            $table->string('mobile', 11)->nullable();
            $table->string('nickname', 12)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            //
        });
    }
}
