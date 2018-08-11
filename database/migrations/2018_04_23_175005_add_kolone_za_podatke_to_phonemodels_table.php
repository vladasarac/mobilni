<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKoloneZaPodatkeToPhonemodelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phonemodels', function (Blueprint $table) {
            $table->integer('ts')->default(1)->after('smart');
            $table->integer('year')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phonemodels', function (Blueprint $table) {
            //
            $table->dropColumn('ts');
            $table->dropColumn('year');
        });
    }
}
