<?php

namespace App\Http\Controllers\pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Angsuran;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class AngsuranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Anggota::with('pinjaman.angsurans')
                ->whereHas('pinjaman.angsurans') // Ambil semua anggota yang memiliki angsuran
                ->select('anggotas.*')
                ->orderBy('created_at', 'asc'); // Urutkan dari yang terbaru

            return DataTables::of($data)
                ->addIndexColumn() // Tambahkan nomor urut
                ->addColumn('total_angsuran', function ($row) {
                    return number_format($row->pinjaman->sum('total_pembayaran'), 2);
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-primary btn-sm lihatAngsuran" data-id="'.$row->id.'" data-nama="'.$row->nama.'">Lihat Riwayat</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pimpinan.angsuran.index');
    }

    public function getAngsuranByAnggota(Request $request)
    {
        $angsuranList = Angsuran::whereHas('pinjaman', function ($query) use ($request) {
                $query->where('anggota_id', $request->anggota_id);
            })
            ->orderBy('jatuh_tempo', 'desc') // Urutkan dari yang terbaru
            ->get()
            ->map(function ($angsuran) {
                $hariIni = Carbon::now();
                $jatuhTempo = Carbon::parse($angsuran->jatuh_tempo);
                $denda = 0;

                if ($jatuhTempo->lt($hariIni) && $angsuran->status == 0) {
                    $bulanTerlambat = max(1, $jatuhTempo->diffInMonths($hariIni)); 
                    $denda = ($angsuran->jumlah_bayar * 0.002) * $bulanTerlambat;
                } else { // Jika sudah dibayar, ambil dari database
                    $denda = $angsuran->denda;
                }
                

                return [
                    'id' => $angsuran->id,
                    'jatuh_tempo' => $jatuhTempo->format('Y-m-d'),
                    'jumlah_bayar' => round($angsuran->jumlah_bayar, 2),
                    'denda' => round($denda, 2),
                    'total_bayar' => round($angsuran->jumlah_bayar + $denda, 2),
                    'status' => $angsuran->status,
                ];
            });

        return response()->json($angsuranList);
    }

    public function bayarAngsuran(Request $request)
    {
        $request->validate([
            'angsuran_id' => 'required|exists:angsurans,id',
            'jumlah_bayar' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        $angsuran = Angsuran::findOrFail($request->angsuran_id);

        // Hitung denda jika telat lebih dari 1 hari
        $hariIni = Carbon::now();
        $jatuhTempo = Carbon::parse($angsuran->jatuh_tempo);
        $denda = 0;

        if ($jatuhTempo->lt($hariIni) && $angsuran->status == 0) {
            $bulanTerlambat = max(1, $jatuhTempo->diffInMonths($hariIni)); 
            $denda = ($angsuran->jumlah_bayar * 0.002) * $bulanTerlambat;
        }

        $totalBayar = $angsuran->jumlah_bayar + $denda;

        // Simpan pembayaran
        $angsuran->update([
            'jumlah_bayar' => $totalBayar,
            'status' => 1, // Tandai sebagai lunas
            'updated_at' => Carbon::now()
        ]);

        return response()->json(['success' => 'Angsuran berhasil dibayar.']);
    }
}
