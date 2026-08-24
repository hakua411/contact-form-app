<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContactController::class, 'create'])->name('contacts.create');

Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contacts.confirm');

Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');

Route::get('/thanks', [ContactController::class, 'complete'])->name('contacts.complete');

Route::middleware('auth')->group(function () {
    Route::get('/admin', [ContactController::class, 'index'])->name('admin.index');
    Route::get('/admin/contacts/{contact}', [ContactController::class, 'show'])->name('admin.show');
    Route::delete('/admin/contacts/{contact}', [ContactController::class, 'destroy'])->name('admin.destroy');

    Route::post('/admin/tags', [TagController::class, 'store'])->name('admin.tags.store');
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit'])->name('admin.tags.edit');
    Route::put('/admin/tags/{tag}', [TagController::class, 'update'])->name('admin.tags.update');
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy'])->name('admin.tags.destroy');
});
