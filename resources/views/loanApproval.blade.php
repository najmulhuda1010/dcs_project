@extends('backend.layouts.master')

@section('title','loan approval')
@section('style')
<style>
    .header-section {
        background-color: #f3eded;
    }

    .img-circle {
        border-radius: 50%;
        height: 150px;
        width: auto;
    }

    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        padding-top: 150px;
        margin-left: 120px;
        /* Location of the box */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4);
        /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        display: flex;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        font-size: 28px;
        float: right;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .member_info {
        text-align: center;
    }

    .text_align {
        text-align: center;
    }

    /* input {
        text-align: center;
    } */

    .guarantor_img {
        height: 150px;
        width: 150px;
        border-radius: 50%;
    }

    .nid_img {
        width: 230px;
        height: 140px;
    }

    .font_red {
        color: red;
    }

    .member_info table {
        background: #FB3199;
        color: #FFFFFF;
    }

    .white {
        color: white;
    }

    .bgColor {
        background: #F3F6F9;
        color: black;
    }

    .nav_bar {
        background: #FB3199;
    }

    .nav_bar .nav .nav-item .active {

        color: red;
        background-color: DarkOrange;
    }

    .nav_bar .nav .nav-item .nav-link {
        color: #fff;
    }

    .hidden {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5>
                    <!--begin::Page Title-->@lang('loan.approval_header')
                </h5>
            </div>
            <!--end::Info-->
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <div class="card card-custom">
                {{-- <div class="card-header flex-wrap py-5">
            <div class="card-title">
              <h3 class="card-label">Form </h3>
            </div>
        </div> --}}
                <div class="card-body">
                    <!--begin: Datatable-->
                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul style="margin-bottom: 0rem;">
                            @foreach ($errors as $error)
                            <li>{{$error['message'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if (Session::has('success'))
                    <div class="alert alert-success" role="success">
                        {{ Session::get('success') }}
                    </div>
                    @endif
                    @if (Session::has('error'))
                    <input type="hidden" value="check" id="tabCheck">
                    <div class="alert alert-danger" role="success">
                        {{ Session::get('error') }}
                    </div>
                    @endif

                    @php
                    $dberp = config('database.dberp');
                    $poName=DB::table($dberp.'.polist')->where('cono',$data->assignedpo)->first();
                    @endphp
                    <div>
                        <div class="title text_align">
                            @if(session('project'))
                            <?php
                            if (session('locale') == 'bn') {
                                $getproject = DB::table('dcs.projects')->where('projectTitle', session('project'))->get();
                                if ($getproject->isEmpty()) {
                                    $projectsName = '';
                                } else {
                                    $projectsName = $getproject[0]->bangla;
                                }
                            } else {
                                $projectsName = session('project');
                            }
                            ?>
                            @endif
                            <h3>@lang('loanApproval.header17')-{{$projectsName}}</h3>
                            <h6>@lang('loanApproval.header18')</h6>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="card card-custom header-section">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p>@lang('loanApproval.header1') :</p>
                                                @if(!empty($admissionData))
                                                @if($admissionData->ApplicantCpmbinedImg==null)
                                                <img src="{{ asset('images/Sample_User_Icon.png') }}" class="img-circle" alt="Applicant image">
                                                @else
                                                <img src="{{ $admissionData->ApplicantCpmbinedImg }}" class="img-circle" alt="Applicant image">
                                                @endif
                                                @else
                                                @if($admissionApi->MemberImageUrl==null)
                                                <img src="{{ asset('images/Sample_User_Icon.png') }}" class="img-circle" alt="Applicant image">
                                                @else
                                                <img src="{{ $admissionApi->MemberImageUrl }}" class="img-circle" alt="Applicant image">
                                                @endif
                                                @endif

                                            </div>
                                            <div class="col-md-8">
                                                @if(!empty($admissionData))
                                                <p>@lang('loanApproval.header2') : {{ $admissionData->ApplicantsName }}
                                                </p>
                                                <p>@lang('loanApproval.header3') : {{ $admissionData->PresentAddress }}
                                                </p>
                                                <p>@lang('loanApproval.header4') : {{ $admissionData->Phone }}</p>
                                                <p>@lang('loanApproval.header5') : {{ $admissionData->MotherName }}</p>
                                                <p>@lang('loanApproval.header6') : {{ $admissionData->FatherName }}</p>
                                                <p>@lang('loanApproval.header7') : {{ $admissionData->SpouseName }}</p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>@lang('loanApproval.header8') : <a href="{{ $admissionData->FrontSideOfIdImg }}">image</a>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p>@lang('loanApproval.header9') : <a href="{{ $admissionData->SpouseNidFront }}">image</a>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>@lang('loanApproval.header10') : <a href="{{ $admissionData->BackSideOfIdimg }}">image</a>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p>@lang('loanApproval.header11') : <a href="{{ $admissionData->SpouseNidBack }}">image</a></p>
                                                    </div>
                                                </div>
                                                @else
                                                <p>@lang('loanApproval.header2') : {{ $admissionApi->MemberName }}</p>
                                                <p>@lang('loanApproval.header3') : {{ $admissionApi->PresentAddress }}
                                                </p>
                                                <p>@lang('loanApproval.header4') : {{ $admissionApi->ContactNo }}</p>
                                                <p>@lang('loanApproval.header5') : {{ $admissionApi->MotherName }}</p>
                                                <p>@lang('loanApproval.header6') : {{ $admissionApi->FatherName }}</p>
                                                <p>@lang('loanApproval.header7') : {{ $admissionApi->SpouseName }}</p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>@lang('loanApproval.header8') : <a href="#">image</a>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p>@lang('loanApproval.header9') : <a href="#">image</a></p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>@lang('loanApproval.header10') : <a href="#">image</a>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p>@lang('loanApproval.header11') : <a href="#">image</a></p>
                                                    </div>
                                                </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-custom header-section">
                                    <div class="card-body">
                                        <p>@lang('loanApproval.header12') : {{$data->branchcode}}</p>
                                        <p>@lang('loanApproval.header13') : {{$data->orgno}}</p>
                                        <p>@lang('loanApproval.header14') : {{$poName->coname}}</p>
                                        <p>@lang('loanApproval.header15') : {{$data->assignedpo}}</p>
                                        @if($data->erp_mem_id)
                                        <a href="#" id="lastCloseLoan">@lang('loanApproval.header16')</a>
                                        <input type="hidden" value="{{ $data->erp_mem_id }}" id="erpMemId">
                                        @endif

                                    </div>
                                </div>
                                <div class="card card-custom header-section mt-5">
                                    <div class="card-body">
                                        <h6>Comments</h6>
                                        @php
                                        $db = config('database.db');
                                        $bmComment = DB::table($db.'.document_history')->select('comment')->where('doc_id',$data->id)->where('doc_type','loan')->where('projectcode',session('projectcode'))->where('roleid',1)->first();
                                        $amComment = DB::table($db.'.document_history')->select('comment')->where('doc_id',$data->id)->where('doc_type','loan')->where('projectcode',session('projectcode'))->where('roleid',2)->first();
                                        $rmComment = DB::table($db.'.document_history')->select('comment')->where('doc_id',$data->id)->where('doc_type','loan')->where('projectcode',session('projectcode'))->where('roleid',3)->first();
                                        $dmComment = DB::table($db.'.document_history')->select('comment')->where('doc_id',$data->id)->where('doc_type','loan')->where('projectcode',session('projectcode'))->where('roleid',4)->first();
                                        @endphp

                                        <p>BM:{{$bmComment}}</p>
                                        @if(session('role_designation') == 'RM')
                                        <p>AM:{{$amComment}}</p>
                                        @endif
                                        @if(session('role_designation') == 'DM')
                                        <p>AM:{{$amComment}}</p>
                                        <p>RM:{{$rmComment}}</p>
                                        @endif
                                        @if(session('role_designation') == 'HO')
                                        <p>AM:{{$amComment}}</p>
                                        <p>RM:{{$rmComment}}</p>
                                        <p>DM:{{$dmComment}}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <form action="{{route('loan_approve')}}" method="post">
                        @csrf

                        <div class="box-body ">
                            <div class="nav_bar hidden">
                                <ul class="nav  nav-pills nav-fill">
                                    <li class="nav-item active">
                                        <a class="nav-link active details1" id="details1" data-toggle="tab" href="#loanDetails">@lang('loan.approval_tab1')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="rca1" data-toggle="tab" href="#rca">@lang('loan.approval_tab2')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#recommender">@lang('loan.approval_tab3')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="csi1" data-toggle="tab" href="#csi">@lang('loan.approval_tab4')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="residence1" data-toggle="tab" href="#residence">@lang('loan.approval_tab5')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="client1" data-toggle="tab" href="#clientInfo">@lang('loan.approval_tab6')</a>
                                    </li>
                                    @if($data2!=null)
                                    <li class="nav-item">
                                        <a class="nav-link" id="more1" data-toggle="tab" href="#moreInfo">@lang('loan.approval_tab7')</a>
                                    </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link social1" id="social1" data-toggle="tab" href="#acceptiblity">@lang('loan.approval_tab8')</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="nav_bar ">
                                <ul class="nav  nav-pills nav-fill click">
                                    <li class="nav-item active">
                                        <a class="nav-link active details1" id="details1" data-toggle="tab" href="#loanDetails">@lang('loan.approval_tab1')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="rca1" data-toggle="tab" href="#rca">@lang('loan.approval_tab2')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#recommender">@lang('loan.approval_tab3')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="csi1" data-toggle="tab" href="#csi">@lang('loan.approval_tab4')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="residence1" data-toggle="tab" href="#residence">@lang('loan.approval_tab5')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="client1" data-toggle="tab" href="#clientInfo">@lang('loan.approval_tab6')</a>
                                    </li>
                                    @if($data2!=null)
                                    <li class="nav-item">
                                        <a class="nav-link" id="more1" data-toggle="tab" href="#moreInfo">@lang('loan.approval_tab7')</a>
                                    </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link social1" id="social1" data-toggle="tab" href="#acceptiblity">@lang('loan.approval_tab8')</a>
                                    </li>
                                </ul>
                            </div>
                            @php
                            $db = config('database.db');
                            @endphp

                            <div class="tab-content pre-scrollable" style="overflow-x: hidden;">
                                <div class="tab-pane active" id="loanDetails">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="6" class="bgColor">@lang('loanApproval.title3')</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="12"></td>
                                            <td>@lang('loanApproval.label1')</td>
                                            <td rowspan="12"></td>
                                            <td colspan="2">
                                                @if($data->loan_product!= null)
                                                @php
                                                if(session('projectcode') == '015')
                                                {
                                                $projectcode = "15";
                                                }
                                                elseif(session('projectcode') == '015')
                                                {
                                                $projectcode = "60";
                                                }
                                                $loan_product=DB::table($db.'.product_project_member_category')->select('productname')->where('productid',$data->loan_product)->where('projectcode',$projectcode)->first();

                                                @endphp
                                                {{$loan_product->productname}}
                                                @endif
                                            </td>
                                            <td rowspan="12"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label71')</td>
                                            <td colspan="2">{{$data->loan_duration}}</td>
                                        </tr>

                                        <tr>
                                            <td>@lang('loanApproval.label2')</td>
                                            <td colspan="2">
                                                @if($data->invest_sector!= null)
                                                @php
                                                $invest_sector=DB::table($db.'.schemem_sector_subsector')->select('sectorname')->where('sectorid',$data->invest_sector)->first();
                                                @endphp
                                                {{$invest_sector->sectorname}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label3')</td>
                                            <td colspan="2">
                                                @php
                                                $scheme=DB::table($db.'.schemem_sector_subsector')->select('productname')->where('schemeid',$data->scheme)->first();
                                                @endphp
                                                {{$scheme->productname}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label4')</td>
                                            <td colspan="2">{{$data->propos_amt}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label5')</td>
                                            <td colspan="2">
                                                {{$data->amount_inword}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label6')</td>
                                            <td colspan="2">
                                                {{$data->loan_purpose}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label7')</td>
                                            <td colspan="2">
                                                {{$data->loan_user}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label8')</td>
                                            <td colspan="2">
                                                {{$data->loan_type}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label9')</td>
                                            <td colspan="2">
                                                {{$data->brac_loancount}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label10')</td>
                                            <td colspan="2">{{$data->bracloan_family}}</td>
                                        </tr>
                                    </table>
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-4">
                                            </div>
                                            <div class="col-md-4">

                                            </div>

                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnNext float-right" id="btnNext">Next</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="rca">

                                    @if(session('role_designation')=='AM')
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="7" class="bgColor">@lang('loanApproval.title1')</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="4"></td>
                                            <td>@lang('loanApproval.label11')</td>
                                            <td rowspan="4"></td>
                                            <td colspan="3">
                                                @if($rca->primary_earner!= null)
                                                @php

                                                $primaryEarner=DB::table($db.'.payload_data')->select('data_name')->where('data_type','primaryEarner')->where('data_id',$rca->primary_earner)->first();
                                                @endphp
                                                {{$primaryEarner->data_name}}
                                                @endif
                                            </td>
                                            <td rowspan="5"></td>
                                        </tr>
                                        <tr class="text_align">
                                            <td>@lang('loanApproval.label12')</td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label16')</td>
                                            <td>{{$rca->monthlyincome_main}}</td>
                                            <td>{{$rca->bm_monthlyincome_main}}</td>
                                            @if($rca->am_monthlyincome_main!=null)
                                            <td><input class="form-control" type="text" id="all_monthlyincome_main" name="am_monthlyincome_main" value="{{$rca->am_monthlyincome_main}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_monthlyincome_main" name="am_monthlyincome_main" value="{{$rca->bm_monthlyincome_main}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label17')</td>
                                            <td>{{$rca->monthlyincome_spouse_child}}</td>
                                            <td>{{$rca->bm_monthlyincome_spouse_child}}</td>
                                            @if($rca->am_monthlyincome_spouse_child!=null)
                                            <td><input class="form-control" type="text" id="all_monthlyincome_spouse_child" name="am_monthlyincome_spouse_child" value="{{$rca->am_monthlyincome_spouse_child}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_monthlyincome_spouse_child" name="am_monthlyincome_spouse_child" value="{{$rca->bm_monthlyincome_spouse_child}}"></td>
                                            @endif

                                        </tr>
                                        {{-- <tr>
                                            <td>Income from other family</td>
                                            <td>{{$rca->monthlyincome_other}}</td>
                                        <td>{{$rca->bm_monthlyincome_other}}</td>
                                        @if($rca->am_monthlyincome_other!=null)
                                        <td><input class="form-control" type="text" id="all_monthlyincome_other" name="am_monthlyincome_other" value="{{$rca->am_monthlyincome_other}}">
                                        </td>
                                        @else
                                        <td><input class="form-control" type="text" id="all_monthlyincome_other" name="am_monthlyincome_other" value="{{$rca->bm_monthlyincome_other}}">
                                        </td>
                                        @endif

                                        </tr> --}}
                                        <tr>
                                            <th colspan="7" class="bgColor">@lang('loanApproval.title2')</th>
                                        </tr>
                                        <tr class="text_align">
                                            <td rowspan="6"></td>
                                            <td>@lang('loanApproval.label18')</td>
                                            <td rowspan="6"></td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                            <td rowspan="6"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label19')</td>
                                            <td>{{$rca->house_rent}}</td>
                                            <td>{{$rca->bm_house_rent}}</td>
                                            @if($rca->am_house_rent!=null)
                                            <td><input class="form-control" type="text" id="all_house_rent" name="am_house_rent" value="{{$rca->am_house_rent}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_house_rent" name="am_house_rent" value="{{$rca->bm_house_rent}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label20')</td>
                                            <td>{{$rca->food}}</td>
                                            <td>{{$rca->bm_food}}</td>
                                            @if($rca->am_food!=null)
                                            <td><input class="form-control" type="text" id="all_food" name="am_food" value="{{$rca->am_food}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_food" name="am_food" value="{{$rca->bm_food}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label21')</td>
                                            <td>{{$rca->education}}</td>
                                            <td>{{$rca->bm_education}}</td>
                                            @if($rca->am_education!=null)
                                            <td><input class="form-control" type="text" id="all_education" name="am_education" value="{{$rca->am_education}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_education" name="am_education" value="{{$rca->bm_education}}"></td>
                                            @endif

                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label22')</td>
                                            <td>{{$rca->medical}}</td>
                                            <td>{{$rca->bm_medical}}</td>
                                            @if($rca->am_medical!=null)
                                            <td><input class="form-control" type="text" id="all_medical" name="am_medical" value="{{$rca->am_medical}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_medical" name="am_medical" value="{{$rca->bm_medical}}"></td>
                                            @endif

                                        </tr>
                                        {{-- <tr>
                                            <td>@lang('loanApproval.label23')</td>
                                            <td>{{$rca->festive}}</td>
                                        <td>{{$rca->bm_festive}}</td>
                                        @if($rca->am_festive!=null)
                                        <td><input class="form-control" type="text" id="all_festive" name="am_festive" value="{{$rca->am_festive}}"></td>
                                        @else
                                        <td><input class="form-control" type="text" id="all_festive" name="am_festive" value="{{$rca->bm_festive}}"></td>
                                        @endif
                                        </tr> --}}
                                        {{-- <tr>
                                            <td>@lang('loanApproval.label24')</td>
                                            <td>{{$rca->utility}}</td>
                                        <td>{{$rca->bm_utility}}</td>
                                        @if($rca->am_utility!=null)
                                        <td><input class="form-control" type="text" id="all_utility" name="am_utility" value="{{$rca->am_utility}}"></td>
                                        @else
                                        <td><input class="form-control" type="text" id="all_utility" name="am_utility" value="{{$rca->bm_utility}}"></td>
                                        @endif

                                        </tr> --}}
                                        {{-- <tr>
                                            <td>Saving in other MFI/Bank/Brac</td>
                                            <td>{{$rca->saving}}</td>
                                        <td>{{$rca->bm_saving}}</td>
                                        @if($rca->am_saving!=null)
                                        <td><input class="form-control" type="text" id="all_saving" name="am_saving" value="{{$rca->am_saving}}"></td>
                                        @else
                                        <td><input class="form-control" type="text" id="all_saving" name="am_saving" value="{{$rca->bm_saving}}"></td>
                                        @endif
                                        </tr> --}}
                                        <tr>
                                            <td>@lang('loanApproval.label25')</td>
                                            <td>{{$rca->other}}</td>
                                            <td>{{$rca->bm_other}}</td>
                                            @if($rca->am_other!=null)
                                            <td><input class="form-control" type="text" id="all_other" name="am_other" value="{{$rca->am_other}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_other" name="am_other" value="{{$rca->bm_other}}"></td>
                                            @endif

                                        </tr>
                                        <tr>
                                            <th colspan="7" class="bgColor">@lang('loanApproval.title4')</th>
                                        </tr>
                                        <tr class="text_align">
                                            <td rowspan="5"></td>
                                            <td>@lang('loanApproval.label26')</td>
                                            <td rowspan="5"></td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                            <td rowspan="5"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label27')</td>
                                            <td>{{$rca->debt}}</td>
                                            <td>{{$rca->bm_debt}}</td>
                                            @if($rca->am_debt!=null)
                                            <td><input class="form-control" type="text" id="all_debt" name="am_debt" value="{{$rca->am_debt}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_debt" name="am_debt" value="{{$rca->bm_debt}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label28')</td>
                                            <td>{{$rca->monthly_cash}}</td>
                                            <td>{{$rca->bm_monthly_cash}}</td>
                                            @if($rca->am_monthly_cash!=null)
                                            <td><input class="form-control" type="text" id="all_monthly_cash" name="am_monthly_cash" value="{{$rca->am_monthly_cash}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_monthly_cash" name="am_monthly_cash" value="{{$rca->bm_monthly_cash}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label29')</td>
                                            <td>{{$rca->instal_proposloan}}</td>
                                            <td>{{$rca->bm_instal_proposloan}}</td>
                                            @if($rca->am_instal_proposloan!=null)
                                            <td><input class="form-control" type="text" id="all_instal_proposloan" name="am_instal_proposloan" value="{{$rca->am_instal_proposloan}}">
                                            </td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_instal_proposloan" name="am_instal_proposloan" value="{{$rca->bm_instal_proposloan}}">
                                            </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label30')</td>
                                            <td>

                                                @if($rca->instal_proposloan!=null and $rca->monthly_cash!=null)
                                                @php
                                                if($rca->monthly_cash !=0 and $rca->instal_proposloan !=0)
                                                {
                                                $tolerance =
                                                number_format(($rca->instal_proposloan/$rca->monthly_cash)*100,2);
                                                }
                                                else{
                                                $tolerance=0;
                                                }

                                                @endphp
                                                {{ $tolerance }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($rca->bm_instal_proposloan!=null and $rca->bm_monthly_cash!=null)
                                                @php
                                                if($rca->bm_monthly_cash !=0 and $rca->bm_instal_proposloan !=0)
                                                {
                                                $bmTolerance =
                                                number_format(($rca->bm_instal_proposloan/$rca->bm_monthly_cash)*100,2);
                                                }
                                                else
                                                {
                                                $bmTolerance=0;
                                                }

                                                @endphp
                                                {{ $bmTolerance }}
                                                @endif
                                            </td>
                                            @php
                                            $amTolerance=0;
                                            @endphp
                                            @if($rca->am_instal_proposloan!=null and $rca->am_monthly_cash!=null)
                                            @php
                                            if($rca->am_instal_proposloan !=0 and $rca->am_monthly_cash !=0)
                                            {
                                            $amTolerance =
                                            number_format(($rca->am_instal_proposloan/$rca->am_monthly_cash)*100,2);
                                            }
                                            @endphp
                                            <td>{{ $amTolerance }}</td>
                                            @elseif($rca->am_instal_proposloan=null and $rca->am_monthly_cash!=null)
                                            @php
                                            if($rca->bm_instal_proposloan !=0 and $rca->am_monthly_cash !=0)
                                            {
                                            $amTolerance =
                                            number_format(($rca->bm_instal_proposloan/$rca->am_monthly_cash)*100,2);
                                            }

                                            @endphp
                                            <td>{{ $amTolerance }}</td>
                                            @elseif($rca->am_instal_proposloan!=null and $rca->am_monthly_cash=null)
                                            @php
                                            if($rca->am_instal_proposloan !=0 and $rca->bm_monthly_cash !=0)
                                            {
                                            $amTolerance =
                                            number_format(($rca->am_instal_proposloan/$rca->bm_monthly_cash)*100,2);
                                            }

                                            @endphp
                                            <td>{{ $amTolerance }}</td>
                                            @else
                                            <td>@if($rca->bm_instal_proposloan!=null and
                                                $rca->bm_monthly_cash!=null){{ $bmTolerance }} @endif</td>
                                            @endif
                                        </tr>

                                    </table>
                                    @endif

                                    @if(session('role_designation')=='RM')
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="8" class="bgColor">@lang('loanApproval.title1')</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="4"></td>
                                            <td>@lang('loanApproval.label11')</td>
                                            <td rowspan="4"></td>
                                            <td colspan="4">
                                                @if($rca->primary_earner!= null)
                                                @php

                                                $primaryEarner=DB::table($db.'.payload_data')->select('data_name')->where('data_type','primaryEarner')->where('data_id',$rca->primary_earner)->first();
                                                @endphp
                                                {{$primaryEarner->data_name}}
                                                @endif
                                            </td>
                                            <td rowspan="5"></td>
                                        </tr>
                                        <tr class="text_align">
                                            <td>@lang('loanApproval.label12')</td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                            <td>@lang('loanApproval.label72')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label16')</td>
                                            <td>{{$rca->monthlyincome_main}}</td>
                                            <td>{{$rca->bm_monthlyincome_main}}</td>
                                            <td>{{$rca->am_monthlyincome_main}}</td>
                                            @if($rca->rm_monthlyincome_main!=null)
                                            <td><input class="form-control" type="text" id="all_monthlyincome_main" name="rm_monthlyincome_main" value="{{$rca->rm_monthlyincome_main}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_monthlyincome_main" name="rm_monthlyincome_main" value="{{$rca->am_monthlyincome_main}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label17')</td>
                                            <td>{{$rca->monthlyincome_spouse_child}}</td>
                                            <td>{{$rca->bm_monthlyincome_spouse_child}}</td>
                                            <td>{{$rca->am_monthlyincome_spouse_child}}</td>
                                            @if($rca->rm_monthlyincome_spouse_child!=null)
                                            <td><input class="form-control" type="text" id="all_monthlyincome_spouse_child" name="rm_monthlyincome_spouse_child" value="{{$rca->rm_monthlyincome_spouse_child}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_monthlyincome_spouse_child" name="rm_monthlyincome_spouse_child" value="{{$rca->am_monthlyincome_spouse_child}}"></td>
                                            @endif
                                        </tr>
                                        {{-- <tr>
                                            <td>Income from other family</td>
                                            <td>{{$rca->monthlyincome_other}}</td>
                                        <td>{{$rca->bm_monthlyincome_other}}</td>
                                        <td>{{$rca->am_monthlyincome_other}}</td>
                                        @if($rca->rm_monthlyincome_other!=null)
                                        <td><input class="form-control" type="text" id="all_monthlyincome_other" name="rm_monthlyincome_other" value="{{$rca->rm_monthlyincome_other}}">
                                        </td>
                                        @else
                                        <td><input class="form-control" type="text" id="all_monthlyincome_other" name="rm_monthlyincome_other" value="{{$rca->am_monthlyincome_other}}">
                                        </td>
                                        @endif
                                        </tr> --}}
                                        <tr>
                                            <th colspan="8" class="bgColor">@lang('loanApproval.title2')</th>
                                        </tr>
                                        <tr class="text_align">
                                            <td rowspan="6"></td>
                                            <td>@lang('loanApproval.label18')</td>
                                            <td rowspan="6"></td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                            <td>@lang('loanApproval.label72')</td>
                                            <td rowspan="6"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label19')</td>
                                            <td>{{$rca->house_rent}}</td>
                                            <td>{{$rca->bm_house_rent}}</td>
                                            <td>{{$rca->am_house_rent}}</td>
                                            @if($rca->rm_house_rent!=null)
                                            <td><input class="form-control" type="text" id="all_house_rent" name="rm_house_rent" value="{{$rca->rm_house_rent}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_house_rent" name="rm_house_rent" value="{{$rca->am_house_rent}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label20')</td>
                                            <td>{{$rca->food}}</td>
                                            <td>{{$rca->bm_food}}</td>
                                            <td>{{$rca->am_food}}</td>
                                            @if($rca->rm_food!=null)
                                            <td><input class="form-control" type="text" id="all_food" name="rm_food" value="{{$rca->rm_food}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_food" name="rm_food" value="{{$rca->am_food}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label21')</td>
                                            <td>{{$rca->education}}</td>
                                            <td>{{$rca->bm_education}}</td>
                                            <td>{{$rca->am_education}}</td>
                                            @if($rca->rm_education!=null)
                                            <td><input class="form-control" type="text" id="all_education" name="rm_education" value="{{$rca->rm_education}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_education" name="rm_education" value="{{$rca->am_education}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label22')</td>
                                            <td>{{$rca->medical}}</td>
                                            <td>{{$rca->bm_medical}}</td>
                                            <td>{{$rca->am_medical}}</td>
                                            @if($rca->rm_medical!=null)
                                            <td><input class="form-control" type="text" id="all_medical" name="rm_medical" value="{{$rca->rm_medical}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_medical" name="rm_medical" value="{{$rca->am_medical}}"></td>
                                            @endif
                                        </tr>
                                        {{-- <tr>
                                            <td>@lang('loanApproval.label23')</td>
                                            <td>{{$rca->festive}}</td>
                                        <td>{{$rca->bm_festive}}</td>
                                        <td>{{$rca->am_festive}}</td>
                                        @if($rca->rm_festive!=null)
                                        <td><input class="form-control" type="text" id="all_festive" name="rm_festive" value="{{$rca->rm_festive}}"></td>
                                        @else
                                        <td><input class="form-control" type="text" id="all_festive" name="rm_festive" value="{{$rca->am_festive}}"></td>
                                        @endif
                                        </tr> --}}
                                        {{-- <tr>
                                            <td>@lang('loanApproval.label24')</td>
                                            <td>{{$rca->utility}}</td>
                                        <td>{{$rca->bm_utility}}</td>
                                        <td>{{$rca->am_utility}}</td>
                                        @if($rca->rm_utility!=null)
                                        <td><input class="form-control" type="text" id="all_utility" name="rm_utility" value="{{$rca->rm_utility}}"></td>
                                        @else
                                        <td><input class="form-control" type="text" id="all_utility" name="rm_utility" value="{{$rca->am_utility}}"></td>
                                        @endif
                                        </tr> --}}
                                        {{-- <tr>
                                            <td>Saving in other MFI/Bank/Brac</td>
                                            <td>{{$rca->saving}}</td>
                                        <td>{{$rca->bm_saving}}</td>
                                        <td>{{$rca->am_saving}}</td>
                                        @if($rca->rm_saving!=null)
                                        <td><input class="form-control" type="text" id="all_saving" name="rm_saving" value="{{$rca->rm_saving}}"></td>
                                        @else
                                        <td><input class="form-control" type="text" id="all_saving" name="rm_saving" value="{{$rca->am_saving}}"></td>
                                        @endif
                                        </tr> --}}
                                        <tr>
                                            <td>@lang('loanApproval.label25')</td>
                                            <td>{{$rca->other}}</td>
                                            <td>{{$rca->bm_other}}</td>
                                            <td>{{$rca->am_other}}</td>
                                            @if($rca->rm_other!=null)
                                            <td><input class="form-control" type="text" id="all_other" name="rm_other" value="{{$rca->rm_other}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_other" name="rm_other" value="{{$rca->am_other}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <th colspan="8" class="bgColor">@lang('loanApproval.title4')</th>
                                        </tr>
                                        <tr class="text_align">
                                            <td rowspan="5"></td>
                                            <td>@lang('loanApproval.label26')</td>
                                            <td rowspan="5"></td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                            <td>@lang('loanApproval.label72')</td>
                                            <td rowspan="5"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label27')</td>
                                            <td>{{$rca->debt}}</td>
                                            <td>{{$rca->bm_debt}}</td>
                                            <td>{{$rca->am_debt}}</td>
                                            @if($rca->rm_debt!=null)
                                            <td><input class="form-control" type="text" id="all_debt" name="rm_debt" value="{{$rca->rm_debt}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_debt" name="rm_debt" value="{{$rca->am_debt}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label28')</td>
                                            <td>{{$rca->monthly_cash}}</td>
                                            <td>{{$rca->bm_monthly_cash}}</td>
                                            <td>{{$rca->am_monthly_cash}}</td>
                                            @if($rca->rm_monthly_cash!=null)
                                            <td><input class="form-control" type="text" id="all_monthly_cash" name="rm_monthly_cash" value="{{$rca->rm_monthly_cash}}"></td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_monthly_cash" name="rm_monthly_cash" value="{{$rca->am_monthly_cash}}"></td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label29')</td>
                                            <td>{{$rca->instal_proposloan}}</td>
                                            <td>{{$rca->bm_instal_proposloan}}</td>
                                            <td>{{$rca->am_instal_proposloan}}</td>
                                            @if($rca->rm_instal_proposloan!=null)
                                            <td><input class="form-control" type="text" id="all_instal_proposloan" name="rm_instal_proposloan" value="{{$rca->rm_instal_proposloan}}">
                                            </td>
                                            @else
                                            <td><input class="form-control" type="text" id="all_instal_proposloan" name="rm_instal_proposloan" value="{{$rca->am_instal_proposloan}}">
                                            </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label30')</td>
                                            <td>
                                                @if($rca->instal_proposloan!=null and $rca->monthly_cash!=null)
                                                @php
                                                $tolerance =
                                                number_format(($rca->instal_proposloan/$rca->monthly_cash)*100,2);
                                                @endphp
                                                {{ $tolerance }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($rca->bm_instal_proposloan!=null and $rca->bm_monthly_cash!=null)
                                                @php
                                                $bmTolerance =
                                                number_format(($rca->bm_instal_proposloan/$rca->bm_monthly_cash)*100,2);
                                                @endphp
                                                {{ $bmTolerance }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($rca->am_instal_proposloan!=null and $rca->am_monthly_cash!=null)
                                                @php
                                                $amTolerance =
                                                number_format(($rca->am_instal_proposloan/$rca->am_monthly_cash)*100,2);
                                                @endphp
                                                {{ $amTolerance }}
                                                @endif
                                            </td>
                                            @if($rca->rm_instal_proposloan!=null and $rca->rm_monthly_cash!=null)
                                            @php
                                            $rmTolerance =
                                            number_format(($rca->rm_instal_proposloan/$rca->rm_monthly_cash)*100,2);
                                            @endphp
                                            <td>{{ $rmTolerance }}</td>
                                            @elseif($rca->rm_instal_proposloan=null and $rca->rm_monthly_cash!=null)
                                            @php
                                            $rmTolerance =
                                            number_format(($rca->am_instal_proposloan/$rca->rm_monthly_cash)*100,2);
                                            @endphp
                                            <td>{{ $rmTolerance }}</td>
                                            @elseif($rca->rm_instal_proposloan!=null and $rca->rm_monthly_cash=null)
                                            @php
                                            $rmTolerance =
                                            number_format(($rca->rm_instal_proposloan/$rca->am_monthly_cash)*100,2);
                                            @endphp
                                            <td>{{ $rmTolerance }}</td>
                                            @else
                                            <td>
                                                @if($rca->am_instal_proposloan!=null and $rca->am_monthly_cash!=null)
                                                {{ $amTolerance }}
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                    </table>
                                    @endif

                                    @if(session('role_designation')=='DM' or session('role_designation')=='HO')
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="8" class="bgColor">@lang('loanApproval.title1')</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="4"></td>
                                            <td>@lang('loanApproval.label11')</td>
                                            <td rowspan="4"></td>
                                            <td colspan="4">
                                                @if($rca->primary_earner!= null)
                                                @php

                                                $primaryEarner=DB::table($db.'.payload_data')->select('data_name')->where('data_type','primaryEarner')->where('data_id',$rca->primary_earner)->first();
                                                @endphp
                                                {{$primaryEarner->data_name}}
                                                @endif
                                            </td>
                                            <td rowspan="5"></td>
                                        </tr>
                                        <tr class="text_align">
                                            <td>@lang('loanApproval.label12')</td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                            <td>@lang('loanApproval.label72')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label16')</td>
                                            <td>{{$rca->monthlyincome_main}}</td>
                                            <td>{{$rca->bm_monthlyincome_main}}</td>
                                            <td>{{$rca->am_monthlyincome_main}}</td>
                                            <td>{{$rca->rm_monthlyincome_main}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label17')</td>
                                            <td>{{$rca->monthlyincome_spouse_child}}</td>
                                            <td>{{$rca->bm_monthlyincome_spouse_child}}</td>
                                            <td>{{$rca->am_monthlyincome_spouse_child}}</td>
                                            <td>{{$rca->rm_monthlyincome_spouse_child}}</td>
                                        </tr>
                                        {{-- <tr>
                                            <td>Income from other family</td>
                                            <td>{{$rca->monthlyincome_other}}</td>
                                        <td>{{$rca->bm_monthlyincome_other}}</td>
                                        <td>{{$rca->am_monthlyincome_other}}</td>
                                        <td>{{$rca->rm_monthlyincome_other}}</td>
                                        </tr> --}}
                                        <tr>
                                            <th colspan="8" class="bgColor">@lang('loanApproval.title2')</th>
                                        </tr>
                                        <tr class="text_align">
                                            <td rowspan="6"></td>
                                            <td>@lang('loanApproval.label18')</td>
                                            <td rowspan="6"></td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                            <td>@lang('loanApproval.label72')</td>
                                            <td rowspan="6"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label19')</td>
                                            <td>{{$rca->house_rent}}</td>
                                            <td>{{$rca->bm_house_rent}}</td>
                                            <td>{{$rca->am_house_rent}}</td>
                                            <td>{{$rca->rm_house_rent}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label20')</td>
                                            <td>{{$rca->food}}</td>
                                            <td>{{$rca->bm_food}}</td>
                                            <td>{{$rca->am_food}}</td>
                                            <td>{{$rca->rm_food}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label21')</td>
                                            <td>{{$rca->education}}</td>
                                            <td>{{$rca->bm_education}}</td>
                                            <td>{{$rca->am_education}}</td>
                                            <td>{{$rca->rm_education}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label22')</td>
                                            <td>{{$rca->medical}}</td>
                                            <td>{{$rca->bm_medical}}</td>
                                            <td>{{$rca->am_medical}}</td>
                                            <td>{{$rca->rm_medical}}</td>
                                        </tr>
                                        {{-- <tr>
                                            <td>@lang('loanApproval.label23')</td>
                                            <td>{{$rca->festive}}</td>
                                        <td>{{$rca->bm_festive}}</td>
                                        <td>{{$rca->am_festive}}</td>
                                        <td>{{$rca->rm_festive}}</td>
                                        </tr> --}}
                                        {{-- <tr>
                                            <td>@lang('loanApproval.label24')</td>
                                            <td>{{$rca->utility}}</td>
                                        <td>{{$rca->bm_utility}}</td>
                                        <td>{{$rca->am_utility}}</td>
                                        <td>{{$rca->rm_utility}}</td>
                                        </tr> --}}
                                        {{-- <tr>
                                            <td>Saving in other MFI/Bank/Brac</td>
                                            <td>{{$rca->saving}}</td>
                                        <td>{{$rca->bm_saving}}</td>
                                        <td>{{$rca->am_saving}}</td>
                                        <td>{{$rca->rm_saving}}</td>
                                        </tr> --}}
                                        <tr>
                                            <td>@lang('loanApproval.label25')</td>
                                            <td>{{$rca->other}}</td>
                                            <td>{{$rca->bm_other}}</td>
                                            <td>{{$rca->am_other}}</td>
                                            <td>{{$rca->rm_other}}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="8" class="bgColor">@lang('loanApproval.title4')</th>
                                        </tr>
                                        <tr class="text_align">
                                            <td rowspan="5"></td>
                                            <td>@lang('loanApproval.label26')</td>
                                            <td rowspan="5"></td>
                                            <td>@lang('loanApproval.label13')</td>
                                            <td>@lang('loanApproval.label14')</td>
                                            <td>@lang('loanApproval.label15')</td>
                                            <td>@lang('loanApproval.label72')</td>
                                            <td rowspan="5"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label27')</td>
                                            <td>{{$rca->debt}}</td>
                                            <td>{{$rca->bm_debt}}</td>
                                            <td>{{$rca->am_debt}}</td>
                                            <td>{{$rca->rm_debt}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label28')</td>
                                            <td>{{$rca->monthly_cash}}</td>
                                            <td>{{$rca->bm_monthly_cash}}</td>
                                            <td>{{$rca->am_monthly_cash}}</td>
                                            <td>{{$rca->rm_monthly_cash}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label29')</td>
                                            <td>{{$rca->instal_proposloan}}</td>
                                            <td>{{$rca->bm_instal_proposloan}}</td>
                                            <td>{{$rca->am_instal_proposloan}}</td>
                                            <td>{{$rca->rm_instal_proposloan}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label30')</td>
                                            <td>
                                                @if($rca->instal_proposloan!=null and $rca->monthly_cash!=null)
                                                @php
                                                $tolerance =
                                                number_format(($rca->instal_proposloan/$rca->monthly_cash)*100,2);
                                                @endphp
                                                {{ $tolerance }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($rca->bm_instal_proposloan!=null and $rca->bm_monthly_cash!=null)
                                                @php
                                                $bmTolerance =
                                                number_format(($rca->bm_instal_proposloan/$rca->bm_monthly_cash)*100,2);
                                                @endphp
                                                {{ $bmTolerance }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($rca->am_instal_proposloan!=null and $rca->am_monthly_cash!=null)
                                                @php
                                                $amTolerance =
                                                number_format(($rca->am_instal_proposloan/$rca->am_monthly_cash)*100,2);
                                                @endphp
                                                {{ $amTolerance }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($rca->rm_instal_proposloan!=null and $rca->rm_monthly_cash!=null)
                                                @php
                                                $rmTolerance =
                                                number_format(($rca->rm_instal_proposloan/$rca->rm_monthly_cash)*100,2);
                                                @endphp
                                                {{ $rmTolerance }}
                                                @endif
                                            </td>
                                        </tr>

                                    </table>
                                    @endif
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            <div class="col-md-4">

                                            </div>

                                            <div class="col-md-4">
                                                {{-- <a href="#recommender" id="recommender2" data-toggle="tab" class="btn btn-secondary float-right">Next</a> --}}
                                                <a class="btn btn-primary btnNext float-right">Next</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="tab-pane" id="recommender">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="6" class="bgColor">@lang('loanApproval.title5')</th>
                                        </tr>

                                        <tr>
                                            <td rowspan="2"></td>
                                            <td>@lang('loanApproval.label31')</td>
                                            <td rowspan="2"></td>
                                            <td colspan="2">{{$data->vo_leader}}</td>
                                            <td rowspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label32')</td>
                                            <td colspan="2">{{$data->recommender}}</td>
                                        </tr>

                                        <tr>
                                            <th colspan="6" class="bgColor">@lang('loanApproval.label33')</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="8"></td>
                                            <td>@lang('loanApproval.label34')</td>
                                            <td rowspan="8"></td>
                                            <td colspan="2">{{$data->grntor_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label35')</td>
                                            <td colspan="2">{{$data->grntor_phone}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label36')</td>
                                            <td colspan="2">
                                                @if($data->grntor_rlationClient!= null)
                                                @php
                                                $grntor_relation=DB::table($db.'.payload_data')->select('data_name')->where('data_type','relationshipId')->where('data_id',$data->grntor_rlationClient)->first();
                                                @endphp
                                                {{$grntor_relation->data_name}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label37')</td>
                                            <td colspan="2">{{$data->grntor_nid}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label38')</td>
                                            <td colspan="2">
                                                @if($data->witness_knows== '0')
                                                {{"No"}}
                                                @elseif($data->witness_knows== '1')
                                                {{"Yes"}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label39')</td>
                                            <td colspan="2">
                                                @if($data->grantor_nidfront_photo)
                                                <img class="nid_img" src="{{$data->grantor_nidfront_photo}}" alt="">
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label40')</td>
                                            <td colspan="2">
                                                @if($data->grantor_nidback_photo)
                                                <img class="nid_img" src="{{$data->grantor_nidback_photo}}" alt="">
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label41')</td>
                                            <td colspan="2">
                                                @if($data->grantor_photo)
                                                <img class="guarantor_img" src="{{$data->grantor_photo}}" alt="">
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            <div class="col-md-4">

                                            </div>

                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnNext float-right" id="btnNext">Next</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="csi">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="6" class="bgColor">@lang('loanApproval.title6')</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="10"></td>
                                            <td>@lang('loanApproval.label42')</td>
                                            <td rowspan="10"></td>
                                            <td colspan="2">
                                                @if($data->insurn_type== '1')
                                                {{"Single"}}
                                                @elseif($data->insurn_type== '2')
                                                {{"Double"}}
                                                @endif
                                            </td>
                                            <td rowspan="10"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label43')</td>
                                            <td colspan="2">
                                                @if($data->insurn_option== '1')
                                                {{"Existing"}}
                                                @elseif($data->insurn_option== '2')
                                                {{"New"}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label44')</td>
                                            <td colspan="2">{{$data->insurn_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label45')</td>
                                            <td colspan="2">{{$data->insurn_dob}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label46')</td>
                                            <td colspan="2">
                                                {{$data->insurn_mainID}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label47')</td>
                                            <td colspan="2">
                                                @if($data->insurn_gender!= null)
                                                @php
                                                $insurn_gender=DB::table($db.'.payload_data')->select('data_name')->where('data_type','genderId')->where('data_id',$data->insurn_gender)->first();
                                                @endphp
                                                {{$insurn_gender->data_name}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label48')</td>
                                            <td colspan="2">
                                                @if($data->insurn_relation!= null)
                                                @php
                                                $insurn_relation=DB::table($db.'.payload_data')->select('data_name')->where('data_type','relationshipId')->where('data_id',$data->insurn_relation)->first();
                                                @endphp
                                                {{$insurn_relation->data_name}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label49')</td>
                                            <td colspan="2">
                                                {{$data->insurn_spouseName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label50')</td>
                                            <td colspan="2">{{$data->insurn_spouseNid}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label51')</td>
                                            <td colspan="2">{{$data->insurn_spouseDob}}</td>
                                        </tr>
                                    </table>
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            <div class="col-md-4">

                                            </div>

                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnNext float-right">Next</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="residence">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="6" class="bgColor">@lang('loanApproval.title7')</th>
                                        </tr>

                                        <tr>
                                            <td rowspan="6"></td>
                                            <td>@lang('loanApproval.label52')</td>
                                            <td rowspan="6"></td>
                                            <td colspan="2">{{$data->residence_type}}</td>
                                            <td rowspan="6"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label53')</td>
                                            <td colspan="2">{{$data->residence_duration}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label54')</td>
                                            <td colspan="2">{{$data->houseowner_knows}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label55')</td>
                                            <td colspan="2">{{$data->reltive_presAddress}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label56')</td>
                                            <td colspan="2">{{$data->reltive_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label57')</td>
                                            <td colspan="2">{{$data->reltive_phone}}</td>
                                        </tr>
                                    </table>
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            <div class="col-md-4">

                                            </div>
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnNext float-right">Next</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @if($admissionApi==null)
                                <div class="tab-pane" id="clientInfo">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="6" class="bgColor">Client Information</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="32"></td>
                                            <td>@lang('admissionApproval.label1')</td>
                                            <td rowspan="32"></td>
                                            <td colspan="2">
                                                {{$admissionData->ApplicantsName}}
                                            </td>
                                            <td rowspan="32"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label2')</td>
                                            <td colspan="2">
                                                @if($admissionData->MainIdTypeId!= null)
                                                @php
                                                $idType=DB::table($db.'.payload_data')->select('data_name')->where('data_type','cardTypeId')->where('data_id',$admissionData->MainIdTypeId)->first();
                                                @endphp
                                                {{$idType->data_name}}
                                                @endif
                                                <!-- {{$admissionData->MainIdTypeId}} -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label27')</td>
                                            <td colspan="2">
                                                {{$admissionData->IdNo}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label3')</td>
                                            <td colspan="2">
                                                {{$admissionData->DOB}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label4')</td>
                                            <td colspan="2">
                                                {{$admissionData->MotherName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label5')</td>
                                            <td colspan="2">
                                                {{$admissionData->FatherName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label6')</td>
                                            <td colspan="2">
                                                @if($admissionData->EducationId!= null)
                                                @php
                                                $education=DB::table($db.'.payload_data')->select('data_name')->where('data_type','educationId')->where('data_id',$admissionData->EducationId)->first();
                                                @endphp
                                                {{$education->data_name}}
                                                @endif
                                                <!-- {{$admissionData->EducationId}} -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label7')</td>
                                            <td colspan="2">
                                                @if($admissionData->Occupation!= null)
                                                @php
                                                $occupation=DB::table($db.'.payload_data')->select('data_name')->where('data_type','occupationId')->where('data_id',$admissionData->Occupation)->first();
                                                @endphp
                                                {{$occupation->data_name}}
                                                @endif
                                                <!-- {{$admissionData->Occupation}} -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label8')</td>
                                            <td colspan="2">
                                                {{$admissionData->Phone}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label9') </td>
                                            <td colspan="2">
                                                {{$admissionData->PresentAddress}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label10')</td>
                                            <td colspan="2">
                                                {{$admissionData->PermanentAddress }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label11')</td>
                                            <td colspan="2">
                                                @if($admissionData->MaritalStatusId!= null)
                                                @php
                                                $maritStatus=DB::table($db.'.payload_data')->select('data_name')->where('data_type','maritalStatusId')->where('data_id',$admissionData->MaritalStatusId)->first();
                                                @endphp
                                                {{$maritStatus->data_name}}
                                                @endif
                                                <!-- {{$admissionData->MaritalStatusId}} -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label12')</td>
                                            <td colspan="2">
                                                {{$admissionData->SpouseName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label28')</td>
                                            <td colspan="2">
                                                {{$admissionData->SpouseNidOrBid}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label13')</td>
                                            <td colspan="2">
                                                @if($admissionData->SpuseOccupationId!= null)
                                                @php
                                                $spuseOccupation=DB::table($db.'.payload_data')->select('data_name')->where('data_type','occupationId')->where('data_id',$admissionData->SpuseOccupationId)->first();
                                                @endphp
                                                {{$spuseOccupation->data_name}}
                                                @endif
                                                <!-- {{$admissionData->SpuseOccupationId}} -->
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>@lang('admissionApproval.label14')</td>
                                            <td colspan="2">
                                                {{$admissionData->FamilyMemberNo}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label15')</td>
                                            <td colspan="2">
                                                {{$admissionData->NoOfChildren}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>@lang('admissionApproval.label16')</td>
                                            <td colspan="2">
                                                {{$admissionData->NomineeName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label17')</td>
                                            <td colspan="2">
                                                {{$admissionData->NomineeDOB}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label18')</td>
                                            <td colspan="2">
                                                @if($admissionData->RelationshipId!= null)
                                                @php
                                                $relation=DB::table($db.'.payload_data')->select('data_name')->where('data_type','relationshipId')->where('data_id',$admissionData->RelationshipId)->first();
                                                @endphp
                                                {{$relation->data_name}}
                                                @endif
                                                <!-- {{$admissionData->RelationshipId}} -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label19')</td>
                                            <td colspan="2">
                                                @if($admissionData->IsBkash== '1')
                                                {{"Yes"}}
                                                @else
                                                {{"No"}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label20')</td>
                                            <td colspan="2">
                                                {{$admissionData->WalletNo}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label21')</td>
                                            <td colspan="2">
                                                @if($admissionData->WalletOwner!= null)
                                                @php
                                                $WalletOwner=DB::table($db.'.payload_data')->select('data_name')->where('data_type','primaryEarner')->where('data_id',$admissionData->WalletOwner)->first();
                                                @endphp
                                                {{$WalletOwner->data_name}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label25')</td>
                                            <td colspan="2">
                                                {{$admissionData->ReffererName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label26')</td>
                                            <td colspan="2">
                                                {{$admissionData->ReffererPhone}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label22')</td>
                                            <td colspan="2">
                                                @if($admissionData->ReffererIdImg)
                                                <img class="guarantor_img " src="{{$admissionData->ReffererIdImg}}" alt="Refferer Picture">
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label23')</td>
                                            <td colspan="2">
                                                @if($admissionData->ApplicantCpmbinedImg)
                                                <img class="guarantor_img " src="{{$admissionData->ApplicantCpmbinedImg}}" alt="Combine Picture">
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label24')</td>
                                            <td colspan="2">
                                                @if($admissionData->NomineeIdImg)
                                                <img class="guarantor_img " src="{{$admissionData->NomineeIdImg}}" alt="Nominee's Images">
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            <div class="col-md-4">

                                            </div>

                                            @if($data2!=null)
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            @else
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnNext float-right" id="btnNext">Next</a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="tab-pane" id="clientInfo">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="6" class="bgColor">Client Information</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="32"></td>
                                            <td>@lang('admissionApproval.label1')</td>
                                            <td rowspan="32"></td>
                                            <td colspan="2">
                                                {{$admissionApi->MemberName}}
                                            </td>
                                            <td rowspan="32"></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label2')</td>
                                            <td colspan="2">
                                                @if($admissionApi->MemberIDCard!= null)
                                                @php
                                                $idType=DB::table($db.'.payload_data')->select('data_name')->where('data_type','cardTypeId')->where('data_id',$admissionApi->MemberIDCard->CardTypeId)->first();
                                                @endphp
                                                {{$idType->data_name}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label27')</td>
                                            <td colspan="2">
                                                {{$admissionApi->MemberIDCard->IdCardNo}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label3')</td>
                                            <td colspan="2">
                                                {{$admissionApi->DateOfBirth}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label4')</td>
                                            <td colspan="2">
                                                {{$admissionApi->MotherName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label5')</td>
                                            <td colspan="2">
                                                {{$admissionApi->FatherName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label6')</td>
                                            <td colspan="2">

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label7')</td>
                                            <td colspan="2">
                                                @if($admissionApi->OccupationId!= null)
                                                @php
                                                $occupation=DB::table($db.'.payload_data')->select('data_name')->where('data_type','occupationId')->where('data_id',$admissionApi->OccupationId)->first();
                                                @endphp
                                                {{$occupation->data_name}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label8')</td>
                                            <td colspan="2">
                                                {{$admissionApi->ContactNo}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label9') </td>
                                            <td colspan="2">
                                                {{$admissionApi->PresentAddress}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label10')</td>
                                            <td colspan="2">
                                                {{$admissionApi->PermanentAddress }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label11')</td>
                                            <td colspan="2">
                                                @if($admissionApi->MaritalStatusId!= null)
                                                @php
                                                $maritStatus=DB::table($db.'.payload_data')->select('data_name')->where('data_type','maritalStatusId')->where('data_id',$admissionApi->MaritalStatusId)->first();
                                                @endphp
                                                {{$maritStatus->data_name}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label12')</td>
                                            <td colspan="2">
                                                {{$admissionApi->SpouseName}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label28')</td>
                                            <td colspan="2">
                                                @if($admissionApi->SpouseIDCard !=null)

                                                {{$admissionApi->SpouseIDCard->IdCardNo}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label13')</td>
                                            <td colspan="2">

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>@lang('admissionApproval.label14')</td>
                                            <td colspan="2">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label15')</td>
                                            <td colspan="2">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>@lang('admissionApproval.label16')</td>
                                            <td colspan="2">
                                                @if($admissionApi->Nominees!= null)
                                                {{$admissionApi->Nominees[0]->NomineesName}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label17')</td>
                                            <td colspan="2">
                                                @if($admissionApi->Nominees!= null)
                                                {{$admissionApi->Nominees[0]->DateOfBirth}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label18')</td>
                                            <td colspan="2">
                                                @if($admissionApi->Nominees!= null)
                                                @php
                                                $relation=DB::table($db.'.payload_data')->select('data_name')->where('data_type','relationshipId')->where('data_id',$admissionApi->Nominees[0]->RelationshipId)->first();
                                                @endphp
                                                {{$relation->data_name}}
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>@lang('admissionApproval.label20')</td>
                                            <td colspan="2">
                                                {{$admissionApi->BkashWalletNo}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label21')</td>
                                            <td colspan="2">

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label25')</td>
                                            <td colspan="2">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label26')</td>
                                            <td colspan="2">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label22')</td>
                                            <td colspan="2">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label23')</td>
                                            <td colspan="2">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('admissionApproval.label24')</td>
                                            <td colspan="2">
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            <div class="col-md-4">

                                            </div>

                                            @if($data2!=null)
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            @else
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnNext float-right" id="btnNext">Next</a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($data2!=null)
                                <div class="tab-pane" id="moreInfo">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="6" class="bgColor">More Information</th>
                                        </tr>
                                        <tr class="brac-color-pink">
                                            <th>Field Name</th>
                                            <th>Field Value</th>
                                        </tr>
                                        @foreach($data2 as $row)
                                        <tr>
                                            <td>{{$row->fieldName}}</td>
                                            <td>{{$row->fieldValue}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="6"></th>
                                        </tr>
                                    </table>
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnPrevious">Previous</a>
                                            </div>
                                            <div class="col-md-4">

                                            </div>
                                            <div class="col-md-4">
                                                <a class="btn btn-primary btnNext float-right">Next</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="tab-pane" id="acceptiblity">
                                    <table class="table table-bordered">
                                        <tr class="font_red">
                                            <th colspan="6">@lang('loanApproval.title8')</th>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label58')</td>
                                            <td colspan="2">
                                                {{$data->bm_noofChild}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label59')</td>
                                            <td colspan="2">
                                                {{$data->bm_earningMember}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label60')</td>
                                            <td colspan="2">
                                                {{$data->bm_duration}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label61')</td>
                                            <td colspan="2">
                                                @if($data->bm_hometown== '1')
                                                Yes
                                                @elseif($data->bm_hometown== '0')
                                                No
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label62')</td>
                                            <td colspan="2">
                                                @if($data->bm_landloard== '1')
                                                Yes
                                                @elseif($data->bm_landloard== '0')
                                                No
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label63')</td>
                                            <td colspan="2">
                                                @if($data->bm_recomand== '1')
                                                Yes
                                                @elseif($data->bm_recomand== '0')
                                                No
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label64')</td>
                                            <td colspan="2">
                                                @if($data->bm_occupation== '1')
                                                Yes
                                                @elseif($data->bm_occupation== '0')
                                                No
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label65')</td>
                                            <td colspan="2">
                                                @if($data->bm_aware== '1')
                                                Yes
                                                @elseif($data->bm_aware== '0')
                                                No
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label66')</td>
                                            <td colspan="2">
                                                @if($data->bm_grantor== '1')
                                                Yes
                                                @elseif($data->bm_grantor== '0')
                                                No
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label67')</td>
                                            <td colspan="2">{{$data->bm_socialAcecptRating}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label68')</td>
                                            <td colspan="2">{{$data->bm_grantorRating}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label69')</td>
                                            <td colspan="2">{{$data->bm_remarks}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('loanApproval.label70')</td>
                                            <td colspan="2">
                                                @if($data->bm_clienthouse)
                                                <img src="{{$data->bm_clienthouse}}" alt="house">
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    <input type="hidden" value="{{$data->id}}" name="id">
                                    <input type="hidden" value="Approve" name="action">
                                    @php
                                    $authData =
                                    DB::table($db.'.auths')->where('roleId',session('roll'))->where('processId','3')->where('projectcode',session('projectcode'))->first();
                                    $authorization = $authData->isAuthorized;
                                    @endphp
                                    @if($authorization == true and $data->reciverrole==session('roll') and
                                    $data->status=='1')
                                    <div class="box-footer">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <a class="btn btn-danger btn-block" id="reject" href="#">Reject</a>
                                            </div>
                                            <div class="col-md-3">
                                                <a class="btn btn-primary btn-block" id="sendback" href="#">Send
                                                    Back</a>
                                            </div>
                                            <div class="col-md-3">
                                                <a class="btn btn-secondary btn-block" id="recommend" href="#">Recommend</a>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" id="approve" class="btn btn-success btn-block">Approve</button>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <a class="btn btn-primary btnPrevious">Previous</a>
                                    @endif
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>

                    <!--end: Datatable-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
</div>

<!-- The reject Modal -->
<div id="reject_modal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div>
            <span class="close">&times;</span>
        </div>
        <div>
            <form id="action_btn" action="{{ route('action_btn') }}" method="post">
                @csrf
                <input type="hidden" value="{{$data->id}}" name="id">
                <div id="action"></div>
                <div class="box-body">
                    <h5>Comment:</h5>
                    <div class="form-group">
                        <textarea name="comment" id="comment" class="form-control" cols="30" rows="5"></textarea>
                    </div>
                    <div>
                        <input type="hidden" value="" name="all_monthlyincome_main1" id="all_monthlyincome_main1">
                        <input type="hidden" value="" name="all_monthlyincome_spouse_child1" id="all_monthlyincome_spouse_child1">
                        <input type="hidden" value="" name="all_monthlyincome_other1" id="all_monthlyincome_other1">
                        <input type="hidden" value="" name="all_house_rent1" id="all_house_rent1">
                        <input type="hidden" value="" name="all_food1" id="all_food1">
                        <input type="hidden" value="" name="all_education1" id="all_education1">
                        <input type="hidden" value="" name="all_medical1" id="all_medical1">
                        <input type="hidden" value="" name="all_festive1" id="all_festive1">
                        <input type="hidden" value="" name="all_utility1" id="all_utility1">
                        <input type="hidden" value="" name="all_saving1" id="all_saving1">
                        <input type="hidden" value="" name="all_other1" id="all_other1">
                        <input type="hidden" value="" name="all_debt1" id="all_debt1">
                        <input type="hidden" value="" name="all_monthly_cash1" id="all_monthly_cash1">
                        <input type="hidden" value="" name="all_instal_proposloan1" id="all_instal_proposloan1">
                    </div>
                </div><!-- /.box-body -->
                <div class="form-footer">
                    <div class="row">
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-secondary btn-block">Submit</button>
                        </div>
                        <div class="col-md-4">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- The close loan Modal -->
<div id="closeLoanModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div>
            <span class="close">&times;</span>
        </div>
        <div>
            <h4 class="mb-4">@lang('loanApproval.header16')</h4>
            <div class="row">
                <div class="col-md-6">
                    <p>@lang('loanApproval.closeLoan1'): <span id="loanNo"></span></p>
                    <p>@lang('loanApproval.closeLoan2'): <span id="installmentAmount"></span></p>
                </div>
                <div class="col-md-6">
                    <p>@lang('loanApproval.closeLoan3'): <span id="disburseDate"></span></p>
                    <p>@lang('loanApproval.closeLoan4'): <span id="disbursedAmount"></span></p>
                </div>
            </div>
            <table style="text-align: center;" class="table table-bordered">
                <thead>
                    <tr class="brac-color">
                        <th>@lang('loanApproval.closeLoan5')</th>
                        <th>@lang('loanApproval.closeLoan6')</th>
                        <th>@lang('loanApproval.closeLoan7')</th>
                    </tr>
                </thead>
                <tbody id="closeLoanTable">
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')

<script>
    $(document).ready(function() {

        $('.click').click(false);
        $('.btnNext').click(function() {
            $('.nav-pills .active').parent().next('li').find('a').trigger('click');
            $('.click .active').parent().next('li').find('a').addClass('active');
            $('.click .active').parent().prev('li').find('a').removeClass('active');
        });

        $('.btnPrevious').click(function() {
            $('.nav-pills .active').parent().prev('li').find('a').trigger('click');
            $('.click .active').parent().prev('li').find('a').addClass('active');
            $('.click .active').parent().next('li').find('a').removeClass('active');
        });


        if ($('#tabCheck').val() == 'check') {
            $("li .active").removeClass("active");
            $('.social1').addClass('active');
            $("#loanDetails").removeClass("active");
            $('#acceptiblity').addClass('active');
        }

        $('#reject').on('click', function() {
            document.querySelector('#reject_modal').style.display = 'block';
            $("#action").append(`<input type="hidden" value="Reject" name="action">`);

        })
        $('#sendback').on('click', function() {
            document.querySelector('#reject_modal').style.display = 'block';
            $("#action").append(`<input type="hidden" value="Sendback" name="action">`);
            let all_monthlyincome_main = $('#all_monthlyincome_main').val();
            $("#all_monthlyincome_main1").val(all_monthlyincome_main);
            let all_monthlyincome_spouse_child = $('#all_monthlyincome_spouse_child').val();
            $("#all_monthlyincome_spouse_child1").val(all_monthlyincome_spouse_child);
            let all_monthlyincome_other = $('#all_monthlyincome_other').val();
            $("#all_monthlyincome_other1").val(all_monthlyincome_other);
            let all_house_rent = $('#all_house_rent').val();
            $("#all_house_rent1").val(all_house_rent);
            let all_food = $('#all_food').val();
            $("#all_food1").val(all_food);
            let all_education = $('#all_education').val();
            $("#all_education1").val(all_education);
            let all_medical = $('#all_medical').val();
            $("#all_medical1").val(all_medical);
            let all_festive = $('#all_festive').val();
            $("#all_festive1").val(all_festive);
            let all_utility = $('#all_utility').val();
            $("#all_utility1").val(all_utility);
            let all_saving = $('#all_saving').val();
            $("#all_saving1").val(all_saving);
            let all_other = $('#all_other').val();
            $("#all_other1").val(all_other);
            let all_debt = $('#all_debt').val();
            $("#all_debt1").val(all_debt);
            let all_monthly_cash = $('#all_monthly_cash').val();
            $("#all_monthly_cash1").val(all_monthly_cash);
            let all_instal_proposloan = $('#all_instal_proposloan').val();
            $("#all_instal_proposloan1").val(all_instal_proposloan);
        })
        $('#recommend').on('click', function() {
            document.querySelector('#reject_modal').style.display = 'block';
            $("#action").append(`<input type="hidden" value="Recommend" name="action">`);
            let all_monthlyincome_main = $('#all_monthlyincome_main').val();
            $("#all_monthlyincome_main1").val(all_monthlyincome_main);
            let all_monthlyincome_spouse_child = $('#all_monthlyincome_spouse_child').val();
            $("#all_monthlyincome_spouse_child1").val(all_monthlyincome_spouse_child);
            let all_monthlyincome_other = $('#all_monthlyincome_other').val();
            $("#all_monthlyincome_other1").val(all_monthlyincome_other);
            let all_house_rent = $('#all_house_rent').val();
            $("#all_house_rent1").val(all_house_rent);
            let all_food = $('#all_food').val();
            $("#all_food1").val(all_food);
            let all_education = $('#all_education').val();
            $("#all_education1").val(all_education);
            let all_medical = $('#all_medical').val();
            $("#all_medical1").val(all_medical);
            let all_festive = $('#all_festive').val();
            $("#all_festive1").val(all_festive);
            let all_utility = $('#all_utility').val();
            $("#all_utility1").val(all_utility);
            let all_saving = $('#all_saving').val();
            $("#all_saving1").val(all_saving);
            let all_other = $('#all_other').val();
            $("#all_other1").val(all_other);
            let all_debt = $('#all_debt').val();
            $("#all_debt1").val(all_debt);
            let all_monthly_cash = $('#all_monthly_cash').val();
            $("#all_monthly_cash1").val(all_monthly_cash);
            let all_instal_proposloan = $('#all_instal_proposloan').val();
            $("#all_instal_proposloan1").val(all_instal_proposloan);
        })
        $('.close').on('click', function() {
            document.querySelector('#reject_modal').style.display = 'none';
        })

        $('#lastCloseLoan').on('click', function() {
            document.querySelector('#closeLoanModal').style.display = 'block';
            var memId = $('#erpMemId').val();
            $.ajax({
                url: "{{url('operation/closeLoan')}}",
                type: 'Get',
                data: {
                    "_token": "{{ csrf_token() }}",
                    memId: memId
                },
                success: function(data) {
                    console.log(data);

                    $("#closeLoanTable").empty();

                    if (data['message'] == "No data found") {
                        $("#closeLoanTable").append(`<tr id="table_row">
                                        <td colspan="4"> <p style="text-align:center;">Data not found</p></td
                                        </tr>`)
                    } else {
                        var closeLoanData = data['data'][0];


                        $("#loanNo").append(closeLoanData['LoanNo']);
                        $("#installmentAmount").append(closeLoanData['InstallmentAmount']);
                        $("#disburseDate").append(closeLoanData['DisburseDate']);
                        $("#disbursedAmount").append(closeLoanData['DisbursedAmount']);
                        $.each(closeLoanData['Collections'], function(key, value) {
                            if (value.CollectionMethod == 1) {
                                var colMethod = "Cash";
                            } else if (value.CollectionMethod == 5) {
                                var colMethod = "bKash";
                            } else if (value.CollectionMethod == 6) {
                                var colMethod = "Bank";
                            } else if (value.CollectionMethod == 3) {
                                var colMethod = "Journal";
                            }
                            $("#closeLoanTable").append(`<tr>
                            <td>
                                ` + value.CollectionAmount + `
                            </td>
                            <td>
                                ` + value.CollectionDate + `
                            </td>
                            <td>
                                ` + colMethod + `
                            </td>                               
                        </tr>`)

                        });
                    }

                }
            });
        });
        $('.close').on('click', function() {
            document.querySelector('#closeLoanModal').style.display = 'none';
        })

    });
</script>

@endsection