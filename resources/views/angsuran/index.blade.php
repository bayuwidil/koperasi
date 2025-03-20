@extends('layouts.app')

@section('content')
@if(session('success'))
    <div id="flash-message" class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<nav aria-label="breadcrumb" style="margin-left:3%;">
    <h3 class="font-weight-bolder mb-0">Anggota</h3>
</nav>

<div class="container-fluid py-4">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h3>Daftar Angsuran</h3>
        <table class="table table-hover" style="width: 100%;" id="angsuranTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jumlah Pinjaman</th>
                    <th>Angsuran Perbulan</th>
                    <th>Jatuh Tempo</th>
                    <th>Denda</th>
                    <th>Jumlah Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Pembayaran -->
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="payModalLabel">Konfirmasi Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Nama:</strong> <span id="modalNama"></span></p>
        <p><strong>Jumlah Angsuran:</strong> Rp <span id="modalAmount"></span></p>
        <p><strong>Denda:</strong> Rp <span id="modalDenda"></span></p>
        <p><strong>Total Bayar:</strong> Rp <span id="modalTotal"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success" id="confirmPay">Bayar Sekarang</button>
      </div>
    </div>
  </div>
</div>

@endsection

<!-- Midtrans Payment -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('scripts')
<script type="text/javascript">
    $(function () {
        var angsuranTable = $('#angsuranTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('angsuran.index') }}",
            columns: [
                { data: null, orderable: false, searchable: false, 
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    } 
                },
                { data: 'pinjaman.anggota.nama', name: 'pinjaman.anggota.nama' },
                { data: 'pinjaman.jumlah', name: 'pinjaman.jumlah', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp ') },
                { data: 'pinjaman.angsuran_bulanan', name: 'pinjaman.angsuran_bulanan', render: $.fn.dataTable.render.number(',', '.', 0, 'Rp ') },
                { data: 'jatuh_tempo', name: 'jatuh_tempo' },
                { 
                    data: 'denda', 
                    name: 'denda', 
                    render: function (data, type, row) {
                        if (row.status == 1) {
                            return row.denda ? $.fn.dataTable.render.number(',', '.', 0, 'Rp ').display(row.denda) : 'Rp 0';
                        }
                        return $.fn.dataTable.render.number(',', '.', 0, 'Rp ').display(data);
                    }
                },
                { 
                    data: 'jumlah_bayar', 
                    name: 'jumlah_bayar', 
                    render: $.fn.dataTable.render.number(',', '.', 0, 'Rp ') 
                },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
   

    let selectedAngsuranId = null;
    let totalBayar = 0;

    $('body').on('click', '.payAngsuran', function () {
        selectedAngsuranId = $(this).data('id');
        let amount = parseFloat($(this).data('amount')) || 0;
        let denda = parseFloat($(this).data('denda')) || 0; // Pastikan dalam angka
        let nama = $(this).data('nama') || 'Tanpa Nama';
        let jatuhTempo = $(this).data('jatuh-tempo') ?? 'Tidak Diketahui';


    

        totalBayar = amount + denda;

        $("#modalNama").text(nama);
        $("#modalAmount").text(amount.toLocaleString());
        $("#modalDenda").text(denda.toLocaleString());
        $("#modalTotal").text(totalBayar.toLocaleString());

        $("#payModal").modal('show');
    });

    $("#confirmPay").on("click", function () {
        $("#payModal").modal('hide');

        $.ajax({
            url: "{{ route('angsuran.pay') }}",
            type: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            data: { 
                angsuran_id: selectedAngsuranId, 
                total_bayar: totalBayar // Kirim total pembayaran yang sudah termasuk denda
            },
            beforeSend: function () {
                Swal.fire({
                    title: "Memproses Pembayaran...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function (response) {
                Swal.close();
                snap.pay(response.snap_token, {
                    onSuccess: function () {
                        $.ajax({
                            url: "{{ route('angsuran.callback') }}",
                            type: "POST",
                            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            data: { order_id: 'ANGS-' + selectedAngsuranId },
                            success: function () {
                                Swal.fire("Sukses!", "Pembayaran berhasil dan status diperbarui.", "success");
                                angsuranTable.ajax.reload();
                            }
                        });
                    },
                    onError: function () {
                        Swal.fire("Gagal!", "Pembayaran gagal. Coba lagi.", "error");
                    }
                });
            }
        });
    });
});
</script>
@endsection
