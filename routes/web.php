<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContactController::class, 'create'])->name('contacts.create');

Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contacts.confirm');

Route::post('/', [ContactController::class, 'store'])->name('contacts.store');

Route::get('/tanks', [ContactController::class, 'complete'])->name('contacts.complete');

Route::middleware('auth')->group(function () {
    Route::get('/admin', [ContactController::class, 'index'])->name('admin.index');
    Route::get('/admin/contacts/{contact}', [ContactController::class, 'show'])->name('admin.show');
    Route::delete('/admin/contacts/{contact}', [ContactController::class, 'destroy'])->name('admin.destroy');

    Route::get('/admin/tags/{tag}/edit', fn () => 'タグ編集')->name('admin.tags.edit');
});
