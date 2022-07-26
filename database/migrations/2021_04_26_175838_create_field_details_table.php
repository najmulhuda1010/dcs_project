<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.field_details', function (Blueprint $table) {
            $table->id();
            $table->string('fieldNameEn',50);
            $table->string('fieldNameBn',50);
            $table->string('formName', 50);
            $table->string('dataType', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dcs.field_details');
    }
}
