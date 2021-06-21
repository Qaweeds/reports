<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/login', 'ldapController@index')->name('login.index');
Route::post('/login', 'ldapController@login')->name('login.login');

Route::get('/updateDistibutors', 'DistributorControllerManual@updateDistributors')->name('upd_dist');
//Route::get('/updateDistibutorstabel', 'DistributorControllerManual@updateDistributorsTimeSheet')->name('upd_tabel');
//Route::get('/updateDistibutorsStaff', 'DistributorControllerManual@updateDistributorsStaff')->name('upd_staff');
//Route::get('/updateDistibutorsstores', 'DistributorControllerManual@updateSuperStores')->name('upd_stores');

Route::get('/hats', 'HatsController@store');
//Route::get('/hatsdata', 'HatsController@get');
Route::get('/hatsdata', 'HatsReportController@create');

Route::get('/data', 'DistributorReportController@getData');
Route::get('/copy', 'XlsCopyController@saveCopy');
Route::get('/test', 'XlsCopyController@test');
Route::get('/', 'ReportController@auth')->name('report');
Route::get('/admin', 'AdminController');


Route::fallback(function () {
    return redirect()->route('login.index');
});



