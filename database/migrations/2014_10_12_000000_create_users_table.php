<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('username')->nullable();
            $table->string('password');
            $table->string('mobile', 11)->nullable();
            $table->string('nickname', 12)->nullable();
            $table->date('last_login')->nullable();
            // 0未激活，1已激活，2禁用
            $table->integer('status')->default(0);
            $table->dateTime('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        // Schema::connection('mongodb')->create('muser', function (Blueprint $table) {
        //     $table->bigIncrements('_id');
        //     $table->string('phone');
        //     $table->string('first_name');
        //     $table->string('last_name');
        //     $table->string('address');
        //     $table->string('born_date')->nullable();
        //     $table->string('born_place')->nullable();
        //     $table->text('photo')->nullable();
        //     $table->string('npwp')->nullable();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql')->dropIfExists('user');
        // Schema::connection('pgsql')->dropIfExists('prole');
        // Schema::connection('mongodb')->dropIfExists('muser');

    }
}
