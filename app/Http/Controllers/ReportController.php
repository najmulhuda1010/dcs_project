<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Branch;
use App\Admission;
use App\Survey;
use DB;

class ReportController extends Controller
{
    public function report()
    {
        $db = config('database.db');
        $dberp = config('database.dberp');
        $currentdate = date('Y-m-d');
        $startdate = date('Y-m-01');
        $report_type = "Summary Report";
        // $currentdate = date('Y-m-d');
        // $startdate = date('Y-m-01');
        $projectcode = session('projectcode');
        $search2 = Branch::where([
            'program_id' => session('program_id')
        ])->distinct('division_id')->get();
        $noOfAdmission = Admission::where('projectcode', $projectcode)->where('reciverrole', '!=', 0)->where('created_at', '<=', $currentdate)->where('created_at', '>=', $startdate)->count();
        $noOfSurvey = Survey::where('projectcode', $projectcode)->where('created_at', '<=', $currentdate)->where('created_at', '>=', $startdate)->count();
        $noOfLoan = DB::table($db . '.loans')->where('projectcode', $projectcode)->whereDate('time', '<=', $currentdate)->whereDate('time', '>=', $startdate)->where('reciverrole', '!=', 0)->count();
        $totalDisAmount = DB::table($db . '.loans')->selectRaw('sum(cast(propos_amt AS numeric))')->where('reciverrole', '!=', 0)->where('projectcode', $projectcode)->whereDate('time', '<=', $currentdate)->whereDate('time', '>=', $startdate)->get();
        $totalDisAmount = $totalDisAmount[0]->sum ?? 0;
        if ($totalDisAmount != 0) {
            $averageLoanSize = round($totalDisAmount / $noOfLoan);
        } else {
            $averageLoanSize = 0;
        }

        return view('Report', compact('noOfAdmission', 'noOfSurvey', 'noOfLoan', 'totalDisAmount', 'averageLoanSize', 'report_type', 'search2'));
    }

