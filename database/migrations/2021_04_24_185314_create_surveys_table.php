<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.surveys', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('surveyid')->nullable();
            $table->bigInteger('entollmentid');
            $table->string('name', 20);
            $table->string('mainidtypeid', 200);
            $table->string('idno', 17);
            $table->string('phone', 11);
            $table->string('status', 50);
            $table->string('label', 50);
            $table->date('targetdate');
            $table->string('refferdbyid', 10)->nullable();
            $table->json('dynamicfieldvalue')->nullable();
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
        Schema::dropIfExists('dcs.surveys');
    }
}
