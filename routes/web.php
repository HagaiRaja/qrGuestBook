<?php

use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScannerController;

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

Route::get('/', [ScannerController::class, 'index'])->name('scanner.index');
Route::redirect('/dashboard', '/guests')->name('dashboard');
Route::get('/guests', [GuestController::class, 'index'])->name('guest.index');
Route::get('/guests/list', [GuestController::class, 'list'])->name('guest.list');
Route::get('/guests/create', [GuestController::class, 'create'])->name('guest.create');
Route::post('/guests', [GuestController::class, 'store'])->name('guest.store');
Route::get('/guests/{guest}/edit', [GuestController::class, 'edit'])->name('guest.edit');
Route::put('/guests/{guest}', [GuestController::class, 'update'])->name('guest.update');
Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])->name('guest.destroy');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
