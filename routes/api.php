<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/students', 'StudentController@apiAddStudents')->name('api_add_students');
Route::post('/update_students', 'StudentController@apiUpdateStudents')->name('api_update_students');
Route::post('/products', 'ProductController@apiAddProducts')->name('api_add_products');
Route::post('/delete_products', 'ProductController@apiDeleteProducts')->name('api_delete_products');
Route::post('/delete_purchases', 'PurchaseController@apiDeletePurchases')->name('api_delete_purchases');
Route::post('/undelete_purchases', 'PurchaseController@apiUnDeletePurchases')->name('api_undelete_purchases');
Route::post('/purchases', 'PurchaseController@apiAddPurchases')->name('api_add_purchases');
Route::post('/marketers', 'MarketerController@apiCheckMarketer')->name('api_check_marketer');
Route::post('/login', 'UserController@apiLogin')->name('api_login');
Route::get('/filter_students', 'StudentController@apiFilterStudents')->name('api_filter_students');

/////////////////                 student academy API                 /////////////////////////////////////////

Route::get('/student_show/{id}', 'StudentController@apiShowStudent')->name('api_show_student');

Route::get('/student_index', 'StudentController@apiIndexStudents')->name('api_index_student');

Route::post('/student_store', 'StudentController@apiStoreStudents')->name('api_store_student');

Route::put('/student_update/{id}', 'StudentController@apiUpdateAcademyStudents')->name('api_update_academy_student');

Route::delete('/student_destroy/{id}', 'StudentController@apiDestroyStudent')->name('api_destroy_student');

Route::get('/getStudent/{id}','StudentController@apiShowStudent');



