<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('ho†me');

Route::get('/letters', function () {
  if (Auth::check()) {
      $reference_id = Auth::user()->reference_id;
      $letters = DB::table('letters')->where('reference_id', $reference_id)->get();
#      return $letters;
      return view('letters', compact ('letters'));
    } else {
      return view('welcome');
    }
});


Route::get('/pdf', 'PdfController@index')->name('pdf');

