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

Route::get('/', function () {
    return view('welcome');
})->middleware('guest');

Route::get('/login', function () {
    return view('welcome');
})->middleware('guest');
Route::get('/register', function () {
    return view('auth/register');
})->middleware('guest');
//$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');

Auth::routes();

Route::get('/change/password', 'ChangePasswordController@index')->middleware('auth')->name('change.password');
Route::post('/change/password/store', 'ChangePasswordController@store')->middleware('auth')->name('change.password.store');

Route::get('/settings', 'SettingsController@index')->middleware('auth')->name('settings');
Route::get('/settings/edit/{id}', 'SettingsController@edit')->middleware('auth')->name('settings.edit');
Route::post('/settings/store/{settings}', 'SettingsController@store')->middleware('auth')->name('settings.store');
Route::get('/home', 'HomeController@index')->middleware('auth')->name('home');
Route::get('/dashboard', 'DashboardController@index')->middleware('auth')->name('dashboard');
Route::get('/customer', 'CustomerController@index')->middleware('auth')->name('customer');

Route::get('/journal', 'CustomerController@index')->middleware('auth')->name('journal');
Route::get('/journal/new', 'CustomerController@create')->middleware('auth')->name('journal.create');
Route::post('/journal/store', 'CustomerController@store')->middleware('auth')->name('journal.store');


// Products of QB
Route::get('/settings/products', 'ProductController@index')->name('products.index');
Route::get('/settings/products/create/{id}', 'ProductController@create')->name('products.create');
Route::get('/settings/products/destroy/{id}', 'ProductController@destroy')->name('products.destroy');
Route::post('/settings/products', 'ProductController@store')->name('products.store');


// Route for Quickbook Callback

Route::get('/quickbook/{user}/callback', 'QuickbookController@callback')->name('qb.callback');
