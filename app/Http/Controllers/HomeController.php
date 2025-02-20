<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Http\Request;

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
        $anggota = Anggota::count();
        $anggotaBergabungHariIni = Anggota::whereDate('created_at', today())->count();

        $pinjamantotal = Pinjaman::sum('jumlah');
        $pinjamantoday = Pinjaman::whereDate('created_at', operator: today())->sum('jumlah');

        return view('home', compact('anggota', 'anggotaBergabungHariIni','pinjamantotal','pinjamantoday'));
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
