<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Login Routes
Route::get('/weblogin', 'LoginController@weblogin');
Route::get('/Logout', 'LoginController@Logout');
/***************************Start Api******************************************/
Route::get('/test', 'ApiController@Index');
Route::post('/CelingConfig', 'ApiController@CelingConfig');
Route::POST('/GetConfig', 'ApiController@GetConfig');
Route::get('/GetLoanProducts', 'ApiController@GetLoanProducts');
Route::POST('/Authrization', 'ApiController@Auth');
Route::POST('/NIDVerification', 'ApiController@NIDVerification');
Route::POST('/ImageUpload', 'ApiController@ImageUpload');
Route::post('/SurveyDataStore', 'ApiController@SurveyStore');
Route::get('/AllSurveyData', 'ApiController@AllSurveyData');
Route::post('/AdmissionDataStore', 'ApiController@AdmissionStore');
Route::get('/AllAdmissionData', 'ApiController@AllAdmissionData');
Route::post('/LoanRcaDataStore', 'ApiController@LoanRcaDataStore');
Route::get('/AllLoanRcaData', 'ApiController@AllLoanRcaData');
Route::get('/NotificationManager', 'ApiController@NotificationManager');
Route::get('/DocumentManager', 'ApiController@DocumentManager');
Route::post('/BmAdmissionDataSync', 'ApiController@BmAdmissionDataSync');
Route::post('/BmLoanDataSync', 'ApiController@BmLoanDataSync');
Route::get('/NotificatioManager', 'ApiController@NotificatioManager');
/***************************End Api******************************************/
Route::get('/loginpage', 'DashboardController@login');
//language switcher route
Route::get('/lang/{locale}', function ($locale) {
    session()->put('locale', $locale);
    return redirect()->back();
});
// middleware route for login
Route::group(['middleware' => ['Logcheck']], function () {

    Route::get('/', function () {
        return view('Dashboard');
    });
    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    // project selection
    Route::get('/user/{project}', 'DashboardController@project')->name('user_project');
    // share data
    // View::composer('backend.layouts.partials.sidebar', function ($view) {
    //     $projects = Project::all();
    //     $view->with('projects',$projects);
    // });

    Route::prefix('config')->group(function () {
        // admin configuration
        Route::get('/admin-config', 'UserController@index')->name('admin_config');
        Route::post('/admin-info', 'UserController@store')->name('adminInfoStore');
        Route::get('/admin-delete/{id}', 'UserController@delete');
        Route::post('/admin-update/{id}', 'UserController@update');
        Route::get('/admin-edit/{id}', 'UserController@edit')->name('admin_edit');
        // project creation
        Route::get('/project-create', 'ProjectController@index')->name('project');
        Route::post('/project-store', 'ProjectController@store')->name('create-project');
        // middleware route for project
        Route::group(['middleware' => ['Project']], function () {
            // Role Hierarchy
            Route::get('/role-hierarchy', 'RoleHierarchyController@index')->name('role-hierarchy_config');
            // Route::post('/role-hierarchy-store', 'RoleHierarchyController@store')->name('role-hierarchy');
            Route::post('/role-hierarchy-update', 'RoleHierarchyController@update')->name('hierarchy-update');
            Route::get('/update-status', 'RoleHierarchyController@updateStatus');
            // Celling-config
            Route::get('/celling-config', 'CellingController@index')->name('celling-config');
            // auth config
            Route::get('/auth', 'AuthConfigController@index')->name('authorization');
            Route::post('/auth-store', 'AuthConfigController@store')->name('auth-config');
            Route::post('/auth-edit', 'AuthConfigController@edit');
            Route::post('/auth-update', 'AuthConfigController@update')->name('auth-update');
            // Notification configaration
            Route::get('/notification-view', 'NotificationController@index')->name('notification_config');
            Route::post('/notification-view', 'NotificationController@view');
            Route::post('/process-view', 'NotificationController@process');
            Route::get('/notification-delete/{id}', 'NotificationController@delete');
            Route::get('/notification-edit/{id}', 'NotificationController@edit');
            Route::post('/notification-store', 'NotificationController@store')->name('notification');
            Route::post('/notification-update/{id}', 'NotificationController@update')->name('notification-update');
            //form config && popup url
            Route::post('/Formconfig-popup', 'PopupController@store')->name("Formconfig-popup");
            Route::get('/form-config', 'FormConfigController@index')->name('form-config');
            Route::post('/form-config', 'FormConfigController@app_form')->name('application_form');
            Route::post('/form-config/store', 'FormConfigController@store')->name('formConfigstore');
            // dashboard configuration
            Route::get('dashboard-config', function () {
                return view('DashboardConfig');
            });
        });
    });

    // middleware route for project
    Route::group(['middleware' => ['Project']], function () {
        Route::prefix('operation')->group(function () {
            //admission & loan & survey
            Route::get('/loan', 'LoanController@index')->name("loan-request");
            Route::get('/admission', 'AdmissionController@index')->name("admission-request");
            Route::get('/survey', 'SurveyController@index')->name("survey-request");
            //admission & loan ajax call
            Route::get('/admissionTable', 'AdmissionController@admissionTable');
            Route::get('/loanTable', 'LoanController@loanTable');
            Route::get('/suveyTable', 'SurveyController@suveyTable');
            // branch filter ajax for admission & loan
            Route::get('/branch_filter', 'AdmissionController@branchFilter');
            // admission search
            Route::post('/admission-division', 'AdmissionController@division');
            Route::post('/admission-region', 'AdmissionController@region');
            Route::post('/admission-area', 'AdmissionController@area');
            Route::post('/admission-branch', 'AdmissionController@branch');
            Route::get('/admission-search', 'AdmissionController@search')->name("admission-search");
            Route::get('/loan-search', 'LoanController@search')->name("loan-search");
            Route::get('/admission-searchAjax', 'AdmissionController@searchAjax');
            // survey details
            Route::get('/survey-details/{id}', 'SurveyController@survey_details');
            // admission-aprroval
            Route::get('/admission-approval/{id}', 'AdmissionController@admission_approve')->name('admissionApproval');
            // loan-aprroval
            Route::get('/loan-approval/{id}', 'LoanController@loan_approve')->name('loanApproval');
            Route::post('/loan-action', 'LoanController@action_btn')->name('action_btn');
            Route::post('/admission-action', 'AdmissionController@action_btn')->name('admission_action');
            Route::post('/loan-Approve', 'LoanController@approve_loan')->name('loan_approve');
            Route::post('/admission-Approve', 'AdmissionController@approve_admission')->name('admission_approve');
        });
        Route::prefix('report')->group(function () {
            // form
            Route::get('/survey', 'SurveyController@index');
            Route::post('/survey', 'SurveyController@store')->name('store');
            Route::get('/posted-admission', 'postedAdmissionController@index')->name('postedAdmission');
            Route::get('/postedAdmissionData', 'postedAdmissionController@postedAdmissionData');
            Route::get('/posted-admission-details/{id}', 'postedAdmissionController@postedAdmissionDetails');
            Route::get('/posted-loan', 'postedLoanController@index')->name('postedLoan');
            Route::get('/postedLoanData', 'postedLoanController@postedLoanData');
            Route::get('/posted-loan-details/{id}', 'postedLoanController@postedLoanDetails');
            Route::post('/posted-loan', 'AdmissionController@store')->name('sotre');
            Route::get('/s_show', 'FormConfigController@s_show');
            Route::get('/loan-proposal', 'LoanProposalContoller@index');
            Route::get('/disbursment-status', 'DusbursementController@index')->name("disbursment-status");
            // Admin config
            Route::post('/user-store', 'UserController@store')->name('user');
            // Action List
            Route::get('/action-list', 'ActionlistContoller@index')->name('action');
            Route::post('/action-list-store', 'ActionlistContoller@store')->name('action-list');
            // Message Que
            Route::get('/message-que', 'MessageQueController@index')->name('message-que_config');
            Route::post('/message-que-store', 'MessageQueController@store')->name('message-que');
            // process
            Route::get('/process', 'ProcessController@index')->name('process-create');
            Route::post('/process-store', 'ProcessController@store')->name('process');
            // Route::post('/process-store', 'ProcessController@store')->name('process');
            Route::get('/survey-table', 'SurveyTableController@index');
            Route::get('/survey-info', 'SurveyTableController@survey_info');
            // admission api
            Route::get('/admission-api', 'AdmissionTableController@admission_api');
            Route::get('/UserLoad', 'MainController@userListLoad');
        });
        /********************************Start Notification*****************************************/
        // Route::get('Notification','NotificationController@Notification');
        /********************************End Notification*****************************************/
    });
});
// admission
Route::get('/updateAdmission', 'AdmissionTableController@admission_api');
// loans
Route::get('/updateRCA', 'LoanApprovalController@rca_api');
// rca
Route::get('/updateLoans', 'LoanApprovalController@loan_api');
// survey api
Route::get('/survey-api', 'SurveyController@index');
Route::post('/survey-dynamic-value', 'SurveyController@dynamic_value');
Route::get('/UpdateSurvey', 'SurveyTableController@insert');
Route::get('/testData', 'TestTableController@postedAdmission');
Route::get('/testDataLoan', 'TestTableController@postedLoan');
Route::get('/getApi', 'TestTableController@timecheck');
Route::get('/getApiLoan', 'TestTableController@getApiLoan');
Route::get('/getresadmission', 'TestTableController@getErpDcsAddmissionList');
Route::get('/getresloan', 'TestTableController@getErpDcsLoanList');
