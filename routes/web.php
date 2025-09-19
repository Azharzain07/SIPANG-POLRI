<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\Admin\PengajuanDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\ProgramController;

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

require __DIR__.'/auth.php';

