@extends('layouts.apppim')

@section('content')
<div class="container">
    <h3>Manajemen Admin</h3>
    <a class="btn btn-success mb-3" id="createNewAdmin">Tambah Admin</a>
    <table class="table table-bordered" id="adminTable">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Modal untuk Tambah/Edit Admin -->
<div class="modal fade" id="ajaxAdminModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="adminModalHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="adminForm" name="adminForm" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="admin_id" id="admin_id">

                    <!-- Error message display -->
                    <div class="alert alert-danger" id="errorMessages" style="display:none;"></div>

                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" minlength="6">
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" minlength="6">
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="saveAdminBtn">Simpan</button>
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
        // Setup CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Tampilkan DataTable
        var adminTable = $('#adminTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.index') }}",
            columns: [
                { 
                    data: null, 
                    orderable: false, 
                    searchable: false, 
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-primary btn-sm editAdmin">Edit</a>
                            <a href="javascript:void(0)" data-id="${row.id}" class="btn btn-danger btn-sm deleteAdmin">Hapus</a>
                        `;
                    } 
                },
            ]
        });

        // Modal Tambah Admin
        $('#createNewAdmin').click(function () {
            $('#saveAdminBtn').val("create-admin");
            $('#admin_id').val('');
            $('#adminForm').trigger("reset");
            $('#errorMessages').hide();
            $('#adminModalHeading').html("Tambah Admin");
            $('#ajaxAdminModal').modal('show');
        });

        // Simpan Admin Baru/Update
        $('#saveAdminBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Menyimpan...');

            var admin_id = $('#admin_id').val();
            var url = admin_id ? `/admin/${admin_id}` : "{{ route('admin.store') }}";
            var method = admin_id ? "PUT" : "POST";

            $.ajax({
                data: $('#adminForm').serialize(),
                url: url,
                type: method,
                dataType: 'json',
                success: function (data) {
                    $('#adminForm').trigger("reset");
                    $('#ajaxAdminModal').modal('hide');
                    $('#saveAdminBtn').html('Simpan');
                    adminTable.draw();
                },
                error: function (xhr) {
                    var response = xhr.responseJSON;
                    var errorMessages = '';

                    if (response && response.errors) {
                        $.each(response.errors, function (key, value) {
                            errorMessages += `<p>${value[0]}</p>`;
                        });
                    } else {
                        errorMessages = '<p>Terjadi kesalahan, coba lagi.</p>';
                    }

                    $('#errorMessages').html(errorMessages).show();
                    $('#saveAdminBtn').html('Simpan');
                }
            });
        });

        // Modal Edit Admin
        $('body').on('click', '.editAdmin', function () {
            var admin_id = $(this).data('id');
            $.get(`/admin/${admin_id}/edit`, function (data) {
                $('#adminModalHeading').html("Edit Admin");
                $('#saveAdminBtn').val("edit-admin");
                $('#ajaxAdminModal').modal('show');
                $('#errorMessages').hide();

                $('#admin_id').val(data.id);
                $('#name').val(data.name);
                $('#email').val(data.email).prop('readonly', true); // Disable email
                $('#password').val('');
                $('#password_confirmation').val('');
            });
        });

        // Delete Admin
        $('body').on('click', '.deleteAdmin', function () {
            var admin_id = $(this).data("id");
            if (confirm("Apakah Anda yakin ingin menghapus admin ini?")) {
                $.ajax({
                    type: "DELETE",
                    url: `/admin/${admin_id}`,
                    success: function () {
                        adminTable.draw();
                    },
                    error: function () {
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                });
            }
        });
    });
</script>
@endsection
