<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\Admin\PengajuanDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Npwp\DashboardController;
use App\Http\Controllers\Ppk\DashboardController as PpkDashboardController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    if (Auth::user()->role == 'admin') {
        return redirect()->route('admin.pengajuan.index');
    } else {
        return view('dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('pengajuan', PengajuanController::class);
    
    Route::get('/get-bagian/{polsekId}', [PengajuanController::class, 'getBagian'])->name('getBagian');
     // RUTE BARU UNTUK DROPDOWN DINAMIS
    Route::get('/get-activities/{programId}', [PengajuanController::class, 'getActivities'])->name('getActivities');
    Route::get('/get-kros/{activityId}', [PengajuanController::class, 'getKros'])->name('getKros');

    
     Route::get('/get-accounts', [PengajuanController::class, 'getAccounts'])->name('getAccounts');
    Route::get('/get-coas/{accountId}', [PengajuanController::class, 'getCoas'])->name('getCoas');

});

// Grup Rute Khusus Admin
Route::middleware(['auth', 'is.admin'])->group(function () {
    Route::resource('/admin/categories', CategoryController::class);
    
    Route::get('/admin/pengajuan', [PengajuanDashboardController::class, 'index'])->name('admin.pengajuan.index');
    Route::post('/admin/pengajuan/{pengajuan}/approve', [PengajuanDashboardController::class, 'approve'])->name('admin.pengajuan.approve');
    Route::post('/admin/pengajuan/{pengajuan}/reject', [PengajuanDashboardController::class, 'reject'])->name('admin.pengajuan.reject');
    
    Route::get('/admin/pengajuan/export-pdf', [PengajuanDashboardController::class, 'exportPDF'])->name('admin.pengajuan.exportPDF');
    Route::resource('/admin/programs', ProgramController::class);
    
    // PASTIKAN RUTE INI ADA DAN TIDAK DI DALAM KOMENTAR
    Route::get('/admin/pengajuan/export-excel', [PengajuanDashboardController::class, 'exportExcel'])->name('admin.pengajuan.exportExcel');
});

//Rute untuk Admin NPWP
Route::middleware(['auth', 'is.npwp'])->prefix('npwp')->name('npwp.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/pengajuan/{pengajuan}/approve', [DashboardController::class, 'approve'])->name('pengajuan.approve');
    Route::post('/pengajuan/{pengajuan}/reject', [DashboardController::class, 'reject'])->name('pengajuan.reject');
    // Nanti kita akan tambahkan rute untuk approve & reject di sini
});

//Rute untuk admin PPK
Route::middleware(['auth', 'is.ppk'])->prefix('ppk')->name('ppk.')->group(function () {
    Route::get('/dashboard', [PpkDashboardController::class, 'index'])->name('dashboard');
    // TAMBAHKAN DUA RUTE INI
    Route::post('/pengajuan/{pengajuan}/approve', [PpkDashboardController::class, 'approve'])->name('pengajuan.approve');
    Route::post('/pengajuan/{pengajuan}/reject', [PpkDashboardController::class, 'reject'])->name('pengajuan.reject');
});
require __DIR__.'/auth.php';

