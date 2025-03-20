<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() 
{
    $userId = Auth::id(); // Ambil ID pengguna yang sedang login

    // Total pinjaman yang diambil oleh pengguna yang login
    $pinjamantotal = Pinjaman::where('user_id', $userId)
        ->whereHas('angsurans', function ($query) {
            $query->where('status', 0); // Hanya pinjaman dengan angsuran yang belum lunas
        })
        ->sum('jumlah');

    // Tagihan angsuran bulan ini
    $angsuranBulanIni = Angsuran::whereHas('pinjaman', function ($query) use ($userId) {
        $query->where('user_id', $userId);
    })
    ->where('status', 0) // Hanya angsuran yang belum lunas
    ->whereBetween('jatuh_tempo', [Carbon::now()->startOfMonth(), Carbon::now()->addMonth()->endOfMonth()])
    ->sum('jumlah_bayar');

    return view('home', compact('pinjamantotal', 'angsuranBulanIni'));
}

    public function indexpim()
    {
        $anggota = Anggota::count();
        $anggotaBergabungHariIni = Anggota::whereDate('created_at', today())->count();

        $pinjamantotal = Pinjaman::sum('jumlah');
        $pinjamantoday = Pinjaman::whereDate('created_at', operator: today())->sum('jumlah');

        return view('homepim', compact('anggota', 'anggotaBergabungHariIni','pinjamantotal','pinjamantoday'));
    }


}
