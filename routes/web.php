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
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');


Route::get('/get-users', [App\Http\Controllers\Api\TableController::class, 'GetTableData'])->middleware('auth')->name('users');

Route::post('/add-user', [App\Http\Controllers\Api\TableController::class, 'AddTableData'])->middleware('auth')->name('addUser');
Route::post('/update-user', [App\Http\Controllers\Api\TableController::class, 'UpdateTableData'])->middleware('auth')->name('updateUser');
Route::post('/delete-user', [App\Http\Controllers\Api\TableController::class, 'DeleteTableData'])->middleware('auth')->name('deleteUser');


Route::get('/profile-data', [App\Http\Controllers\Auth\ProfileController::class, 'GetProfileData'])->middleware('auth')->name('profileData');
Route::post('/upload-image', [App\Http\Controllers\Auth\ProfileController::class, 'UploadImage'])->middleware('auth')->name('uploadImage');
