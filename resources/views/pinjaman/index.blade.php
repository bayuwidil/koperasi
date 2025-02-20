@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Manajemen Pinjaman</h3>
    <a class="btn btn-success mb-3" id="createNewPinjaman">Tambah Pinjaman</a>
    <table class="table table-bordered" id="pinjamanTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Jumlah Pinjaman</th>
                <th>Bunga (10%)</th>
                <th>Tempo (Bulan)</th>
                <th>Angsuran Bulanan</th>
                <th>Total Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal untuk Tambah/Edit Pinjaman -->
<div class="modal fade" id="ajaxModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalHeading"></h4>
            </div>
            <div class="modal-body">
                
                <form id="pinjamanForm" name="pinjamanForm" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="pinjaman_id" id="pinjaman_id">
                    
                    <!-- Error message display -->
                    <div class="alert alert-danger" id="errorMessages" style="display:none;"></div>

                    <div class="form-group">
                        <label for="anggota_id">Nama Anggota</label>
                        <select name="anggota_id" id="anggota_id" class="form-control" style="width: 100%;">
                            @foreach($anggotas as $anggota)
                                <option value="{{ $anggota->id }}">{{ $anggota->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    

                    <div class="form-group">
                        <label for="jumlah">Jumlah Pinjaman</label>
                        <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                    </div>

                    <div class="form-group">
                        <label for="tempo">Tempo (Bulan)</label>
                        <input type="number" class="form-control" id="tempo" name="tempo" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
            ajax: "{{ route('pinjaman.index') }}",
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
                {data: 'action', name: 'action', orderable: false, searchable: false, render: function (data, type, row) {
                return `
                    <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-primary btn-sm editPinjaman">Edit</a>
                    <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-danger btn-sm deletePinjaman">Delete</a>
                `;
            }},
            ]
        });

        // Modal untuk Tambah Pinjaman
        $('#createNewPinjaman').click(function () {
            $('#saveBtn').val("create-pinjaman");
            $('#pinjaman_id').val('');
            $('#pinjamanForm').trigger("reset");
            $('#errorMessages').hide();
            $('#modalHeading').html("Tambah Pinjaman");
            $('#ajaxModal').modal('show');
        });

        // Inisialisasi Select2 pada dropdown anggota
        $('#anggota_id').select2({
            placeholder: "Pilih Nama Anggota",
            allowClear: true
        });

        // Simpan Pinjaman
        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Simpan...');

            $.ajax({
                data: $('#pinjamanForm').serialize(),
                url: "{{ route('pinjaman.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#pinjamanForm').trigger("reset");
                    $('#ajaxModal').modal('hide');
                    $('#saveBtn').html('Simpan');
                    table.draw();
                },
                error: function (xhr, status, error) {
                    var response = xhr.responseJSON;
                    var errorMessages = '';

                    // Cek jika ada properti errors dalam respons
                    if (response && response.errors) {
                        $.each(response.errors, function (key, value) {
                            errorMessages += '<p>' + value[0] + '</p>';
                        });
                    } else {
                        // Jika respons tidak memiliki errors, tampilkan pesan error umum
                        errorMessages = '<p>Terjadi kesalahan, coba lagi.</p>';
                    }

                    $('#errorMessages').html(errorMessages).show();
                    $('#saveBtn').html('Simpan');
                }
            });
        });


        // Edit Pinjaman
        $('body').on('click', '.editPinjaman', function () {
            var pinjaman_id = $(this).data('id');
            $.get("{{ route('pinjaman.index') }}" +'/' + pinjaman_id +'/edit', function (data) {
                $('#modalHeading').html("Edit Pinjaman");
                $('#saveBtn').val("edit-pinjaman");
                $('#ajaxModal').modal('show');
                $('#errorMessages').hide();
                $('#pinjaman_id').val(data.id);
                $('#anggota_id').val(data.anggota_id);
                $('#jumlah').val(data.jumlah);
                $('#tempo').val(data.tempo);
            })
        });

        // Delete Pinjaman
        // Delete Pinjaman
        $('body').on('click', '.deletePinjaman', function () {
            var pinjaman_id = $(this).data("id");
            if (confirm("Apakah Anda yakin ingin menghapus pinjaman ini?")) {
                $.ajax({
                    type: "DELETE",
                    url: "/pinjaman/" + pinjaman_id,
                    success: function (data) {
                        if (data.success) {
                            alert(data.success);
                            $('#pinjamanTable').DataTable().ajax.reload(); // Reload DataTable setelah hapus
                        } else if (data.error) {
                            alert(data.error);
                        }
                    },
                    error: function (data) {
                        console.log('Error:', data);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            }
        });


    });
</script>

@endsection