    public function search_report(Request $request)
    {
        $report_type = $request->report_type;
        $db = config('database.db');
        $dberp = config('database.dberp');
        $startDate = $request->dateFrom;
        $endDate = $request->dateTo;
        $report_type = $request->report_type;
        $division = $request->division;
        $projectcode = session('projectcode');
        $program_id = session('program_id');
        $search2 = Branch::where([
            'program_id' => $program_id
        ])->distinct('division_id')->get();

        if ($report_type == 'summary') {
            if ($division == null) {
                $location = 'All';
                $noOfAdmission = Admission::where('projectcode', $projectcode)->where('reciverrole', '!=', 0)->where('created_at', '<=', $endDate)->where('created_at', '>=', $startDate)->count();
                $noOfSurvey = Survey::where('projectcode', $projectcode)->where('created_at', '<=', $endDate)->where('created_at', '>=', $startDate)->count();
                $noOfLoan = DB::table($db . '.loans')->where('projectcode', $projectcode)->where('reciverrole', '!=', 0)->whereDate('time', '<=', $endDate)->whereDate('time', '>=', $startDate)->count();
                // dd($noOfLoan, $endDate, $startDate);
                $totalDisAmount = DB::table($db . '.loans')->selectRaw('sum(cast(propos_amt AS numeric))')->where('reciverrole', '!=', 0)->where('projectcode', $projectcode)->whereDate('time', '<=', $endDate)->whereDate('time', '>=', $startDate)->get();
                $totalDisAmount = $totalDisAmount[0]->sum ?? 0;
                if ($totalDisAmount != 0) {
                    $averageLoanSize = round($totalDisAmount / $noOfLoan);
                } else {
                    $averageLoanSize = 0;
                }
                return view('ReportSearch', compact('noOfAdmission', 'noOfSurvey', 'noOfLoan', 'totalDisAmount', 'averageLoanSize', 'report_type', 'search2', 'location', 'startDate', 'endDate'));
            } else {
                $region = $request->region;
                if ($region == null) {
                    $response = $this->findBranchList($division, 'division', $program_id);
                    $branchList = $response['branchList'];
                    $locationInfo = $response['locationInfo'];
                    $location = $locationInfo->division_name;
                } else {
                    $area = $request->area;
                    if ($area == null) {
                        $response = $this->findBranchList($region, 'region', $program_id);
                        $branchList = $response['branchList'];
                        $locationInfo = $response['locationInfo'];
                        $location = $locationInfo->region_name;
                    } else {
                        $branch = $request->branch;
                        if ($branch == null) {
                            $response = $this->findBranchList($area, 'area', $program_id);
                            $branchList = $response['branchList'];
                            $locationInfo = $response['locationInfo'];
                            $location = $locationInfo->area_name;
                        } else {
                            $assignedpo = $request->po;
                            $response = $this->findBranchList($branch, 'branch', $program_id);
                            $branchList = $response['branchList'];
                            $locationInfo = $response['locationInfo'];
                            $location = $locationInfo->branch_name;
                            if ($assignedpo != null) {
                                $noOfAdmission = Admission::where('projectcode', $projectcode)->where('reciverrole', '!=', 0)->where('created_at', '<=', $endDate)->where('created_at', '>=', $startDate)->where('assignedpo', $assignedpo)->count();
                                // dd($noOfAdmission);
                                $noOfSurvey = Survey::where('projectcode', $projectcode)->where('created_at', '<=', $endDate)->where('created_at', '>=', $startDate)->where('assignedpo', $assignedpo)->count();
                                $noOfLoan = DB::table($db . '.loans')->where('reciverrole', '!=', 0)->where('projectcode', $projectcode)->whereDate('time', '<=', $endDate)->whereDate('time', '>=', $startDate)->where('assignedpo', $assignedpo)->count();
                                $totalDisAmount = DB::table($db . '.loans')->selectRaw('sum(cast(propos_amt AS numeric))')->where('projectcode', $projectcode)->whereDate('time', '<=', $endDate)->where('reciverrole', '!=', 0)->whereDate('time', '>=', $startDate)->where('assignedpo', $assignedpo)->get();
                                $totalDisAmount = $totalDisAmount[0]->sum ?? 0;
                                if ($totalDisAmount != 0) {
                                    $averageLoanSize = round($totalDisAmount / $noOfLoan);
                                } else {
                                    $averageLoanSize = 0;
                                }
                                return view('ReportSearch', compact('noOfAdmission', 'noOfSurvey', 'noOfLoan', 'totalDisAmount', 'averageLoanSize', 'report_type', 'search2', 'location', 'startDate', 'endDate'));
                            }
                        }
                    }
                }
                $noOfAdmission = Admission::where('projectcode', $projectcode)->where('reciverrole', '!=', 0)->where('created_at', '<=', $endDate)->where('created_at', '>=', $startDate)->whereIn('branchcode', $branchList)->count();
                // dd($noOfAdmission);
                $noOfSurvey = Survey::where('projectcode', $projectcode)->where('created_at', '<=', $endDate)->where('created_at', '>=', $startDate)->whereIn('branchcode', $branchList)->count();
                $noOfLoan = DB::table($db . '.loans')->where('reciverrole', '!=', 0)->where('projectcode', $projectcode)->whereDate('time', '<=', $endDate)->whereDate('time', '>=', $startDate)->whereIn('branchcode', $branchList)->count();
                $totalDisAmount = DB::table($db . '.loans')->selectRaw('sum(cast(propos_amt AS numeric))')->where('reciverrole', '!=', 0)->where('projectcode', $projectcode)->whereDate('time', '<=', $endDate)->whereDate('time', '>=', $startDate)->whereIn('branchcode', $branchList)->get();
                $totalDisAmount = $totalDisAmount[0]->sum ?? 0;
                if ($totalDisAmount != 0) {
                    $averageLoanSize = round($totalDisAmount / $noOfLoan);
                } else {
                    $averageLoanSize = 0;
                }
                return view('ReportSearch', compact('noOfAdmission', 'noOfSurvey', 'noOfLoan', 'totalDisAmount', 'averageLoanSize', 'report_type', 'search2', 'location', 'startDate', 'endDate'));
            }
        } else {
            $loanData = [];
            if ($division == null) {
                $loans = DB::table($db . '.loans')->where('reciverrole', '!=', 0)->where('projectcode', $projectcode)->whereDate('time', '<=', $endDate)->whereDate('time', '>=', $startDate)->orderBy('time', 'desc')->get();
                foreach ($loans as $row) {
                    $branchcode = $row->branchcode;
                    $loan_product = $row->loan_product;
                    $assignedpo = $row->assignedpo;
                    $mem_id = $row->mem_id;

                    $branchname = Branch::select('branch_name')->where([
                        'branch_id' => $branchcode,
                        'program_id' => session('program_id')
                    ])->first();
                    $productname = DB::table($db . '.product_project_member_category')->select('productname')->where('projectcode', (int)$projectcode)->where('productid', $loan_product)->first();
                    $poname = DB::table($dberp . '.polist')->select('coname')->where('cono', $assignedpo)->where('branchcode', $branchcode)->first();

                    $serverurl = DB::Table($dberp . '.server_url')->where('server_status', 3)->where('status', 1)->first();
                    $key = '5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae';
                    $UpdatedAt = "2000-01-01 00:00:00";
                    $member = Http::get($serverurl->url . 'MemberList', [
                        'BranchCode' => $branchcode,
                        'CONo' => $assignedpo,
                        'ProjectCode' => $projectcode,
                        'UpdatedAt' => $UpdatedAt,
                        'Status' => 1,
                        'OrgNo' => $row->orgno,
                        'OrgMemNo' => $row->orgmemno,
                        'key' => $key
                    ]);
                    // dd($member);
                    $member = $member->object();
                    if ($member != null) {
                        $appliedby = $member->data[0];
                        $MemberName = $appliedby->MemberName;
                    }

                    $data['branchname'] = $branchname->branch_name;
                    $data['poname'] = $poname->coname;
                    $data['applicationdate'] = date('d-m-Y', strtotime($row->time));
                    $data['disbamnt'] = $row->propos_amt;
                    $data['productname'] = $productname->productname;
                    $data['appliedby'] = $MemberName;

                    $loanData[] = $data;
                }
                return view('ReportSearch', compact('loanData', 'loans', 'report_type', 'search2', 'startDate', 'endDate'));
                // dd($loans);
            } else {
                $region = $request->region;
                if ($region == null) {
                    $response = $this->findBranchList($division, 'division', $program_id);
                    $branchList = $response['branchList'];
                    $locationInfo = $response['locationInfo'];
                    $location = $locationInfo->division_name;
                } else {
                    $area = $request->area;
                    if ($area == null) {
                        $response = $this->findBranchList($region, 'region', $program_id);
                        $branchList = $response['branchList'];
                        $locationInfo = $response['locationInfo'];
                        $location = $locationInfo->region_name;
                    } else {
                        $branch = $request->branch;
                        if ($branch == null) {
                            $response = $this->findBranchList($area, 'area', $program_id);
                            $branchList = $response['branchList'];
                            $locationInfo = $response['locationInfo'];
                            $location = $locationInfo->area_name;
                        } else {
                            $assignedpo = $request->po;
                            $response = $this->findBranchList($branch, 'branch', $program_id);
                            $branchList = $response['branchList'];
                            $locationInfo = $response['locationInfo'];
                            $location = $locationInfo->branch_name;
                            if ($assignedpo != null) {
                                $loans = DB::table($db . '.loans')->where('reciverrole', '!=', 0)->where('projectcode', $projectcode)->whereDate('time', '<=', $endDate)->whereDate('time', '>=', $startDate)->where('assignedpo', $assignedpo)->orderBy('time', 'desc')->get();
                                foreach ($loans as $row) {
                                    $branchcode = $row->branchcode;
                                    $loan_product = $row->loan_product;
                                    $assignedpo = $row->assignedpo;
                                    $mem_id = $row->mem_id;

                                    $branchname = Branch::select('branch_name')->where([
                                        'branch_id' => $branchcode,
                                        'program_id' => session('program_id')
                                    ])->first();
                                    $productname = DB::table($db . '.product_project_member_category')->select('productname')->where('projectcode', (int)$projectcode)->where('productid', $loan_product)->first();
                                    $poname = DB::table($dberp . '.polist')->select('coname')->where('cono', $assignedpo)->where('branchcode', $branchcode)->first();
                                    $serverurl = DB::Table($dberp . '.server_url')->where('server_status', 3)->where('status', 1)->first();
                                    $key = '5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae';
                                    $UpdatedAt = "2000-01-01 00:00:00";
                                    $member = Http::get($serverurl->url . 'MemberList', [
                                        'BranchCode' => $branchcode,
                                        'CONo' => $assignedpo,
                                        'ProjectCode' => $projectcode,
                                        'UpdatedAt' => $UpdatedAt,
                                        'Status' => 1,
                                        'OrgNo' => $row->orgno,
                                        'OrgMemNo' => $row->orgmemno,
                                        'key' => $key
                                    ]);
                                    // dd($member);
                                    $member = $member->object();
                                    if ($member != null) {
                                        $appliedby = $member->data[0];
                                        $MemberName = $appliedby->MemberName;
                                    }

                                    $data['branchname'] = $branchname->branch_name;
                                    $data['poname'] = $poname->coname;
                                    $data['applicationdate'] = date('d-m-Y', strtotime($row->time));
                                    $data['disbamnt'] = $row->propos_amt;
                                    $data['productname'] = $productname->productname;
                                    $data['appliedby'] = $MemberName;

                                    $loanData[] = $data;
                                }
                                return view('ReportSearch', compact('response', 'loans', 'report_type', 'search2', 'startDate', 'endDate'));
                            }
                        }
                    }
                }
                $loans = DB::table($db . '.loans')->where('reciverrole', '!=', 0)->where('projectcode', $projectcode)->whereDate('time', '<=', $endDate)->whereDate('time', '>=', $startDate)->whereIn('branchcode', $branchList)->orderBy('time', 'desc')->get();
                foreach ($loans as $row) {
                    $branchcode = $row->branchcode;
                    $loan_product = $row->loan_product;
                    $assignedpo = $row->assignedpo;
                    $mem_id = $row->mem_id;

                    $branchname = Branch::select('branch_name')->where([
                        'branch_id' => $branchcode,
                        'program_id' => session('program_id')
                    ])->first();
                    $productname = DB::table($db . '.product_project_member_category')->select('productname')->where('projectcode', (int)$projectcode)->where('productid', $loan_product)->first();
                    $poname = DB::table($dberp . '.polist')->select('coname')->where('cono', $assignedpo)->where('branchcode', $branchcode)->first();
                    $serverurl = DB::Table($dberp . '.server_url')->where('server_status', 3)->where('status', 1)->first();
                    $key = '5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae';
                    $UpdatedAt = "2000-01-01 00:00:00";
                    $member = Http::get($serverurl->url . 'MemberList', [
                        'BranchCode' => $branchcode,
                        'CONo' => $assignedpo,
                        'ProjectCode' => $projectcode,
                        'UpdatedAt' => $UpdatedAt,
                        'Status' => 1,
                        'OrgNo' => $row->orgno,
                        'OrgMemNo' => $row->orgmemno,
                        'key' => $key
                    ]);
                    // dd($member);
                    $member = $member->object();
                    if ($member != null) {
                        $appliedby = $member->data[0];
                        $MemberName = $appliedby->MemberName;
                    }

                    $data['branchname'] = $branchname->branch_name;
                    $data['poname'] = $poname->coname;
                    $data['applicationdate'] = date('d-m-Y', strtotime($row->time));
                    $data['disbamnt'] = $row->propos_amt;
                    $data['productname'] = $productname->productname;
                    $data['appliedby'] = $MemberName;

                    $loanData[] = $data;
                }
                return view('ReportSearch', compact('loanData', 'loans', 'report_type', 'search2', 'startDate', 'endDate'));
            }
        }
    }

