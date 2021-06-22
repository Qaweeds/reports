<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', 'ldapController@index')->name('login.index');
Route::post('/login', 'ldapController@login')->name('login.login');

Route::middleware('auth')->group(function () {

    Route::namespace('Distributors')->group(function () {
        Route::get('/', 'ReportController@index')->name('report');
        Route::get('/retail', 'ReportController@index')->name('report_retail');
    });

    Route::namespace('Hats')->group(function () {
        Route::get('/hats', 'HatsReportController@create');
    });


    Route::get('/test', 'Test');

});


Route::fallback(function () {
    return redirect()->route('login.index');
});



