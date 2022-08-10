<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loans;
use App\Admission;
use DB;
use Log;
use App\RcaTable;
use App\Branch;
use Illuminate\Support\Facades\Http;

class LoanController extends Controller
{

    public function index()
    {

        $db = config('database.db');
        $role_designation = session('role_designation');
        // dd(session('projectcode'));

        if (session('role_designation') == 'AM') {
            $value = Branch::where([
                'program_id' => session('program_id')
            ])->get();
            $search2 = Branch::where([
                'area_id' => session('asid'),
                'program_id' => session('program_id')
            ])->distinct('branch_id')->get();
            $branch = Branch::where([
                'area_id' => session('asid'),
                'program_id' => session('program_id')
            ])->first();
            // $data1=Branch::select('branch_id','branch_name')
            //                 ->where([
            //                 'area_id' => session('asid'),
            //                 'program_id' => session('program_id')])->get();
        } else if (session('role_designation') == 'RM') {
            $value = Branch::where([
                'program_id' => session('program_id')
            ])->get();
            $search2 = Branch::where([
                'region_id' => session('asid'),
                'program_id' => session('program_id')
            ])->distinct('area_id')->get();

            $branch = Branch::where([
                'region_id' => session('asid'),
                'program_id' => session('program_id')
            ])->first();
        } else if (session('role_designation') == 'DM') {
            $value = Branch::where([
                'program_id' => session('program_id')
            ])->get();
            $search2 = Branch::where([
                'division_id' => session('asid'),
                'program_id' => session('program_id')
            ])->distinct('region_id')->get();
            $branch = Branch::where([
                'division_id' => session('asid'),
                'program_id' => session('program_id')
            ])->first();
        } else if (session('role_designation') == 'HO') {
            $value = Branch::where([
                'program_id' => session('program_id')
            ])->get();
            $branch = Branch::where([
                'division_id' => session('asid'),
                'program_id' => session('program_id')
            ])->first();
            $search2 = Branch::where([
                'program_id' => session('program_id')
            ])->distinct('division_id')->get();
        } else if (session('role_designation') == 'BM') {
            $value = Branch::where([
                'branch_id' => session('asid'),
                'program_id' => session('program_id')
            ])->get();
            $branch = Branch::where([
                'branch_id' => session('asid'),
                'program_id' => session('program_id')
            ])->first();
            $search2 = Branch::where([
                'program_id' => session('program_id')
            ])->distinct('division_id')->get();
        } else {
            return redirect()->back()->with('error', 'data does not match');
        }
        // $polist = DB::table($dberp.'.polist')
        //         ->where('projectcode',$projectcode)
        //         ->where('status','1')
        //         ->whereIn('branchcode', $all_branchcode)
        //         ->get();
        // $po=array();
        // foreach($polist as $cono)
        // {
        //     foreach($all_assignedpo as $key=> $value)
        //     {
        //         if($cono->cono == $value)
        //         {
        //             $po[] = $value;
        //         }
        //     }
        // }
        // if(session('roll')!=7)
        //     {
        // $datas = DB::table($db.'.loan')
        //     ->whereIn('assignedpo', $po)
        //     // ->where('status', 'Pending')
        //     ->where('reciverrole', session('roll'))
        //     ->get();
        // }
        $status = DB::table($db . '.status')->where('process', '*')->orderBy('status_id', 'asc')->get();

        return view('loan-request')->with('branch', $branch)->with('value', $value)->with('search2', $search2)->with('status', $status);
    }

