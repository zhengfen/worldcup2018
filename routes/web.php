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

Route::get('/','FrontController@welcome')->name('root');
Route::get('/welcome','FrontController@welcome')->name('welcome');
// Auth
Auth::routes();
Route::get('/home', 'FrontController@welcome')->name('home');
// Team 
Route::get('/teams','TeamController@index');
// Group
Route::get('/groups','GroupController@index');
// Matches
Route::get('/matches','MatchController@index')->name('matches');
Route::post('/matches/update_score_home','MatchController@update_score_home');
Route::post('/matches/update_score_away','MatchController@update_score_away');
Route::post('/matches/update_scores','MatchController@update_scores');

// Pronostic
Route::get('/pronostics','PronosticController@index')->name('pronostics');
Route::get('/pronostics_json','PronosticController@index_json');
Route::post('/pronostics/update_scores','PronosticController@update_scores');

// classement user points ranking
Route::get('/ranking','FrontController@ranking')->name('ranking');
Route::get('/ranking_json','FrontController@ranking_json')->name('ranking_json');

// admin for set users status
Route::get('/admin','AdminController@index')->name('admin');
Route::post('/admin/status','AdminController@toggle_status')->name('toggle_status');

// vue route
Route::get('/vue/{vue_capture?}', function () {
 return view('vue.index');
})->where('vue_capture', '[\/\w\.-]*')->middleware('auth');
