<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOglasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oglas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('brand_id')->nullable()->unsigned();
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->integer('phonemodel_id')->nullable()->unsigned();
            $table->foreign('phonemodel_id')->references('id')->on('phonemodels')->onDelete('cascade');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('title');
            $table->integer('price');
            $table->integer('year');
            $table->text('description')->nullable();
            $table->integer('damaged')->default(0);
            $table->integer('new')->default(0);
            $table->integer('images')->default(0);
            $table->string('imagesfolder')->nullable();
            $table->integer('timesviewed');
            $table->integer('approved')->default(0);
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
        Schema::drop('oglas');
    }
}
