<?php

use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\HomeController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

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
Route::get('qr-code-g', function () {
  $id = (string) Str::uuid();
  QrCode::size(500)
          ->format('png')
          ->generate($id, public_path("temp/qrcode.png"));
  $path = "temp/qrcode.png";
return view('qrCode', compact('path'));
  
});

Route::get('qr-code-g/{id}', function ($id) {
  $path = 'temp/'.$id . '.png';
return view('qrCode', compact('path'));
  
});

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::get('/scanners', [ScannerController::class, 'index'])->name('scanner.index');
Route::post('/scanners', [ScannerController::class, 'update'])->name('scanner.update');
Route::get('/scanners/show', [ScannerController::class, 'show'])->name('scanner.show');
Route::get('/scanners/scan', [ScannerController::class, 'scan'])->name('scanner.scan');
Route::get('/scanners/check/{qr_code}', [ScannerController::class, 'check'])->name('scanner.check');

Route::redirect('/dashboard', '/guests')->name('dashboard');
Route::get('/guests', [GuestController::class, 'index'])->name('guest.index');
Route::get('/guests/check', [GuestController::class, 'check'])->name('guest.check');
Route::get('/guests/{guest}/toggle/{command}', [GuestController::class, 'toggle'])->name('guest.toggle');
Route::get('/guests/export', [GuestController::class, 'export'])->name('guest.export');
Route::get('/guests/list', [GuestController::class, 'list'])->name('guest.list');
Route::get('/guests/create', [GuestController::class, 'create'])->name('guest.create');
Route::post('/guests/excel', [GuestController::class, 'store_excel'])->name('guest.store_excel');
Route::post('/guests', [GuestController::class, 'store'])->name('guest.store');
Route::get('/guests/{guest}/edit', [GuestController::class, 'edit'])->name('guest.edit');
Route::post('/guests/{guest}', [GuestController::class, 'update'])->name('guest.update');
Route::get('/guests/{guest}/destroy', [GuestController::class, 'destroy'])->name('guest.destroy');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
