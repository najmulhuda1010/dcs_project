<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.admissions', function (Blueprint $table) {
            $table->id();
            $table->boolean('IsRefferal');
            $table->string('RefferedById', 10)->nullable();
            $table->bigInteger('entollmentId');          
            $table->string('MemberId', 10);
            $table->string('MemberCateogryId', 10);
            $table->string('ApplicantsName', 150);
            $table->bigInteger('MainIdTypeId');
            $table->string('IdNo', 17);
            $table->bigInteger('OtherIdTypeId');
            $table->string('OtherIdNo', 17);
            $table->date('ExpiryDate');
            $table->string('IssuingCountry', 50);
            $table->dateTime('DOB');
            $table->string('MotherName', 150);
            $table->string('FatherName', 150);
            $table->bigInteger('EducationId');
            $table->string('Phone', 11);
            $table->string('PresentAddress', 200);
            $table->bigInteger('presentUpazilaId');
            $table->string('PermanentAddress', 200);
            $table->bigInteger('parmanentUpazilaId');
            $table->bigInteger('MaritalStatusId');
            $table->string('SpouseName', 150);
            $table->string('SpouseNidOrBid', 17);
            $table->dateTime('SposeDOB');
            $table->bigInteger('SpuseOccupationId');
            $table->bigInteger('SpouseBusinessTypeId');
            $table->string('ReffererName', 150);
            $table->string('ReffererPhone', 11);
            $table->integer('FamilyMemberNo');
            $table->integer('NoOfChildren');
            $table->dateTime('NomineeDOB');
            $table->bigInteger('RelationshipId');
            $table->binary('ApplicantCpmbinedImg');
            $table->binary('ReffererImg');
            $table->binary('ReffererIdImg');
            $table->binary('FrontSideOfIdImg');
            $table->binary('BackSideOfIdimg');
            $table->binary('NomineeIdImg');
            $table->binary('SpuseIdImg');
            $table->json('DynamicFieldValue');
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
        Schema::dropIfExists('dcs.admissions');
    }
}
