@extends('layouts.app')

@section('content')
<nav aria-label="breadcrumb" style="margin-left:3%;">
    <h6 class="font-weight-bolder mb-0">Tambah Anggota</h6>
  </nav>
<div class="container-fluid py-4">
    
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        
      <div class="card-body" >

        <form class="forms-sample" action="{{url('post-anggota')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="col-6 form-group">
              <label for="nama">Nama Nasabah</label>
              <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" value="{{old('nama')}}" name="nama">
              @error('nama')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <div class=" col-6 form-group ">
            <label for="NIK">NIK</label>
            <input type="text" class="form-control @error('NIK') is-invalid @enderror" id="NIK" value="{{old('NIK')}}" name="NIK">
            @error('NIK')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
          <div class=" col-6 form-group ">
              <label for="alamat">Alamat</label>
              <input type="text" class="form-control @error('alamat') is-invalid @enderror" id="alamat" value="{{old('alamat')}}" name="alamat">
              @error('alamat')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          
          <div class=" col-6 form-group ">
              <label for="no_telepon">No Telepon</label>
              <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" id="no_telepon" value="{{old('no_telepon')}}" name="no_telepon">
              @error('no_telepon')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>
          <button type="submit" class="btn btn-primary mr-2">Submit</button>
      </form>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
