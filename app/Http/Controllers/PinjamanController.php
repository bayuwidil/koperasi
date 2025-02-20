<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PinjamanController extends Controller
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
        return view('pinjaman.index', compact('anggotas'));
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
    
        // Mengambil data dari request
        $jumlahPinjaman = $request->input('jumlah');
        $bungaPersen = 0.10; // Bunga 10% tetap
        $tempo = $request->input('tempo');
    
        // Menghitung total bunga
        $totalBunga = ($jumlahPinjaman * $bungaPersen) * $tempo;
    
        // Menghitung total pembayaran
        $totalPembayaran = $jumlahPinjaman + $totalBunga;
    
        // Menghitung angsuran bulanan
        $angsuranBulanan = $totalPembayaran / $tempo;
    
        // Simpan atau update data pinjaman
        Pinjaman::updateOrCreate(
            ['id' => $request->pinjaman_id], // Jika ada ID pinjaman, maka update
            [
                'anggota_id' => $request->input('anggota_id'),
                'jumlah' => $jumlahPinjaman,
                'bunga' => $totalBunga, // Simpan total bunga (bukan dalam persentase)
                'tempo' => $tempo,
                'angsuran_bulanan' => $angsuranBulanan,
                'total_pembayaran' => $totalPembayaran,
            ]
        );
    
        return response()->json(['success' => 'Pinjaman berhasil disimpan.']);
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
