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
Route::get('/login', 'UserController@login')->name('login');
Route::post('/login', 'UserController@login')->name('dologin');
Route::get('/register', 'RegisterController@index')->name('register');
Route::post('/register', 'RegisterController@sendsms')->name('sendsms');
Route::post('/register/checksms', 'RegisterController@checksms')->name('checksms');
Route::post('/register/createuser', 'RegisterController@createuser')->name('createuser');
Route::group(['middleware' => ['auth', 'message','changeCharactersAllToBePersian']], function () {
    Route::get('/', 'DashboardController@index')->name('dashboard_admin');

    Route::group(['prefix' => '/need_parent_tag_ones','middleware' => 'limit-access'], function () {
        Route::get('/', 'NeedParentTagOneController@index')->name('need_parent_tag_ones');
        Route::any('/create', 'NeedParentTagOneController@create')->name('need_parent_tag_one_create');
        Route::any('/edit/{id}', 'NeedParentTagOneController@edit')->name('need_parent_tag_one_edit');
        Route::get('/delete/{id}', 'NeedParentTagOneController@delete')->name('need_parent_tag_one_delete');
    });

    Route::group(['prefix' => '/need_parent_tag_twos','middleware' => 'limit-access'], function () {
        Route::get('/', 'NeedParentTagTwoController@index')->name('need_parent_tag_twos');
        Route::any('/create', 'NeedParentTagTwoController@create')->name('need_parent_tag_two_create');
        Route::any('/edit/{id}', 'NeedParentTagTwoController@edit')->name('need_parent_tag_two_edit');
        Route::get('/delete/{id}', 'NeedParentTagTwoController@delete')->name('need_parent_tag_two_delete');
    });

    Route::group(['prefix' => '/need_parent_tag_threes','middleware' => 'limit-access'], function () {
        Route::get('/', 'NeedParentTagThreeController@index')->name('need_parent_tag_threes');
        Route::any('/create', 'NeedParentTagThreeController@create')->name('need_parent_tag_three_create');
        Route::any('/edit/{id}', 'NeedParentTagThreeController@edit')->name('need_parent_tag_three_edit');
        Route::get('/delete/{id}', 'NeedParentTagThreeController@delete')->name('need_parent_tag_three_delete');
    });

    Route::group(['prefix' => '/need_parent_tag_fours','middleware' => 'limit-access'], function () {
        Route::get('/', 'NeedParentTagFourController@index')->name('need_parent_tag_fours');
        Route::any('/create', 'NeedParentTagFourController@create')->name('need_parent_tag_four_create');
        Route::any('/edit/{id}', 'NeedParentTagFourController@edit')->name('need_parent_tag_four_edit');
        Route::get('/delete/{id}', 'NeedParentTagFourController@delete')->name('need_parent_tag_four_delete');
    });

    Route::group(['prefix' => '/parent_tag_ones','middleware' => 'limit-access'], function () {
        Route::get('/', 'ParentTagOneController@index')->name('parent_tag_ones');
        Route::any('/create', 'ParentTagOneController@create')->name('parent_tag_one_create');
        Route::any('/edit/{id}', 'ParentTagOneController@edit')->name('parent_tag_one_edit');
        Route::get('/delete/{id}', 'ParentTagOneController@delete')->name('parent_tag_one_delete');
    });

    Route::group(['prefix' => '/parent_tag_twos','middleware' => 'limit-access'], function () {
        Route::get('/', 'ParentTagTwoController@index')->name('parent_tag_twos');
        Route::any('/create', 'ParentTagTwoController@create')->name('parent_tag_two_create');
        Route::any('/edit/{id}', 'ParentTagTwoController@edit')->name('parent_tag_two_edit');
        Route::get('/delete/{id}', 'ParentTagTwoController@delete')->name('parent_tag_two_delete');
    });

    Route::group(['prefix' => '/parent_tag_threes','middleware' => 'limit-access'], function () {
        Route::get('/', 'ParentTagThreeController@index')->name('parent_tag_threes');
        Route::any('/create', 'ParentTagThreeController@create')->name('parent_tag_three_create');
        Route::any('/edit/{id}', 'ParentTagThreeController@edit')->name('parent_tag_three_edit');
        Route::get('/delete/{id}', 'ParentTagThreeController@delete')->name('parent_tag_three_delete');
    });

    Route::group(['prefix' => '/parent_tag_fours','middleware' => 'limit-access'], function () {
        Route::get('/', 'ParentTagFourController@index')->name('parent_tag_fours');
        Route::any('/create', 'ParentTagFourController@create')->name('parent_tag_four_create');
        Route::any('/edit/{id}', 'ParentTagFourController@edit')->name('parent_tag_four_edit');
        Route::get('/delete/{id}', 'ParentTagFourController@delete')->name('parent_tag_four_delete');
    });

    Route::group(['prefix' => '/tags','middleware' => 'limit-access'], function () {
        Route::get('/', 'TagController@index')->name('tags');
        Route::any('/create', 'TagController@create')->name('tag_create');
        Route::any('/edit/{id}', 'TagController@edit')->name('tag_edit');
        Route::get('/delete/{id}', 'TagController@delete')->name('tag_delete');
    });

    Route::group(['prefix' => '/need_tags','middleware' => 'limit-access'], function () {
        Route::get('/', 'NeedTagController@index')->name('need_tags');
        Route::any('/create', 'NeedTagController@create')->name('need_tag_create');
        Route::any('/edit/{id}', 'NeedTagController@edit')->name('need_tag_edit');
        Route::get('/delete/{id}', 'NeedTagController@delete')->name('need_tag_delete');
    });

    Route::group(['prefix' => '/collections','middleware' => 'limit-access'], function () {
        Route::get('/', 'CollectionController@index')->name('collections');
        Route::any('/create', 'CollectionController@create')->name('collection_create');
        Route::any('/edit/{id}', 'CollectionController@edit')->name('collection_edit');
        Route::get('/delete/{id}', 'CollectionController@delete')->name('collection_delete');
    });

    Route::group(['prefix' => '/products','middleware' => 'limit-access'], function () {
        Route::any('/', 'ProductController@index')->name('products');
        Route::any('/create', 'ProductController@create')->name('product_create');
        Route::any('/edit/{id}', 'ProductController@edit')->name('product_edit');
        Route::get('/delete/{id}', 'ProductController@delete')->name('product_delete');
    });

    Route::group(['prefix' => '/temperatures','middleware' => 'admin-or-supervisor'], function () {
        Route::get('/', 'TemperatureController@index')->name('temperatures');
        Route::any('/create', 'TemperatureController@create')->name('temperature_create');
        Route::any('/edit/{id}', 'TemperatureController@edit')->name('temperature_edit');
        Route::get('/delete/{id}', 'TemperatureController@delete')->name('temperature_delete');
    });

    Route::group(['prefix' => '/students'], function () {
        Route::any('/', 'StudentController@index')->name('students');
        Route::any('/all', 'StudentController@indexAll')->name('student_all')->middleware('admin-or-supervisor');
        Route::get('/merged','StudentController@merge')->name('student_merged');
        Route::any('/banned', 'StudentController@banned')->name('student_banned')->middleware('admin-or-supervisor');
        Route::any('/archived', 'StudentController@archived')->name('student_archived')->middleware('admin-or-supervisor');
        Route::any('/create', 'StudentController@create')->name('student_create');
        Route::any('/edit/{call_back}/{id}', 'StudentController@edit')->name('student_edit');
        Route::get('/delete/{id}', 'StudentController@delete')->name('student_delete');
        Route::get('/call/{id}', 'StudentController@call')->name('student_call');
        Route::post('/tag', 'StudentController@tag')->name('student_tag');
        Route::post('/temperature', 'StudentController@temperature')->name('student_temperature');
        Route::any('/csv', 'StudentController@csv')->name('student_csv')->middleware('admin-or-supervisor');
        Route::any('/output-csv','StudentController@outputCsv')->name('student_output_csv')->middleware('limit-access');
        Route::post('/supporter', 'StudentController@supporter')->name('student_supporter');
        Route::get('/purchases/{id}', 'StudentController@purchases')->name('student_purchases');
        Route::any('/class/{id}', 'StudentController@class')->name('student_class');
        Route::get('/class/{student_id}/delete/{id}', 'StudentController@classDelete')->name('student_class_delete');
        Route::post('/class/{student_id}/add', 'StudentController@classAdd')->name('student_class_add');
    });
    Route::group(['prefix' => '/merge_students','middleware' => 'admin-or-supervisor'], function(){
        Route::get('/','MergeStudentsController@index')->name('merge_students_index');
        Route::post('/','MergeStudentsController@indexPost')->name('merge_students_index_post');
        Route::any('/create','MergeStudentsController@create')->name('merge_students_create');
        Route::any('/edit/{id}','MergeStudentsController@edit')->name('merge_students_edit');
        Route::get('/delete/{id}','MergeStudentsController@delete')->name('merge_students_delete');
        Route::post('/getStudents','MergeStudentsController@getStudents')->name('merge_students_get');
    });
    Route::group(['prefix' => '/assign_students','middleware' => 'admin-or-supervisor'], function(){
        Route::any('/','AssignGroupsOfStudentsToASponserController@index')->name('assign_students_index');
        Route::any('/create','AssignGroupsOfStudentsToASponserController@create')->name('assign_students_create');
        Route::any('/edit/{id}','AssignGroupsOfStudentsToASponserController@edit')->name('assign_students_edit');
        Route::get('/delete/{id}','AssignGroupsOfStudentsToASponserController@delete')->name('assign_students_delete');
    });

    Route::group(['prefix' => '/marketer'], function () {
        Route::any('profile','MarketerController@profile')->name('marketerprofile');
        Route::get('dashboard','MarketerController@dashboard')->name('marketerdashboard');
        Route::get('mystudents','MarketerController@mystudents')->name('marketermystudents');
        Route::get('students','MarketerController@students')->name('marketerstudents');
        Route::any('students/create','MarketerController@createStudents')->name('marketercreatestudents');
        Route::get('payments','MarketerController@payments')->name('marketerpayments');
        Route::get('circulars','MarketerController@circulars')->name('marketercirculars');
        Route::get('mails','MarketerController@mails')->name('marketermails');
        Route::get('discounts','MarketerController@discounts')->name('marketerdiscounts');
        Route::get('products','MarketerController@products')->name('marketerproducts');
        Route::get('code','MarketerController@code')->name('marketercode');
    });



    Route::group(['prefix' => '/sources','middleware' => 'admin-or-supervisor'], function () {
        Route::get('/', 'SourceController@index')->name('sources');
        Route::any('/create', 'SourceController@create')->name('source_create');
        Route::any('/edit/{id}', 'SourceController@edit')->name('source_edit');
        Route::get('/delete/{id}', 'SourceController@delete')->name('source_delete');
    });

    Route::group(['prefix' => '/users','middleware' => 'limit-access'], function () {
        Route::any('/', 'UserController@index')->name('user_alls');
        Route::any('/create', 'UserController@create')->name('user_all_create');
        Route::any('/edit/{id}', 'UserController@edit')->name('user_all_edit');
        Route::get('/delete/{id}', 'UserController@delete')->name('user_all_delete');
    });

    Route::group(['prefix' => '/messages'], function () {
        Route::get('/', 'MessageController@index')->name('messages');
        Route::get('/outbox', 'MessageController@indexOutbox')->name('messages_outbox');
        Route::any('/create', 'MessageController@create')->name('message_create');
        Route::get('/user/{id}', 'MessageController@userIndex')->name('message_user');
        Route::any('/user_create/{id}', 'MessageController@userCreate')->name('message_user_create');
        Route::any('/message_create/{id}', 'MessageController@messageCreate')->name('message_message_create');
        Route::get('/delete/{id}', 'MessageController@delete')->name('message_delete');
    });

    Route::group(['prefix' => '/purchases','middleware' => 'admin-or-supervisor'], function () {
        Route::get('/', 'PurchaseController@index')->name('purchases');
        Route::post('/','PurchaseController@indexPost')->name('purchases_post');
        Route::any('/create', 'PurchaseController@create')->name('purchase_create');
        Route::post('/open_site_edit_modal', 'PurchaseController@openSiteEditModal')->name('purchase_open_site_edit_modal');
        Route::post('/apply_site_edit_modal', 'PurchaseController@applySiteEditModal')->name('purchase_apply_site_edit_modal');
        Route::post('/open_manual_edit_modal', 'PurchaseController@openManualEditModal')->name('purchase_open_manual_edit_modal');
        Route::post('/apply_manual_edit_modal', 'PurchaseController@applyManualEditModal')->name('purchase_apply_manual_edit_modal');
        Route::post('/get_students','PurchaseController@getStudents')->name('purchase_get_students');
        Route::post('/get_products','PurchaseController@getProducts')->name('purchase_get_products');
        Route::post('/delete', 'PurchaseController@delete')->name('purchase_delete');
        Route::any('/test','PurchaseController@test')->name('purchase_test');
        Route::get('/assign-excel-for-purchases','PurchaseController@assignExcelForPurchaseGet')->name('pur_assign_excel_get');
        Route::post('/assign-excel-for-purchases','PurchaseController@assignExcelForPurchasePost')->name('pur_assign_excel_post');
    });

    Route::group(['prefix' => '/user_supporters'], function () {
        Route::get('/', 'SanadController@index')->name('sanads');
        Route::get('/', 'SupporterController@index')->name('user_supporters')->middleware('admin-or-supervisor');
        Route::get('/calls-get', 'SupporterController@callIndex')->name('user_supporter_calls')->middleware('admin-or-supervisor');
        Route::any('/supporter_calls', 'SupporterController@supporterCallIndex')->name('user_a_supporter_calls');
        Route::any('/call/{id}', 'SupporterController@acallIndex')->name('user_supporter_acall');
        Route::any('/students/{id}/{level?}', 'SupporterController@students')->name('supporter_allstudents');
        Route::any('/create', 'SupporterController@create')->name('user_supporter_create');
        Route::post('/change_pass', 'SupporterController@changePass')->name('user_supporter_changepass');
        Route::any('/delete_a_call/{user_id}/{id}','SupporterController@newDeleteCall')->name('user_supporter_delete_call');
        Route::post('/calls-post','SupporterController@callIndexPost')->name('user_supporter_calls_post');
        Route::get('/supporter-histories','SupporterController@supporterHistories')->name('student_supporter_histories')->middleware('limit-access');
        Route::post('/supporter-histories-post','SupporterController@supporterHistoriesPost')->name('student_supporter_histories_post')->middleware('limit-access');
    });

    Route::group(['prefix' => '/schools','middleware' => 'limit-access'], function () {
        Route::get('/', 'SchoolController@index')->name('schools');
        Route::any('/create', 'SchoolController@create')->name('school_create');
        Route::any('/edit/{id}', 'SchoolController@edit')->name('school_edit');
        Route::get('/delete/{id}', 'SchoolController@delete')->name('school_delete');
    });

    Route::group(['prefix' => '/sale_suggestions','middleware' => 'admin-or-supervisor'], function () {
        Route::get('/', 'SaleSuggestionController@index')->name('sale_suggestions');
        Route::any('/create', 'SaleSuggestionController@create')->name('sale_suggestion_create');
        Route::any('/edit/{id}', 'SaleSuggestionController@edit')->name('sale_suggestion_edit');
        Route::get('/delete/{id}', 'SaleSuggestionController@delete')->name('sale_suggestion_delete');
    });

    Route::group(['prefix' => '/call_results','middleware' => 'admin-or-supervisor'], function () {
        Route::get('/', 'CallResultController@index')->name('call_results');
        Route::any('/create', 'CallResultController@create')->name('call_result_create');
        Route::any('/edit/{id}', 'CallResultController@edit')->name('call_result_edit');
        Route::get('/delete/{id}', 'CallResultController@delete')->name('call_result_delete');
    });

    Route::group(['prefix' => '/notices','middleware' => 'admin-or-supervisor'], function () {
        Route::get('/', 'NoticeController@index')->name('notices');
        Route::any('/create', 'NoticeController@create')->name('notice_create');
        Route::any('/edit/{id}', 'NoticeController@edit')->name('notice_edit');
        Route::get('/delete/{id}', 'NoticeController@delete')->name('notice_delete');
    });

    Route::group(['prefix' => '/supporter_students'], function () {
        Route::any('/', 'SupporterController@student')->name('supporter_students');
        Route::any('/sanad/{id}', 'SupporterController@sanad')->name('supporter_sanads');
        Route::post('/changeLevel', 'SupporterController@changeLevelAjax')->name('change_level_ajax');
        Route::any('/l1', 'SupporterController@levelOneStudents')->name('student_level_1');
        Route::any('/l2', 'SupporterController@levelTwoStudents')->name('student_level_2');
        Route::any('/l3', 'SupporterController@levelThreeStudents')->name('student_level_3');
        Route::any('/l4', 'SupporterController@levelFourStudents')->name('student_level_4');
        Route::any('/students', 'SupporterController@newStudents')->name('supporter_student_new');
        Route::get('/income','SupporterController@showIncome')->name('supporter_student_income');
        Route::post('/income','SupporterController@showIncomePost')->name('supporter_student_income_post');
        Route::get('/merge-students','SupporterController@mergeStudents')->name('supporter_merge_students');
        Route::post('/call', 'SupporterController@call')->name('supporter_student_call');
        Route::post('/seen', 'SupporterController@seen')->name('supporter_student_seen');
        Route::any('/calls/{id}', 'SupporterController@calls')->name('supporter_student_allcall');
        Route::any('/delete_call/{user_id}/{id}', 'SupporterController@deleteCall')->name('supporter_student_deletecall');
        Route::any('/create', 'SupporterController@studentCreate')->name('supporter_student_create');
        Route::get('/purchases', 'SupporterController@getPurchases')->name('supporter_student_purchases_get');
        Route::post('/purchases','SupporterController@postPurchases')->name('supporter_student_purchases_post');
        Route::any('/all_missed_calls','SupporterController@allMissedCalls')->name('supporter_all_missed_calls');
        Route::any('/yesterday_missed_calls','SupporterController@yesterdayMissedCalls')->name('supporter_yesterday_missed_calls');
        Route::any('/no_need_calls_students','SupporterController@noNeedStudents')->name('no_need_students');
    });

    Route::group(['prefix' => '/circulars'], function () {
        Route::get('/', 'CircularController@index')->name('circulars');
        Route::any('/create', 'CircularController@create')->name('circular_create');
        Route::any('/edit/{id}', 'CircularController@edit')->name('circular_edit');
        Route::get('/delete/{id}', 'CircularController@delete')->name('circular_delete');
    });

    Route::group(['prefix' => '/reminders'], function () {
        Route::get('/{date?}', 'ReminderController@index')->name('reminders_get');
        Route::post('/{date?}', 'ReminderController@indexPost')->name('reminders_post');
        Route::get('/delete/{id}', 'ReminderController@delete')->name('reminder_delete');
    });

    Route::group(['prefix' => '/cities','middleware' => 'limit-access'], function () {
        Route::get('/', 'CityController@index')->name('cities');
        Route::any('/create', 'CityController@create')->name('city_create');
        Route::any('/edit/{id}', 'CityController@edit')->name('city_edit');
        Route::get('/delete/{id}', 'CityController@delete')->name('city_delete');
    });

    Route::group(['prefix' => '/provinces','middleware' => 'limit-access'], function () {
        Route::get('/', 'ProvinceController@index')->name('provinces');
        Route::any('/create', 'ProvinceController@create')->name('province_create');
        Route::any('/edit/{id}', 'ProvinceController@edit')->name('province_edit');
        Route::get('/delete/{id}', 'ProvinceController@delete')->name('province_delete');
    });

    Route::group(['prefix' => '/helps'], function () {
        Route::get('/', 'HelpController@index')->name('helps')->middleware('admin-or-supervisor');
        Route::get('/grid', 'HelpController@grid')->name('grid');
        Route::any('/create', 'HelpController@create')->name('help_create')->middleware('admin-or-supervisor');
        Route::any('/edit/{id}', 'HelpController@edit')->name('help_edit')->middleware('admin-or-supervisor');
        Route::get('/delete/{id}', 'HelpController@delete')->name('help_delete')->middleware('admin-or-supervisor');
    });

    Route::group(['prefix' => '/class_rooms','middleware' => 'limit-access'], function () {
        Route::get('/', 'ClassRoomController@index')->name('class_rooms');
        Route::any('/create', 'ClassRoomController@create')->name('class_room_create');
        Route::any('/edit/{id}', 'ClassRoomController@edit')->name('class_room_edit');
        Route::get('/delete/{id}', 'ClassRoomController@delete')->name('class_room_delete');
    });

    Route::group(['prefix' => '/lessons','middleware' => 'limit-access'], function () {
        Route::get('/', 'LessonController@index')->name('lessons');
        Route::any('/create', 'LessonController@create')->name('lesson_create');
        Route::any('/edit/{id}', 'LessonController@edit')->name('lesson_edit');
        Route::get('/delete/{id}', 'LessonController@delete')->name('lesson_delete');
    });

    Route::group(['prefix' => '/exams','middleware' => 'limit-access'], function () {
        Route::get('/', 'ExamController@index')->name('exams');
        Route::any('/create', 'ExamController@create')->name('exam_create');
        Route::any('/edit/{id}', 'ExamController@edit')->name('exam_edit');
        Route::get('/delete/{id}', 'ExamController@delete')->name('exam_delete');
        Route::any('/question/{exam_id}', 'ExamController@questions')->name('exam_questions');
        Route::any('/question/{exam_id}/create', 'ExamController@questionCreate')->name('exam_question_create');
        Route::get('/question/{exam_id}/delete/{id}', 'ExamController@questionDelete')->name('exam_question_delete');
    });
    Route::group(['prefix' => '/commissions'],function(){
        Route::get('/index/{id}','CommissionController@index')->name('commission');
        Route::any('/create/{id}','CommissionController@create')->name('commission_create');
        Route::any('/edit/{id}/{supporters_id}','CommissionController@edit')->name('commission_edit');
        Route::any('/delete/{id}','CommissionController@destroy')->name('commission_delete');
    });

    Route::group(['prefix' => '/sanads','middleware' => 'limit-access'], function () {
        Route::get('/', 'SanadController@index')->name('sanads');
        Route::any('/searchIndex', 'SanadController@indexWithSearch')->name('searchIndex');
        
        Route::any('/create', 'SanadController@create')->name('sanad_create');
        Route::any('/edit/{id}', 'SanadController@edit')->name('sanad_edit');
        Route::any('/update/{id}', 'SanadController@update')->name('sanad_update');
    });
});

