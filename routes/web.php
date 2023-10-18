<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::controller(FileController::class)
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/my-files/{folder?}', 'index')
            ->where('folder', '(.*)')
            ->name('myFiles');
        Route::get('/trash', 'trash')->name('trash');
        Route::post('/folder/create', 'createFolder')->name('folder.create');
        Route::post('/file/store', 'store')->name('file.store');
        Route::delete('/file', 'destroy')->name('file.destroy');
        Route::post('/file/restore', 'restore')->name('file.restore');
        Route::delete('/file/delete-forever', 'deleteForever')->name('file.deleteForever');
        Route::post('/file/addFavorites', 'addFavorites')->name('file.addFavorites');
        Route::post('/file/share', 'share')->name('file.share');
        Route::get('sharewithme', 'shareWithMe')->name('shareWithMe');
        Route::get('sharebyme', 'shareByMe')->name('shareByMe');
        Route::get('/file/download', 'download')->name('file.download');
        Route::get('/file/downloadShareWithMe', 'downloadShareWithMe')->name('file.downloadShareWithMe');
        Route::get('/file/downloadShareByMe', 'downloadShareByMe')->name('file.downloadShareByMe');
    });


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
