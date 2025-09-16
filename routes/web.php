<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\Admin\PengajuanDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (Auth::user()->role == 'admin') {
        return redirect()->route('admin.pengajuan.index');
    } else {
        return view('dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup Rute untuk User yang Sudah Login
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rute untuk CRUD Pengajuan oleh User
    Route::resource('pengajuan', PengajuanController::class);
    
    // Rute untuk mengambil data "Bagian" secara dinamis
    Route::get('/get-bagian/{polsekId}', [PengajuanController::class, 'getBagian'])->name('getBagian');
});

// Grup Rute Khusus Admin
Route::middleware(['auth', 'is.admin'])->group(function () {
    // Rute untuk CRUD Kategori (Polsek & Bagian)
    Route::resource('/admin/categories', CategoryController::class);
    
    // Rute untuk Halaman Review & Aksi Admin
    Route::get('/admin/pengajuan', [PengajuanDashboardController::class, 'index'])->name('admin.pengajuan.index');
    Route::post('/admin/pengajuan/{pengajuan}/approve', [PengajuanDashboardController::class, 'approve'])->name('admin.pengajuan.approve');
    Route::post('/admin/pengajuan/{pengajuan}/reject', [PengajuanDashboardController::class, 'reject'])->name('admin.pengajuan.reject');
    
    // Rute untuk Ekspor Data
    Route::get('/admin/pengajuan/export-pdf', [PengajuanDashboardController::class, 'exportPDF'])->name('admin.pengajuan.exportPDF');
    Route::get('/admin/pengajuan/export-excel', [PengajuanDashboardController::class, 'exportExcel'])->name('admin.pengajuan.exportExcel');
});

require __DIR__.'/auth.php';

