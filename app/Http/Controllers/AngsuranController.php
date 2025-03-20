<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use Yajra\DataTables\Facades\DataTables;

class AngsuranController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $angsuran = Angsuran::whereHas('pinjaman', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->with('pinjaman.anggota')
                ->select('angsurans.*')
                ->orderBy('jatuh_tempo', 'desc'); // Urutkan dari yang terbaru
    
            return DataTables::of($angsuran)
                ->addColumn('sisa_pinjaman', function ($row) {
                    return number_format($row->pinjaman->total_pembayaran - $row->pinjaman->angsuran_bulanan, 2);
                })
                ->addColumn('denda', function ($row) {
                    if ($row->status == 1) {
                        return (float) $row->denda; // Ambil denda langsung dari database jika sudah lunas
                    }

                    $hariIni = now();
                    $jatuhTempo = Carbon::parse($row->jatuh_tempo);
                    $denda = 0;

                    if ($jatuhTempo->lt($hariIni) && $row->status == 0) {
                        // Hitung jumlah bulan yang telah lewat
                        $bulanTerlambat = $jatuhTempo->diffInMonths($hariIni);
                        
                        // Denda 0.2% per bulan keterlambatan
                        $denda = ($row->pinjaman->angsuran_bulanan * 0.002) * $bulanTerlambat;
                    }
    
                    return (float) $denda;
                })
                ->addColumn('jumlah_bayar', function ($row) {
                    return $row->jumlah_bayar; // Tambahkan kolom jumlah bayar
                })
                ->addColumn('jatuh_tempo', function ($row) {
                    return $row->jatuh_tempo; // Pastikan ini ada
                })
                ->addColumn('status', function ($row) {
                    return $row->status 
                        ? '<span class="badge bg-success">Lunas</span>' 
                        : '<span class="badge bg-warning">Belum Lunas</span>';
                })
                ->addColumn('action', function ($row) {
                    $hariIni = now();
                    $jatuhTempo = Carbon::parse($row->jatuh_tempo);
                    $denda = 0;

                    if ($jatuhTempo->lt($hariIni) && $row->status == 0) {
                        $bulanTerlambat = $jatuhTempo->diffInMonths($hariIni);
                        $denda = ($row->pinjaman->angsuran_bulanan * 0.002) * $bulanTerlambat;
                    }

                    if ($row->status == 1) {
                        return '<button class="btn btn-secondary btn-sm" disabled>Lunas</button>';
                    }
                    return '<button class="btn btn-success btn-sm payAngsuran" 
                                    data-id="'.$row->id.'" 
                                    data-amount="'.$row->pinjaman->angsuran_bulanan.'" 
                                    data-denda="'.floatval($denda).'" 
                                    data-nama="'.$row->pinjaman->anggota->nama.'"
                                    data-jatuh-tempo="'.$row->jatuh_tempo.'">
                                Bayar
                            </button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    
        return view('angsuran.index');
    }
    
    

    public function pay(Request $request) {
        $angsuran = Angsuran::where('id', $request->angsuran_id)
                            ->whereHas('pinjaman', function ($query) {
                                $query->where('user_id', Auth::id());
                            })->firstOrFail();
    
        $today = now();
        $jatuhTempo = $angsuran->jatuh_tempo;
        $denda = 0;
    
        if ($angsuran->status == 0 && $today->gt($jatuhTempo)) {
            $denda = $angsuran->jumlah_bayar * 0.002; // 0.2% denda jika belum dibayar
            $angsuran->denda = $denda; // Update denda di database
            $angsuran->save();
        } else {
            $denda = $angsuran->denda; // Ambil dari database jika sudah lunas
        }
    
        // Midtrans Config
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = config('services.midtrans.is_sanitized', true);
        Config::$is3ds = config('services.midtrans.is_3ds', true);
    
        $params = [
            'transaction_details' => [
                'order_id' => 'ANGS-' . time(),
                'gross_amount' => $angsuran->jumlah_bayar + $denda,
            ],
            'customer_details' => [
                'first_name' => $angsuran->pinjaman->anggota->nama,
                'email' => $angsuran->pinjaman->anggota->email,
            ]
        ];
    
        $snapToken = Snap::getSnapToken($params);
    
        return response()->json(['snap_token' => $snapToken]);
    }
    
    // Callback dari Midtrans untuk mengubah status angsuran menjadi lunas
    public function callback(Request $request) {
        $orderId = $request->order_id;
    
        $angsuran = Angsuran::where('id', str_replace('ANGS-', '', $orderId))->first();
        if ($angsuran) {
            $angsuran->status = 1; // Ubah menjadi lunas
            $angsuran->jumlah_bayar += $angsuran->denda; // Tambahkan denda ke jumlah_bayar
            $angsuran->denda = $angsuran->denda; // Pastikan denda tetap disimpan di database
            $angsuran->save();
        }
    
        return response()->json(['message' => 'Pembayaran berhasil dan status angsuran diperbarui.']);
    }
    
    public function listByPinjaman(Request $request) {
        $angsuran = Angsuran::where('pinjaman_id', $request->pinjaman_id)
                    ->orderBy('jatuh_tempo', 'asc')
                    ->get();
    
        return response()->json(['angsuran' => $angsuran]);
    }
    
    
}