<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('contact.index');
});

Route::post('/contacts/confirm', function () {
    return view('contact._form');
});

Route::get('/tanks', function () {
    return view('contact.thanks');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin', fn () => '問合せ一覧')->name('admin.index');
    Route::get('/admin/contacts/{contact}', fn () => '問合せ詳細')->name('admin.show');
    Route::get('/admin/tags/{tag}/edit', fn () => 'タグ編集')->name('admin.tags.edit');
});
