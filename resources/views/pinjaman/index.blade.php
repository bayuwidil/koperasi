@extends('layouts.app')

@section('content')
<div class="container">
    <h3> Pinjaman</h3>
    <table class="table table-bordered" id="pinjamanTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Jumlah Pinjaman</th>
                <th>Bunga (5%)</th>
                <th>Tempo (Bulan)</th>
                <th>Angsuran Bulanan</th>
                <th>Total Pembayaran</th>
                <th>Status</th>
                
            </tr>
        </thead>
    </table>
</div> 


@endsection

@section('scripts')
<script type="text/javascript">
    $(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // Tampilkan DataTable
        var table = $('#pinjamanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pinjamans.index') }}",
                dataSrc: function (json) {
                    console.log(json);
                    return json.data;
                }
            },
            columns: [
                {
                    data: null, 
                    name: 'nomor', 
                    orderable: false, 
                    searchable: false, 
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'anggota.nama', name: 'anggota.nama'},
                {
                    data: 'jumlah', 
                    name: 'jumlah',
                    render: function (data, type, row) {
                        return parseFloat(data).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                    }
                },
                {
                    data: 'bunga', 
                    name: 'bunga',
                    render: function (data, type, row) {
                        return parseFloat(data).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                    }
                },
                {data: 'tempo', name: 'tempo'},
                {
                    data: 'angsuran_bulanan', 
                    name: 'angsuran_bulanan',
                    render: function (data, type, row) {
                        return parseFloat(data).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                    }
                },
                {
                    data: 'total_pembayaran', 
                    name: 'total_pembayaran',
                    render: function (data, type, row) {
                        return parseFloat(data).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                    }
                },
                {
                    data: 'status', 
                    name: 'status',
                    render: function (data, type, row) {
                        return data === 'Lunas' 
                            ? '<span class="badge bg-success">Lunas</span>' 
                            : '<span class="badge bg-warning">Belum Lunas</span>';
                    }
                },
            ]
        });
    });
</script>

@endsection
