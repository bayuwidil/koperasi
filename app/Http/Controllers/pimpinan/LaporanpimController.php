<?php

namespace App\Http\Controllers\pimpinan;
use App\Http\Controllers\Controller;

use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use PDF;

class LaporanpimController extends Controller
{
    // Menampilkan laporan anggota dengan filter tanggal
    public function laporanAnggota(Request $request)
    {
        // Ambil input start_date dan end_date
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Filter anggota berdasarkan tanggal bergabung
        $anggota = Anggota::query();

        if ($startDate && $endDate) {
            $anggota->whereBetween('created_at', [$startDate, $endDate]);
        }

        $anggota = $anggota->get();

        return view('pimpinan.laporan.anggota', compact('anggota'));
    }

    // Export laporan anggota menjadi PDF dengan filter tanggal
    public function exportAnggotaPDF(Request $request)
    {
        // Ambil input start_date dan end_date
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Filter anggota berdasarkan tanggal bergabung
        $anggota = Anggota::query();

        if ($startDate && $endDate) {
            $anggota->whereBetween('created_at', [$startDate, $endDate]);
        }

        $anggota = $anggota->get();

        // Buat PDF dengan menggunakan library DomPDF
        $pdf = PDF::loadView('pimpinan.laporan.anggota_pdf', compact('anggota', 'startDate', 'endDate'));

        // Mengunduh file PDF
        return $pdf->download('pimpinan.laporan_anggota.pdf');
    }

    public function laporanpinjaman(Request $request)
    {
        // Ambil input start_date dan end_date
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Filter pinjaman berdasarkan tanggal bergabung
        $pinjaman = Pinjaman::query();

        if ($startDate && $endDate) {
            $pinjaman->whereBetween('created_at', [$startDate, $endDate]);
        }

        $pinjaman = $pinjaman->get();

        return view('pimpinan.laporan.pinjaman', compact('pinjaman'));
    }

    public function exportpinjamanPDF(Request $request)
    {
        // Ambil input start_date dan end_date
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Filter pinjaman berdasarkan tanggal bergabung
        $pinjaman = Pinjaman::query();

        if ($startDate && $endDate) {
            $pinjaman->whereBetween('created_at', [$startDate, $endDate]);
        }

        $pinjaman = $pinjaman->get();

        // Buat PDF dengan menggunakan library DomPDF
        $pdf = PDF::loadView('pimpinan.laporan.pinjaman_pdf', compact('pinjaman', 'startDate', 'endDate'));

        // Mengunduh file PDF
        return $pdf->download('pimpinan.laporan_pinjaman.pdf');
    }
}
