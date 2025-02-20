@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Laporan Daftar Pinjaman</h2>

    <form action="{{ route('laporan.pinjaman') }}" method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="start_date">Dari Tanggal:</label>
                <input type="date" name="start_date" class="form-control" value="{{ request()->get('start_date') }}">
            </div>
            <div class="col-md-4">
                <label for="end_date">Sampai Tanggal:</label>
                <input type="date" name="end_date" class="form-control" value="{{ request()->get('end_date') }}">
            </div>
            <div class="col-md-4">
                <label>&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                    <a href="{{ route('laporan.pinjaman.export-pdf', ['start_date' => request()->get('start_date'), 'end_date' => request()->get('end_date')]) }}" class="btn btn-danger">Download PDF</a>
                </div>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Jumlah</th>
                <th>Bunga</th>
                <th>Tempo</th>
                <th>Angsuran Perbulan</th>
                <th>Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pinjaman as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->anggota->nama }}</td>
                <td>Rp.{{ number_format($item->jumlah) }},00</td>
                <td>Rp.{{ number_format($item->bunga) }},00</td>
                <td>{{ $item->tempo }} bulan</td>
                <td>Rp.{{ number_format($item->angsuran_bulanan) }},00</td>
                <td>Rp.{{ number_format($item->total_pembayaran) }},00</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
