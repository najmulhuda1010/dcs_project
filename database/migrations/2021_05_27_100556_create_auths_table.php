<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.auths', function (Blueprint $table) {
            $table->id();
            $table->String('roleId', 5);
            $table->String('projectcode', 3);
            $table->String('processId', 20);
            $table->boolean('isAuthorized');
            $table->String('createdBy', 20);
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
        Schema::dropIfExists('dcs.auths');
    }
}
