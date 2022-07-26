<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('notificationid');
            $table->string('roleid',5);
            $table->string('projectid',3);
            $table->boolean('sms')->default(0);
            $table->boolean('email')->default(0);
            $table->boolean('web')->default(0);
            $table->boolean('inApp')->default(0);
            $table->bigInteger('actionid');
            $table->string('recieverlist');
            $table->string('msgcontent');
            $table->string('createdby',50);
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
        Schema::dropIfExists('notifications');
    }
}
