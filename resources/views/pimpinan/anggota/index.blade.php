@extends('layouts.apppim')

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
        
        <div class="atas">
            <form class="d-flex  ms-auto my-4 my-lg-0 m-0" action="/searchuser" method="POST">
                @csrf
                <div class="input-group" style="width: 100%;">
                    <a href="/add-anggota" type="button" class="btn btn-primary btn-icon-text btn-sm " style="margin-right:50%">
                        <i class=" btn-icon-prepend" data-feather="plus" style="width: 1rem; margin-right:5px;"></i>Tambah</a>
                </div>
            </form>
                
        </div>
        <p class="card-description">
          
        </p>
        <div class="anggota-table-wrapper">
          <table class="table table-hover" style="width: 100%; " id="anggota-table">
            <thead>
              <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>NIK</th>
                  <th>Alamat</th>
                  <th>No Telepon</th>
                  <th>Aksi</th>
              </tr>
          </thead>
            
            
          </table>
        </div>
        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Data Anggota</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label for="edit_nama">Nama</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_NIK">NIK</label>
                        <input type="text" class="form-control" id="edit_NIK" name="NIK" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_alamat">Alamat</label>
                        <input type="text" class="form-control" id="edit_alamat" name="alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_no_telepon">No Telepon</label>
                        <input type="text" class="form-control" id="edit_no_telepon" name="no_telepon" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')

<script type="text/javascript">
  $(document).ready(function() {
      // Inisialisasi DataTables
      var table = $('#anggota-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: '{{ route('anggotapim.getData') }}',
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
              { data: 'nama', name: 'nama' },
              { data: 'NIK', name: 'NIK' },
              { data: 'alamat', name: 'alamat' },
              { data: 'no_telepon', name: 'no_telepon' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
      });

      // Handle tombol Edit
      $('#anggota-table').on('click', '.edit', function() {
            var id = $(this).data('id');
            var url = '{{ route("anggotapim.edit", ":id") }}';
            url = url.replace(':id', id);

            $.get(url, function(data) {
                $('#edit_id').val(data.id);
                $('#edit_nama').val(data.nama);
                $('#edit_NIK').val(data.NIK);
                $('#edit_alamat').val(data.alamat);
                $('#edit_no_telepon').val(data.no_telepon);
                $('#editModal').modal('show');
            });
        });

        // Handle form submit untuk update
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#edit_id').val();
            var url = '{{ route("anggota.update", ":id") }}';
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#editModal').modal('hide');
                    alert(response.success);
                    table.ajax.reload(null, false); // Reload data tanpa kembali ke halaman pertama
                },
                error: function(xhr) {
                    alert('Gagal memperbarui data.');
                }
            });
        });

      // Handle tombol Delete
      $('#anggota-table').on('click', '.delete', function() {
          var id = $(this).data('id');
          var url = '{{ route("anggota.destroy", ":id") }}';
          url = url.replace(':id', id);

          if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
              $.ajax({
                  url: url,
                  type: 'DELETE',
                  data: {
                      _token: '{{ csrf_token() }}'
                  },
                  success: function(response) {
                      if (response.success) {
                          alert(response.success);
                          // Reload DataTables setelah penghapusan berhasil
                          table.ajax.reload(null, false); // false agar tetap di halaman saat ini
                      }
                  },
                  error: function(xhr) {
                      alert('Gagal menghapus data.');
                  }
              });
          }
      });
  });
</script>

@endsection

