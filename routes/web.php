<?php

use App\Http\Controllers\MarchantController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\ProductController;
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
Route::get('/settings/edit/{id?}', 'SettingsController@edit')->middleware('auth')->name('settings.edit');
Route::post('/settings/store/{settings}', 'SettingsController@store')->middleware('auth')->name('settings.store');
Route::get('/home', 'HomeController@index')->middleware('auth')->name('home');
Route::get('/dashboard', 'DashboardController@index')->middleware('auth')->name('dashboard');
Route::get('/customer', 'CustomerController@index')->middleware('auth')->name('customer');

Route::get('/journal', 'CustomerController@index')->middleware('auth')->name('journal');
Route::get('/journal/new', 'CustomerController@create')->middleware('auth')->name('journal.create');
Route::post('/journal/store', 'CustomerController@store')->middleware('auth')->name('journal.store');


// Products of QB
Route::get('/settings/products', 'ProductController@index')->name('products.index');
Route::get('/settings/products/create/{id?}', 'ProductController@create')->name('products.create');
Route::get('/settings/products/edit/{id}', 'ProductController@edit')->name('products.edit');
Route::put('/settings/products/update/{id}', 'ProductController@update')->name('products.update');
Route::get('/products/autocomplete', [ProductController::class, 'autocomplete'])->name('products.autocomplete');

Route::get('/settings/products/destroy/{id}', 'ProductController@destroy')->name('products.destroy');
Route::post('/settings/products', 'ProductController@store')->name('products.store');

Route::get('/settings/syncItems', 'ProductController@syncItems');


// Route for Quickbook Callback

Route::get('/quickbook/{user}/callback', 'QuickbookController@callback')->name('qb.callback');

//Rote for Marchant


Route::get('/marchants', [MarchantController::class, 'index'])->name('marchants.index');
Route::get('/marchants/create', [MarchantController::class, 'create'])->name('marchants.create');
Route::post('/marchants', [MarchantController::class, 'store'])->name('marchants.store');
Route::get('/marchants/edit/{id}', [MarchantController::class, 'edit'])->name('marchants.edit');
Route::put('/marchants/update/{id}', [MarchantController::class, 'update'])->name('marchants.update');
Route::delete('/marchants/destroy/{id}', [MarchantController::class, 'destroy'])->name('marchants.destroy');

//Route For payemnt Request
Route::get('/payment-requests', [PaymentRequestController::class, 'index'])->name('payment-requests.index');
Route::post('/payment-requests', [PaymentRequestController::class, 'store'])->name('payment-requests.store');
