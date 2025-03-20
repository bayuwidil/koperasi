<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\PinjamanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\pimpinan\LaporanpimController;
use App\Http\Controllers\pimpinan\AnggotapimController;
use App\Http\Controllers\pimpinan\PinjamanpimController;
use App\Http\Controllers\pimpinan\AdminController;
use App\Http\Controllers\pimpinan\AngsuranController as PimpinanAngsuranController;

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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Auth::routes();

Route::middleware(['auth', 'role:admin'])->group(function () {
    
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('admin.dashboard');
    Route::get('/angsuran', [AngsuranController::class, 'index'])->name('angsuran.index');
    Route::post('/angsuran/pay', [AngsuranController::class, 'pay'])->name('angsuran.pay');
    Route::post('/angsuran/callback', [AngsuranController::class, 'callback'])->name('angsuran.callback');
    Route::get('/angsuran/list', [AngsuranController::class, 'listByPinjaman'])->name('angsuran.list');



    //anggota
    // route::get('/anggota',[AnggotaController::class, 'index']);
    // Route::get('anggota/get-data', [AnggotaController::class, 'getAnggota'])->name('anggota.getData');
    // route::get('/add-anggota',[AnggotaController::class, 'add']);
    // route::post('/post-anggota',[AnggotaController::class, 'store']);
    // Route::get('anggota/{id}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    // Route::post('anggota/{id}/update', [AnggotaController::class, 'update'])->name('anggota.update');
    // Route::delete('anggota/{id}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');

    // //pinjaman
    // Route::resource('pinjaman', PinjamanController::class);
    // Route::delete('pinjaman/{id}', [PinjamanController::class, 'destroy'])->name('pinjaman.destroy');

    // //laporan anggota
    // Route::get('/laporan/anggota', [LaporanController::class, 'laporanAnggota'])->name('laporan.anggota');
    // Route::get('/laporan/anggota/export-pdf', [LaporanController::class, 'exportAnggotaPDF'])->name('laporan.anggota.export-pdf');

    // //laoran pinjaman
    // Route::get('/laporan/pinjaman', [LaporanController::class, 'laporanpinjaman'])->name('laporan.pinjaman');
    // Route::get('/laporan/pinjaman/export-pdf', [LaporanController::class, 'exportpinjamanPDF'])->name('laporan.pinjaman.export-pdf');

});

Route::middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'indexpim'])->name('pimpinan.dashboard');

     //anggota
     route::get('/anggota',[AnggotapimController::class, 'index']);
     Route::get('anggota/get-data', [AnggotapimController::class, 'getAnggota'])->name('anggotapim.getData');
     route::get('/add-anggota',[AnggotapimController::class, 'add']);
     route::post('/post-anggota',[AnggotapimController::class, 'store']);
     Route::get('anggota/{id}/edit', [AnggotapimController::class, 'edit'])->name('anggotapim.edit');
     Route::post('anggota/{id}/update', [AnggotapimController::class, 'update'])->name('anggota.update');
     Route::delete('anggota/{id}', [AnggotapimController::class, 'destroy'])->name('anggota.destroy');
     Route::get('/get-anggotas', [AnggotapimController::class, 'getAnggotas'])->name('get.anggotas');

 
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/admin/{id}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{id}', [AdminController::class, 'update'])->name('admin.update');

    Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');

    //pinjaman
    Route::resource('pinjaman', PinjamanpimController::class);
    Route::delete('pinjaman/{id}', [PinjamanpimController::class, 'destroy'])->name('pinjaman.destroy');

    Route::get('/admin/angsuran/getByAnggota', [PimpinanAngsuranController::class, 'getAngsuranByAnggota'])->name('admin.angsuran.getAngsuranByAnggota');
    Route::get('/angsurans', [PimpinanAngsuranController::class, 'index'])->name('admin.angsuran.index');
    Route::post('/admin/angsuran/bayar', [PimpinanAngsuranController::class, 'bayarAngsuran'])->name('admin.angsuran.bayar');

    //laporan anggota
    Route::get('/laporan/anggota', [LaporanpimController::class, 'laporanAnggota'])->name('laporan.anggota');
    Route::get('/laporan/anggota/export-pdf', [LaporanpimController::class, 'exportAnggotaPDF'])->name('laporan.anggota.export-pdf');

    //laoran pinjaman
    Route::get('/laporan/pinjaman', [LaporanpimController::class, 'laporanpinjaman'])->name('laporan.pinjaman');
    Route::get('/laporan/pinjaman/export-pdf', [LaporanpimController::class, 'exportpinjamanPDF'])->name('laporan.pinjaman.export-pdf');
});