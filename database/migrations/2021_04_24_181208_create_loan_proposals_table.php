<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dcs.loan_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('MemberID', 10);
            $table->string('LoanProductId', 10);
            $table->string('LoanDurationId', 10);
            $table->bigInteger('InvestmentSectorId');
            $table->bigInteger('SchemeId');
            $table->double('ProposalAmount');
            $table->double('InstalmentAmount');
            $table->string('LoanInFamily', 10);
            $table->string('VOLeaderId', 10);
            $table->string('RecommenderId', 10);
            $table->string('GruantorName', 150);
            $table->string('Gphone', 11);
            $table->bigInteger('GRelationshipId');
            $table->string('Gnid', 17);
            $table->bigInteger('InsuranceTypeId');
            $table->bigInteger('InsurerOptionId');
            $table->bigInteger('SecondInsurerId');
            $table->string('ResidenceType', 50);
            $table->string('DurationOfResidence', 50);
            $table->boolean('IsHouseOwnerKnows');
            $table->integer('NoOfRelativesInPresentAddress');
            $table->string('RelativeName', 150);
            $table->string('RelativePhone', 11);
            $table->integer('JobTenure');
            $table->double('Salary');
            $table->binary('GNidFrontImg');
            $table->binary('GNidBackImg');
            $table->binary('Gimg');
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
        Schema::dropIfExists('dcs.loan_proposals');
    }
}
