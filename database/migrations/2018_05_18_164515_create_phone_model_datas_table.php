<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneModelDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_model_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('phonemodel_id')->unsigned();
            $table->foreign('phonemodel_id')->references('id')->on('phonemodels')->onDelete('cascade');
            $table->string('network')->nullable()->default(NULL);
            $table->string('yearreleased')->nullable()->default(NULL);
            $table->string('dimensions')->nullable()->default(NULL);
            $table->string('weight')->nullable()->default(NULL);
            $table->string('sim')->nullable()->default(NULL);
            $table->string('displaytype')->nullable()->default(NULL);
            $table->string('displaysize')->nullable()->default(NULL);
            $table->string('displayres')->nullable()->default(NULL);
            $table->string('os')->nullable()->default(NULL);
            $table->string('chipset')->nullable()->default(NULL);
            $table->string('cpu')->nullable()->default(NULL);
            $table->string('gpu')->nullable()->default(NULL);
            $table->string('cardslot')->nullable()->default(NULL);
            $table->string('internalmemory')->nullable()->default(NULL);
            $table->string('phonebook')->nullable()->default(NULL);
            $table->string('cameraprimary')->nullable()->default(NULL);
            $table->string('camerafeatures')->nullable()->default(NULL);
            $table->string('cameravideo')->nullable()->default(NULL);
            $table->string('camerasecond')->nullable()->default(NULL);
            $table->string('alerttypes')->nullable()->default(NULL);
            $table->string('loudspeaker')->nullable()->default(NULL);
            $table->string('tripetmmjack')->nullable()->default(NULL);
            $table->string('wlan')->nullable()->default(NULL);
            $table->string('bluetooth')->nullable()->default(NULL);
            $table->string('gps')->nullable()->default(NULL);
            $table->string('radio')->nullable()->default(NULL);
            $table->string('usb')->nullable()->default(NULL);
            $table->string('sensors')->nullable()->default(NULL);
            $table->string('messaging')->nullable()->default(NULL);
            $table->string('browser')->nullable()->default(NULL);
            $table->text('featuresother')->nullable()->default(NULL);
            $table->string('battery')->nullable()->default(NULL);
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
        Schema::drop('phone_model_datas');
    }
}
