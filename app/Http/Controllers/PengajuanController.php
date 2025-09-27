<?php

namespace App\Http\Controllers;

// TAMBAHKAN SEMUA MODEL YANG DIBUTUHKAN OLEH FORM CREATE
use App\Models\Pengajuan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\SumberDana;
use App\Models\Program;
use App\Models\Activity;
use App\Models\Kro;
use App\Models\Account;
use App\Models\Coa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PengajuanController extends Controller
{
    use AuthorizesRequests;

   public function index()
{
    $user = auth()->user();

    // Tambahkan with('details') untuk Eager Loading
    $query = Pengajuan::with('details');

    if (in_array($user->role, ['ppk', 'npwp', 'admin'])) {
        // Admin bisa melihat semua pengajuan
        $pengajuans = $query->latest()->paginate(10);
    } else {
        // User biasa hanya melihat pengajuan miliknya sendiri
        $pengajuans = $query->where('user_id', $user->id)->latest()->paginate(10);
    }
    
    return view('pengajuan.index', compact('pengajuans'));
}

    /**
     * Menampilkan form untuk membuat pengajuan baru.
     */
    public function create()
    {
        // Ambil semua data yang dibutuhkan untuk dropdown di form
        $ppkUsers = User::where('role', 'ppk')->get();
        $npwpUsers = User::where('role', 'npwp')->get();
        $sumberDanas = SumberDana::all();
        $programs = Program::all();
        // Anda bisa menambahkan data lain yang dibutuhkan di sini
        // $activities = Activity::all(); 
        // $kros = Kro::all();
        // $accounts = Account::all();

        // Kirim semua data tersebut ke view
        return view('pengajuan.create', compact(
            'ppkUsers', 
            'npwpUsers', 
            'sumberDanas',
            'programs'
            // 'activities',
            // 'kros',
            // 'accounts'
        ));
    }

    /**
     * Menyimpan pengajuan baru ke dalam database.
     */
   public function store(Request $request)
{
    $validatedData = $request->validate([
        'tanggal_pengajuan' => 'required|date',
        'ppk_user_id' => 'required|exists:users,id',
        'npwp_user_id' => 'required|exists:users,id',
        'sumber_dana_id' => 'required|exists:sumber_danas,id',
        'uraian' => 'required|string|max:255',
        'program_id' => 'required|exists:programs,id',
        'activity_id' => 'required|exists:activities,id',
        'kro_id' => 'required|exists:kros,id',
        'details' => 'required|array',
        'details.*.coa_id' => 'required|exists:coas,id',
        'details.*.jumlah_diajukan' => 'required|numeric|min:1',
    ]);
     try {
        DB::transaction(function () use ($validatedData) {
            // 1. Buat data pengajuan utama
            $pengajuan = Pengajuan::create([
                'user_id'           => auth()->id(),
                'tanggal_pengajuan' => $validatedData['tanggal_pengajuan'],
                'ppk_user_id'       => $validatedData['ppk_user_id'],
                'npwp_user_id'      => $validatedData['npwp_user_id'],
                'sumber_dana_id'    => $validatedData['sumber_dana_id'],
                'uraian'            => $validatedData['uraian'],
                'program_id'        => $validatedData['program_id'],
                'activity_id'       => $validatedData['activity_id'],
                'kro_id'            => $validatedData['kro_id'],
            ]);

            // 2. Loop dan simpan setiap rincian belanja (details)
            // Pastikan relasi 'details()' sudah ada di model Pengajuan
            foreach ($validatedData['details'] as $detail) {
                $pengajuan->details()->create([
                    'coa_id' => $detail['coa_id'],
                    'jumlah_diajukan' => $detail['jumlah_diajukan'],
                ]);
            }
        });
    } catch (\Exception $e) {
        // Jika terjadi error, kembali dengan pesan error
        return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
    }

    // Jika berhasil, redirect dengan pesan sukses
    return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dibuat.');
}
    
    // ... sisa method lainnya (show, edit, update, destroy) ...
    public function show(Pengajuan $pengajuan)
    {
        $this->authorize('view', $pengajuan);
        return view('pengajuan.show', compact('pengajuan'));
    }

   public function edit(Pengajuan $pengajuan)
    {
        // 1. Otorisasi: Memastikan hanya pemilik yang bisa mengakses halaman ini.
        $this->authorize('update', $pengajuan);

        // 2. Mengambil semua data master yang dibutuhkan untuk mengisi pilihan dropdown.
        $sumberDanas = SumberDana::all();
        $programs = Program::all();

        // 3. Eager Loading: Memuat relasi yang dibutuhkan oleh view agar efisien.
        // Ini akan mengambil data rincian (details) beserta relasi turunannya.
        $pengajuan->load(['details.coa.account']);

        // 4. Mengirim semua data yang sudah disiapkan ke view 'pengajuan.edit'.
        return view('pengajuan.edit', compact(
            'pengajuan',
            'sumberDanas',
            'programs'
        ));
    }

    public function update(Request $request, Pengajuan $pengajuan)
{
    // 1. Otorisasi: Pastikan hanya pemilik yang bisa menyimpan perubahan.
    $this->authorize('update', $pengajuan);

    // 2. Validasi: Pastikan semua data yang dikirim dari form edit valid.
    $validatedData = $request->validate([
        'tanggal_pengajuan' => 'required|date',
        'sumber_dana_id' => 'required|exists:sumber_danas,id',
        'uraian' => 'required|string|max:255',
        'program_id' => 'required|exists:programs,id',
        'activity_id' => 'required|exists:activities,id',
        'kro_id' => 'required|exists:kros,id',
        'details' => 'required|array', // Pastikan rinciannya ada
        'details.*.coa_id' => 'required|exists:coas,id',
        'details.*.jumlah_diajukan' => 'required|numeric|min:1',
    ]);

    try {
        // 3. Memulai Transaksi Database: Untuk menjaga integritas data.
        DB::transaction(function () use ($pengajuan, $validatedData) {
            
            // 4. Update data utama di tabel 'pengajuans'.
            $pengajuan->update([
                'tanggal_pengajuan' => $validatedData['tanggal_pengajuan'],
                'sumber_dana_id'    => $validatedData['sumber_dana_id'],
                'uraian'            => $validatedData['uraian'],
                'program_id'        => $validatedData['program_id'],
                'activity_id'       => $validatedData['activity_id'],
                'kro_id'            => $validatedData['kro_id'],
            ]);

            // 5. SINKRONISASI RINCIAN: Hapus semua rincian lama.
            $pengajuan->details()->delete();

            // 6. Buat ulang semua rincian dengan data baru dari form.
            foreach ($validatedData['details'] as $detail) {
                $pengajuan->details()->create([
                    'coa_id' => $detail['coa_id'],
                    'jumlah_diajukan' => $detail['jumlah_diajukan'],
                ]);
            }
        });

    } catch (\Exception $e) {
        // Jika terjadi error, kembali ke form dengan pesan error.
        return back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())->withInput();
    }

    // 7. Redirect kembali ke halaman index dengan pesan sukses.
    return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil diperbarui.');
}

    public function destroy(Pengajuan $pengajuan)
    {
        $this->authorize('delete', $pengajuan);
        $pengajuan->delete();
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
     
    public function getActivities($programId)
{
    try {
        $activities = \App\Models\Activity::where('program_id', $programId)->get();
        return response()->json($activities);
    } catch (\Exception $e) {
        // Catat error ke log dan kirim response JSON error, BUKAN HTML
        \Log::error('Error di getActivities: ' . $e->getMessage());
        return response()->json(['error' => 'Gagal mengambil data.'], 500);
    }
}

public function getKros($activityId)
{
    try {
        $kros = \App\Models\Kro::where('activity_id', $activityId)->get();
        return response()->json($kros);
    } catch (\Exception $e) {
        \Log::error($e);
        return response()->json(['error' => 'Gagal mengambil data KRO.'], 500);
    }
}

public function getAccounts()
{
    try {
        // Pastikan model 'Account' ada dan tabel 'accounts' ada isinya
        $accounts = Account::all();
        return response()->json($accounts);
    } catch (\Exception $e) {
        // Catat error yang sebenarnya ke dalam log Laravel untuk debugging
        \Log::error('Gagal mengambil data Akun Belanja: ' . $e->getMessage());
        
        // Kirim response JSON yang jelas bahwa ada error server
        return response()->json(['error' => 'Terjadi kesalahan pada server.'], 500);
    }
}
public function getCoas($accountId)
{
    try {
        // Ambil semua data COA yang memiliki 'account_id' yang sesuai
        // Pastikan nama kolom 'account_id' sudah benar
        $coas = \App\Models\Coa::where('account_id', $accountId)->get();
        
        return response()->json($coas);

    } catch (\Exception $e) {
        // Jika ada error, catat ke log dan kirim response JSON error
        \Log::error('Gagal mengambil data COA: ' . $e->getMessage());
        return response()->json(['error' => 'Gagal mengambil data COA dari server.'], 500);
    }
}
}