    public function loanTable(Request $req)
    {
        $db = config('database.db');
        $dberp = config('database.dberp');
        if (session('role_designation') == 'AM') {
            $value = Branch::where([
                'area_id' => session('asid'),
                'program_id' => session('program_id')
            ])->get();
        } else if (session('role_designation') == 'RM') {
            $value = Branch::where([
                'region_id' => session('asid'),
                'program_id' => session('program_id')
            ])->get();
        } else if (session('role_designation') == 'DM') {
            $value = Branch::where([
                'division_id' => session('asid'),
                'program_id' => session('program_id')
            ])->get();
        } else if (session('role_designation') == 'HO') {
            $value = Branch::where([
                'program_id' => session('program_id')
            ])->get();
        } else if (session('role_designation') == 'BM') {
            $value = Branch::where([
                'branch_id' => session('asid'),
                'program_id' => session('program_id')
            ])->get();
        } else {
            return redirect()->back()->with('error', 'data does not match');
        }
        $all_branchcode = array();
        $all_assignedpo = array();
        foreach ($value as $row) {
            $branchCode1 = $row->branch_id;
            $branchCode = str_pad($branchCode1, 4, "0", STR_PAD_LEFT);

            $value1 = DB::table($db . '.loans')->select('branchcode', 'assignedpo')
                ->where('branchcode', $branchCode)->groupBy('branchcode', 'assignedpo')->get();
            if (!$value1->isEmpty()) {
                foreach ($value1 as $assignedpo) {
                    $all_branchcode[] = $assignedpo->branchcode;
                    $all_assignedpo[] = str_pad($assignedpo->assignedpo, 8, "0", STR_PAD_LEFT);
                }
            }
        }

        $polist = DB::table($dberp . '.polist')
            ->where('projectcode', session('projectcode'))
            ->where('status', '1')
            ->whereIn('branchcode', $all_branchcode)
            ->get();
        $po = array();
        foreach ($polist as $cono) {
            foreach ($all_assignedpo as $key => $value) {
                if ($cono->cono == $value) {
                    $po[] = $value;
                }
            }
        }
        // division search
        $division = $req->division;
        if ($division != null) {
            $d_branch = DB::table('public.branch')
                ->where('program_id', session('program_id'))
                ->where('division_id', $req->division)
                ->distinct('branch_id')
                ->get();
            foreach ($d_branch as $key => $value) {
                $division_search1 = str_pad($value->branch_id, 4, "0", STR_PAD_LEFT);
                $division_search[] = $division_search1;
            }
        }

        //find branch for region search
        $region = $req->region;
        if ($region != null) {
            $r_branch = DB::table('public.branch')
                ->where('program_id', session('program_id'))
                ->where('region_id', $region)
                ->distinct('branch_id')
                ->get();
            foreach ($r_branch as $key => $value) {
                $region_search1 = str_pad($value->branch_id, 4, "0", STR_PAD_LEFT);
                $region_search[] = $region_search1;
            }
        }
        //find branch for area search
        $area = $req->area;
        if ($area != null) {
            $area_branch = DB::table('public.branch')
                ->where('program_id', session('program_id'))
                ->where('area_id', $area)
                ->distinct('branch_id')
                ->get();

            foreach ($area_branch as $key => $value) {
                $area_search1 = str_pad($value->branch_id, 4, "0", STR_PAD_LEFT);
                $area_search[] = $area_search1;
            }
        }
        $status_search = $req->status;
        $branch_search = $req->branch;
        $branchcode_search = str_pad($branch_search, 4, "0", STR_PAD_LEFT);
        $dateForm = $req->dateFrom;
        $dateTo = $req->dateTo;
        $po_search = $req->po;

        // division & date search
        if ($division != null && $region == null && $area == null && $branch_search == null && $po_search == null && $status_search == null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->whereIn($db . '.loans.branchcode', $division_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();

            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }
        // region & date search
        if ($region != null && $area == null && $branch_search == null && $po_search == null && $status_search == null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->whereIn($db . '.loans.branchcode', $region_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();

            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }
        // area & date search
        if ($area != null && $branch_search == null && $po_search == null && $status_search == null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->whereIn($db . '.loans.branchcode', $area_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();

            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }
        // area & date & status search
        if ($area != null && $branch_search == null && $po_search == null && $status_search != null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->whereIn($db . '.loans.branchcode', $area_search)
                ->where($db . '.loans.status', $status_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();

            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }
        // region & date & status search
        if ($region != null && $area == null && $branch_search == null && $po_search == null && $status_search != null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->whereIn($db . '.loans.branchcode', $region_search)
                ->where($db . '.loans.status', $status_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();

            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }
        // division & date & status search
        if ($division != null && $region == null && $area == null && $branch_search == null && $po_search == null && $status_search != null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->whereIn($db . '.loans.branchcode', $division_search)
                ->where($db . '.loans.status', $status_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();
            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }

        // branch & date & status  search
        if ($branch_search != null  && $status_search != null && $dateForm != null && $dateTo != null && $po_search == null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->where($db . '.loans.branchcode', $branchcode_search)
                ->where($db . '.loans.status', $status_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();

            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->editColumn('time', function ($datas) {
                return date('d-m-Y', strtotime($datas->time));
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->make(true);
        }
        // branch & date search
        if ($branch_search != null && $dateForm != null && $dateTo != null && $status_search == null && $po_search == null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->where($db . '.loans.branchcode', $branchcode_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();

            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })
                // ->addColumn('propos_amt', function ($datas) {
                //     $def_amount='';
                //     $amount=$datas->propos_amt;
                //     if($amount == null){
                //         $def_amount='0';
                //         return $def_amount;
                //     }
                //     return $def_amount;
                // })
                ->addColumn('time', function ($datas) {
                    $time = date('m/d/Y', strtotime($datas->time));
                    return $time;
                })->addColumn('status', function ($datas) {
                    $db = config('database.db');
                    $Mainstatus = '';
                    $recieverRole = $datas->reciverrole;
                    $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                    $recieverRole_destination = $getRecieverRole->designation;
                    if (session('role_designation') == 'AM') {
                        if ($recieverRole_destination == 'AM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name;
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'RM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at RM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'DM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at DM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'BM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at BM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'HO') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at HO";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'PO') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at PO";
                            return $Mainstatus;
                        }
                        return $Mainstatus;
                    }
                    if (session('role_designation') == 'RM') {
                        if ($recieverRole_destination == 'AM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at AM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'RM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name;
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'DM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at DM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'BM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at BM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'HO') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at HO";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'PO') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at PO";
                            return $Mainstatus;
                        }
                        return $Mainstatus;
                    }
                    if (session('role_designation') == 'DM') {
                        if ($recieverRole_destination == 'AM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at AM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'RM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at RM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'DM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name;
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'BM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at BM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'PO') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at PO";
                            return $Mainstatus;
                        }
                        return $Mainstatus;
                    }
                    if (session('role_designation') == 'HO') {
                        if ($recieverRole_destination == 'AM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at AM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'RM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at RM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'DM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at DM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'BM') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at BM";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'PO') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name . " at PO";
                            return $Mainstatus;
                        }
                        if ($recieverRole_destination == 'HO') {
                            $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                            $Mainstatus = $statusQuery->status_name;
                            return $Mainstatus;
                        }
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                })->addColumn('action', function ($datas) {
                    return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
                })->toJson();
        }

        // date & status search
        if ($dateForm != null && $dateTo != null && $status_search != null && $po_search == null && $branch_search == null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->where($db . '.loans.status', $status_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();
            // dd($datas);
            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }

        // po & date & status
        if ($branch_search != null  && $status_search != null && $dateForm != null && $dateTo != null && $po_search != null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->where($db . '.loans.status', $status_search)
                ->where($db . '.loans.assignedpo', $po_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();
            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->editColumn('time', function ($datas) {
                return date('d-m-Y', strtotime($datas->time));
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->make(true);
        }
        // po & date
        if ($status_search == null && $dateForm != null && $dateTo != null && $po_search != null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->where($db . '.loans.assignedpo', $po_search)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();
            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->editColumn('time', function ($datas) {
                return date('d-m-Y', strtotime($datas->time));
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->make(true);
        }

        // date search
        if ($dateForm != null && $dateTo != null && $status_search == null && $branch_search == null && $po_search == null) {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                // ->whereBetween($db.'.loans.time', [$dateForm, $dateTo])
                ->whereDate($db . '.loans.time', '>=', $dateForm)
                ->whereDate($db . '.loans.time', '<=', $dateTo)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                ->get();

            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }

        if (session('role_designation') == 'HO') {
            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                // ->whereDate($db.'.loans.time', Carbon::today())
                ->get();
            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('d/m/Y', strtotime($datas->time));
                return $time;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'HO') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }

                    return $Mainstatus;
                }


                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }

        if (session('role_designation') != 'HO') {
            $today = date('Y-m-d');

            $datas = DB::table($db . '.loans')
                ->whereIn($db . '.loans.assignedpo', $po)
                ->where($db . '.loans.projectcode', session('projectcode'))
                ->where($db . '.loans.status', '1')
                ->where($db . '.loans.reciverrole', session('roll'))
                ->select($db . '.loans.*', $db . '.admissions.ApplicantsName', $db . '.admissions.MainIdTypeId')
                ->leftJoin($db . '.admissions', $db . '.loans.mem_id', '=', $db . '.admissions.entollmentid')
                // ->whereDate($db.'.loans.time', Carbon::today())
                ->get();
            return datatables($datas)->addColumn('branchcode', function ($datas) {
                $branch_name = '';
                $branchcode = $datas->branchcode;
                $branch_qry = DB::table('public.branch')->where('branch_id', $branchcode)->first();
                if ($branch_qry) {
                    $branch_name = $branch_qry->branch_name;
                    return $branch_name;
                }
                return $branch_name;
            })->addColumn('ApplicantsName', function ($datas) {
                $MemberName = '';
                if ($datas->orgmemno) {
                    $member = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$datas->branchcode&CONo=$datas->assignedpo&ProjectCode=$datas->projectcode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$datas->orgno&OrgMemNo=$datas->orgmemno&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
                    $response = Http::get($member);
                    $admissionArray = $response->object();
                    if ($admissionArray != null) {
                        $admissionApi = $admissionArray->data[0];
                        $MemberName = $admissionApi->MemberName;
                        return $MemberName;
                    }
                } else {
                    $MemberName = $datas->ApplicantsName;
                    return $MemberName;
                }
            })->addColumn('assignedpo', function ($datas) {
                $dberp = config('database.dberp');
                $coname = '';
                $assignedpo = $datas->assignedpo;
                $co_qry = DB::table($dberp . '.polist')->where('cono', $assignedpo)->first();
                if ($co_qry) {
                    $coname = $co_qry->coname;
                    return $coname;
                }
                return $coname;
            })->addColumn('time', function ($datas) {
                $time = date('m/d/Y', strtotime($datas->time));
                return $time;
            })->addColumn('status', function ($datas) {
                $db = config('database.db');
                $Mainstatus = '';
                $recieverRole = $datas->reciverrole;
                $getRecieverRole = DB::table($db . '.role_hierarchies')->select('designation')->where('projectcode', session('projectcode'))->where('position', $recieverRole)->first();
                $recieverRole_destination = $getRecieverRole->designation;
                if (session('role_designation') == 'AM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'RM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at HO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                if (session('role_designation') == 'DM') {
                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }

                if (session('role_designation') == 'HO') {

                    if ($recieverRole_destination == 'AM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at AM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'RM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at RM";

                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'DM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at DM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'BM') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at BM";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'PO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name . " at PO";
                        return $Mainstatus;
                    }
                    if ($recieverRole_destination == 'HO') {
                        $statusQuery = DB::table($db . '.status')->select('status_name')->where('status_id', $datas->status)->first();
                        $Mainstatus = $statusQuery->status_name;
                        return $Mainstatus;
                    }
                    return $Mainstatus;
                }
                return $Mainstatus;
            })->addColumn('action', function ($datas) {
                return '<a href="loan-approval/' . $datas->id . '" class="btn btn-warning">Details</a>';
            })->toJson();
        }
    }

    public function loan_approve($id)
    {
        $db = config('database.db');
        $data = DB::table($db . '.loans')
            ->where($db . '.loans.id', '=', $id)
            ->where($db . '.loans.projectcode', session('projectcode'))
            ->first();

        $data2 = json_decode($data->DynamicFieldValue);
        $rca = RcaTable::where(['loan_id' => $id])->first();
        $admissionApi = '';
        $admissionData = Admission::select('*')->where(['entollmentid' => $data->mem_id])->first();
        if ($admissionData == null) {
            $branchCode = $data->branchcode;
            $CONo = $data->assignedpo;
            $projectCode = $data->projectcode;
            $OrgNo = $data->orgno;
            $OrgMemNo = $data->orgmemno;
            $url = "https://bracapitesting.brac.net/scapir/MemberList?BranchCode=$branchCode&CONo=$CONo&ProjectCode=$projectCode&UpdatedAt=2000-01-01%2000%3A00%3A00&Status=1&OrgNo=$OrgNo&OrgMemNo=$OrgMemNo&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae";
            $response = Http::get($url);
            $admissionArray = $response->object();
            $admissionApi = $admissionArray->data[0];
        }
        return view('loanApproval')->with('data', $data)->with('data2', $data2)->with('rca', $rca)->with('admissionApi', $admissionApi)->with('admissionData', $admissionData);
    }

    public function assessmentInsertion($request)
    {
        $db = config('database.db');

        if (session('role_designation') == 'AM' and $request->action != "Reject") {
            $am_assessment = array(
                'am_primary_earner' => $request->all_primary_earner1,
                'am_monthlyincome_main' => $request->all_monthlyincome_main1,
                'am_monthlyincome_spouse_child' => $request->all_monthlyincome_spouse_child1,
                'am_monthlyincome_other' => $request->all_monthlyincome_other1,
                'am_house_rent' => $request->all_house_rent1,
                'am_food' => $request->all_food1,
                'am_education' => $request->all_education1,
                'am_medical' => $request->all_medical1,
                'am_festive' => $request->all_festive1,
                'am_utility' => $request->all_utility1,
                'am_saving' => $request->all_saving1,
                'am_other' => $request->all_other1,
                'am_debt' => $request->all_debt1,
                'am_monthly_cash' => $request->all_monthly_cash1,
                'am_instal_proposloan' => $request->all_instal_proposloan1
            );
            DB::table($db . '.rca')->where('loan_id', $request->id)->update($am_assessment);
        }
        if (session('role_designation') == 'RM' and $request->action != "Reject") {
            $rm_assessment = array(
                'rm_primary_earner' => $request->all_primary_earner1,
                'rm_monthlyincome_main' => $request->all_monthlyincome_main1,
                'rm_monthlyincome_spouse_child' => $request->all_monthlyincome_spouse_child1,
                'rm_monthlyincome_other' => $request->all_monthlyincome_other1,
                'rm_house_rent' => $request->all_house_rent1,
                'rm_food' => $request->all_food1,
                'rm_education' => $request->all_education1,
                'rm_medical' => $request->all_medical1,
                'rm_festive' => $request->all_festive1,
                'rm_utility' => $request->all_utility1,
                'rm_saving' => $request->all_saving1,
                'rm_other' => $request->all_other1,
                'rm_debt' => $request->all_debt1,
                'rm_monthly_cash' => $request->all_monthly_cash1,
                'rm_instal_proposloan' => $request->all_instal_proposloan1
            );
            DB::table($db . '.rca')->where('loan_id', $request->id)->update($rm_assessment);
        }
    }

    public function action_btn(Request $request)
    {
        $db = config('database.db');

        $this->assessmentInsertion($request);

        $role = session('roll');
        $db1 = DB::table($db . '.loans')
            ->where($db . '.loans.id', '=', $request->id)
            ->where($db . '.loans.projectcode', session('projectcode'))
            ->first();
        $branchcode = $db1->branchcode;
        $loan_id = $db1->loan_id;
        $projectcode = $db1->projectcode;
        $action = $request->action;
        $doc_id = $request->id;
        $pin = session('user_pin');
        $comment = urlencode($request->comment);


        $document_url = "http://scm.brac.net/dcs/DocumentManager?doc_id=$doc_id&projectcode=$projectcode&doc_type=loan&pin=$pin&role=$role&branchcode=$branchcode&action=$action&comment=$comment";
        // dd($document_url);
        Log::channel('daily')->info('Document_url : ' . $document_url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $document_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $documentoutput = curl_exec($ch);
        curl_close($ch);

        $collectionfordocument = json_decode($documentoutput);
        // dd($collectionfordocument);

        if ($collectionfordocument != NULL) {
            return redirect('/operation/loan')->with('success', 'Action suucessful.');
        } else {
            return redirect()->back()->with('error', 'Data does not send!');
        }
    }

    public function approve_loan(Request $request)
    {
        $db = config('database.db');
        $doc_type = "loan";
        $role = session('roll');
        $db1 = DB::table($db . '.loans')
            ->where($db . '.loans.id', '=', $request->id)
            ->where($db . '.loans.projectcode', session('projectcode'))
            ->first();
        $branchcode = $db1->branchcode;
        $loan_id = $db1->loan_id;
        $projectcode = $db1->projectcode;
        $action = "Approve";
        $doc_id = $request->id;
        $pin = session('user_pin');
        $proposeAmount = $db1->propos_amt;

        // work with celling data
        $growth_rate = "HIGH";
        $cellingData = DB::table($db . '.celing_configs')
            ->select('limit_form', 'limit_to')
            ->where('approver', session('role_designation'))
            ->where('growth_rate', $growth_rate)
            ->first();
        $limitFrom = $cellingData->limit_form;
        $limitTo = $cellingData->limit_to;
        if ($proposeAmount < $limitFrom or $proposeAmount > $limitTo) {
            // dd($proposeAmount,$limitFrom,$limitTo);
            return redirect()->back()->with('error', 'Loan amount Limit exceed for your designation');
        }

        $document_url = "http://scm.brac.net/dcs/DocumentManager?doc_id=$doc_id&projectcode=$projectcode&doc_type=loan&pin=$pin&role=$role&branchcode=$branchcode&action=$action";
        // dd($document_url);
        Log::channel('daily')->info('Document_url : ' . $document_url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $document_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $documentoutput = curl_exec($ch);
        curl_close($ch);

        $collectionfordocument = json_decode($documentoutput);
        // dd($collectionfordocument);

        if ($collectionfordocument != NULL) {
            if ($collectionfordocument->status == "E") {
                if (isset($collectionfordocument->errors)) {
                    if (isset($collectionfordocument->errors[0]->message)) {
                        $errors = $collectionfordocument->errors[0]->message;
                        return redirect()->back()->with('error', $errors);
                    } else if (isset($collectionfordocument->errors[0]->fieldErrors)) {
                        foreach ($collectionfordocument->errors[0]->fieldErrors as $row) {
                            $errors[] = [
                                "field" => $row->field,
                                "message" => $row->message
                            ];
                        }
                        return redirect()->back()->with('errors', $errors);
                    } else {
                        $errors = $collectionfordocument->errors;
                        return redirect()->back()->with('error', $errors);
                    }
                } else {
                    $errors = $collectionfordocument->message;
                    return redirect()->back()->with('error', $errors);
                }
            } else {
                return redirect('/operation/loan')->with('success', 'Action suucessful.');
            }
        } else {
            return redirect()->back()->with('error', 'Data does not send!');
        }
    }

    public function closeLoan(Request $request)
    {
        $erp_mem_id = $request->memId;
        $url = "http://scm.brac.net/dcs/LastOneCloseLoanBehavior?token=7f30f4491cb4435984616d1913e88389&BranchCode=1108&MemberId=$erp_mem_id&key=5d0a4a85-df7a-scapi-bits-93eb-145f6a9902ae&OrgNo=&OrgMemNo";
        $response = Http::get($url);
        $data = $response->object();
        return response()->json($data);
    }
}
