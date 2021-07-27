<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', 'ldapController@index')->name('login.index');
Route::post('/login', 'ldapController@login')->name('login.login');

Route::middleware('auth')->group(function () {

    Route::namespace('Distributors')->group(function () {
        Route::get('/', 'ReportController@index')->name('report');
        Route::get('/all', 'ReportController@index')->name('report_retail');
    });

    Route::prefix('hats')->namespace('Hats')->group(function () {
        Route::get('/', 'HatsReportController@create')->name('hats.create');
    });

    Route::prefix('test')->group(function () {
        Route::get('/', 'Test2@handle');
    });

});





Route::fallback(function () {
    return redirect()->route('login.index');
});