    public function findBranchList($location, $location_type, $program_id)
    {
        $db = config('database.db');
        $dberp = config('database.dberp');
        $response = [];
        $branchArray = [];
        if ($location_type == 'division') {
            $branchList = Branch::select('branch_id')->where([
                'division_id' => $location,
                'program_id' => session('program_id')
            ])->get();
            foreach ($branchList as $row) {
                $branchArray[] = $row->branch_id;
            }
            $divisionInfo = Branch::select('division_name')->where([
                'division_id' => $location,
                'program_id' => session('program_id')
            ])->first();
            $response['branchList'] = $branchArray;
            $response['locationInfo'] = $divisionInfo;
        } elseif ($location_type == 'region') {
            $branchList = Branch::select('branch_id')->where([
                'region_id' => $location,
                'program_id' => session('program_id')
            ])->get();
            foreach ($branchList as $row) {
                $branchArray[] = $row->branch_id;
            }
            $regionInfo = Branch::select('region_name')->where([
                'region_id' => $location,
                'program_id' => session('program_id')
            ])->first();
            $response['branchList'] = $branchArray;
            $response['locationInfo'] = $regionInfo;
        } elseif ($location_type == 'area') {
            $branchList = Branch::select('branch_id')->where([
                'area_id' => $location,
                'program_id' => session('program_id')
            ])->get();
            foreach ($branchList as $row) {
                $branchArray[] = $row->branch_id;
            }
            $areaInfo = Branch::select('area_name')->where([
                'area_id' => $location,
                'program_id' => session('program_id')
            ])->first();
            $response['branchList'] = $branchArray;
            $response['locationInfo'] = $areaInfo;
        } elseif ($location_type == 'branch') {
            $branchList = Branch::select('branch_id')->where([
                'branch_id' => $location,
                'program_id' => session('program_id')
            ])->get();
            foreach ($branchList as $row) {
                $branchArray[] = $row->branch_id;
            }
            $branchInfo = Branch::select('branch_name')->where([
                'branch_id' => $location,
                'program_id' => session('program_id')
            ])->first();
            $response['branchList'] = $branchArray;
            $response['locationInfo'] = $branchInfo;
        }

        return $response;
    }
}
