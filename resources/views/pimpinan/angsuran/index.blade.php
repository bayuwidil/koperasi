@extends('layouts.apppim')

@section('content')
<div class="container">
    <h3>Angsuran</h3>
    <table id="anggota-table" class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Total Angsuran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Modal Detail Angsuran -->
    <div class="modal fade" id="angsuranModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Angsuran <span id="namaAnggota"></span></h5>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Jatuh Tempo</th>
                                <th>Jumlah Bayar</th>
                                <th>Denda</th>
                                <th>Total Bayar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="listAngsuran"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        var table = $('#anggota-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.angsuran.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Nomor
                { data: 'nama', name: 'nama' },
                { data: 'total_angsuran', name: 'total_angsuran' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Klik tombol Lihat Angsuran
        $('body').on('click', '.lihatAngsuran', function () {
            var anggotaId = $(this).data('id');
            var nama = $(this).data('nama');

            $('#namaAnggota').text(nama);
            $('#listAngsuran').html('<tr><td colspan="6">Loading...</td></tr>');

            $.ajax({
                url: "{{ route('admin.angsuran.getAngsuranByAnggota') }}",
                method: "GET",
                data: { anggota_id: anggotaId },
                success: function (response) {
                    var rows = '';
                    response.forEach(function (angsuran) {
                        let statusBadge = angsuran.status == 1
                            ? '<span class="badge bg-success text-white p-2">Lunas</span>'
                            : '<span class="badge bg-danger text-white p-2">Belum Lunas</span>';

                        let tombolBayar = angsuran.status == 1 
                            ? '' 
                            : `<button class="btn btn-warning btn-sm bayarAngsuran" 
                                data-id="${angsuran.id}" data-total="${angsuran.total_bayar}" data-anggota="${anggotaId}">
                                Bayar
                            </button>`;

                        rows += `
                            <tr>
                                <td>${angsuran.jatuh_tempo}</td>
                                <td>Rp ${parseFloat(angsuran.jumlah_bayar).toLocaleString()}</td>
                                <td>Rp ${parseFloat(angsuran.denda).toLocaleString()}</td>
                                <td>Rp ${parseFloat(angsuran.total_bayar).toLocaleString()}</td>
                                <td>${statusBadge}</td>
                                <td>${tombolBayar}</td>
                            </tr>
                        `;
                    });
                    $('#listAngsuran').html(rows);
                    $('#angsuranModal').modal('show');
                }
            });
        });

        // Klik tombol Bayar Angsuran
        $('body').on('click', '.bayarAngsuran', function () {
            var angsuranId = $(this).data('id');
            var totalBayar = $(this).data('total');
            var anggotaId = $(this).data('anggota');

            if (confirm("Bayar angsuran sebesar Rp " + totalBayar.toLocaleString() + "?")) {
                $.ajax({
                    url: "{{ route('admin.angsuran.bayar') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        angsuran_id: angsuranId,
                        jumlah_bayar: totalBayar
                    },
                    success: function (response) {
                        alert(response.success);

                        // Refresh daftar angsuran tanpa reload halaman
                        $('.lihatAngsuran[data-id="' + anggotaId + '"]').click();
                        table.ajax.reload();
                    }
                });
            }
        });
    });
</script>
@endsection
