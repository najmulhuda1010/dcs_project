<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.form_configs', function (Blueprint $table) {
            $table->id();
            $table->string('projectcode', 03);
            $table->string('formID', 50);
            $table->json('groupLabel')->nullable();
            $table->json('lebel')->nullable();
            $table->text('dataType');
            $table->json('captions')->nullable();
            $table->json('values')->nullable();
            $table->tinyInteger('columnType');
            $table->integer('displayOrder');
            $table->tinyInteger('status');
            $table->integer('groupNo')->nullable();
            $table->string('createdby', 30)->nullable();
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
        Schema::dropIfExists('dcs.form_configs');
    }
}
