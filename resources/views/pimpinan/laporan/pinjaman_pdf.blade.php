<!DOCTYPE html>
<html>
<head>
    <title>Laporan Daftar Anggota</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

    <h2>Laporan Anggota</h2>

    @if($startDate && $endDate)
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    @endif

    <table>
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

</body>
</html>
