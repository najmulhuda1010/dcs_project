<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCelingConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.celing_configs', function (Blueprint $table) {
            $table->id();
			$table->string('projectcode', 3);
			$table->string('approver', 3);
			$table->string('growth_rate', 10);
			$table->string('limit_form', 20);
			$table->string('limit_to', 20);
			$table->string('repeat_limit_form', 20);
			$table->string('repeat_limit_to', 20);
			$table->string('createdby', 20);
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
        Schema::dropIfExists('dcs.celing_configs');
    }
}
