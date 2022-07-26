<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.projects', function (Blueprint $table) {         
            $table->id();
            $table->string('projectCode', 10);
            $table->string('projectTitle', 150);
            $table->boolean('isActive')->default(0);
            $table->bigInteger('tamplateID');
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
        Schema::dropIfExists('dcs.projects');
    }
}
