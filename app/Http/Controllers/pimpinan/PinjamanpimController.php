<?php

namespace App\Http\Controllers\pimpinan;
use App\Http\Controllers\Controller;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PinjamanpimController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $pinjamans = Pinjaman::with('anggota')->select('pinjamans.*');
            return DataTables::of($pinjamans)
                ->addColumn('action', function ($row) {
                    return '
                        <a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-primary btn-sm editPinjaman">Edit</a>
                        <a href="javascript:void(0)" data-id="'.$row->id.'" class="btn btn-danger btn-sm deletePinjaman">Delete</a>
                    ';
                })
                ->make(true);
        }
    
        // Mengambil data anggota dari database
        $anggotas = Anggota::all();
    
        // Kirim data anggota ke view
        return view('pimpinan.pinjaman.index', compact('anggotas'));
    }

    public function store(Request $request) {
        // Validasi input yang masuk
        $request->validate([
            'anggota_id' => 'required|exists:anggotas,id',
            'jumlah' => 'required|numeric|min:1',
            'tempo' => 'required|integer|min:1',
        ], [
            'anggota_id.required' => 'Anggota harus dipilih.',
            'anggota_id.exists' => 'Anggota yang dipilih tidak valid.',
            'jumlah.required' => 'Jumlah pinjaman wajib diisi.',
            'jumlah.numeric' => 'Jumlah pinjaman harus berupa angka.',
            'jumlah.min' => 'Jumlah pinjaman minimal harus 1.',
            'tempo.required' => 'Tempo wajib diisi.',
            'tempo.integer' => 'Tempo harus berupa angka.',
            'tempo.min' => 'Tempo minimal harus 1 bulan.',
        ]);
    
        // Ambil data anggota
        $anggota = Anggota::findOrFail($request->anggota_id);
        // dd($anggota);
        // Hitung pinjaman
        $jumlahPinjaman = $request->jumlah;
        $bungaPersen = 0.05; // 10% bunga tetap
        $tempo = $request->tempo;
    
        $totalBunga = ($jumlahPinjaman * $bungaPersen) * $tempo;
        $totalPembayaran = $jumlahPinjaman + $totalBunga;
        $angsuranBulanan = $totalPembayaran / $tempo;
    
        // Simpan pinjaman baru
        $pinjaman = Pinjaman::create([
            'anggota_id' => $request->anggota_id,
            'user_id' => $anggota->user_id, // Hubungkan user_id dari anggota
            'jumlah' => $jumlahPinjaman,
            'bunga' => $totalBunga,
            'tempo' => $tempo,
            'angsuran_bulanan' => $angsuranBulanan,
            'total_pembayaran' => $totalPembayaran,
        ]);
    
        // Simpan angsuran otomatis
        for ($i = 1; $i <= $tempo; $i++) {
            Angsuran::create([
                'pinjaman_id' => $pinjaman->id,
                'jumlah_bayar' => $angsuranBulanan,
                'jatuh_tempo' => now()->addMonths($i),
                'status' => 0, // 0 = Belum Lunas
            ]);
        }
    
        return response()->json(['success' => 'Pinjaman berhasil disimpan dan angsuran telah dibuat.']);
    }

    public function edit($id) {
        // Ambil data pinjaman berdasarkan ID
        $pinjaman = Pinjaman::find($id);
        return response()->json($pinjaman);
    }
    
    public function destroy($id)
    {
        $pinjaman = Pinjaman::find($id);
        if ($pinjaman) {
            $pinjaman->delete();
            return response()->json(['success' => 'Pinjaman berhasil dihapus.']);
        }

        return response()->json(['error' => 'Pinjaman tidak ditemukan.'], 404);
    }
}
