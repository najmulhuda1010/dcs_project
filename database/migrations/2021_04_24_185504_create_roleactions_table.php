<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.roleactions', function (Blueprint $table) {
            $table->id();
            $table->integer('role');
            $table->string('actionlist');
            $table->string('receiverlist');
            $table->string('email');
            $table->string('sms');
            $table->string('push');
            $table->string('msgcontent');
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
        Schema::dropIfExists('dcs.roleactions');
    }
}
