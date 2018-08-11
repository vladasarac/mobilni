<?php

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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('verification')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'author'])->default('author');//ja dodo
            $table->integer('aktivan')->default(0);//ja dodo
            $table->string('grad')->nullable();
            $table->string('telefon', 20)->nullable()->unique();//ja dodo
            $table->string('telefon2', 20)->nullable();
            $table->string('telefon3', 20)->nullable();
            $table->integer('brojoglasa')->default(0);//ja dodo
            $table->integer('prikaziemail')->default(0);
            $table->integer('pravnolice')->default(0);
            $table->string('adresa')->nullable();
            $table->integer('logo')->default(0);
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->integer('zoom')->nullable();
            $table->rememberToken();
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
        Schema::drop('users');
    }
}
