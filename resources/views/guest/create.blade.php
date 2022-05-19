@extends('layouts.admin')

@section('content-wrapper')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Guest Registration</h1>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Guest Data</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" method="POST" action="{{env('APP_URL')}}/guests">
              @csrf
              <div class="card-body">
                <div class="form-group row">
                  <label for="name" class="col-md-4 col-form-label text-md-right">Name<span class="text-danger">*</span></label>

                  <div class="col-md-6">
                      <input id="name" 
                              type="text" 
                              class="form-control @error('name') is-invalid @enderror" 
                              name="name" 
                              value="{{ old('name') }}" 
                              autocomplete="name" 
                              placeholder="Guest Name"
                              autofocus>

                      @error('name')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>

                <div class="form-group row">
                  <label for="position" class="col-md-4 col-form-label text-md-right">Position<span class="text-danger">*</span></label>

                  <div class="col-md-6">
                      <select id="position" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position') }}" autocomplete="position" autofocus>
                        <option selected disabled>Position</option>
                        <option {{ (old('position') =='Keluarga Pria')?'selected':'' }}>Keluarga Pria</option>
                        <option {{ (old('position') =='Keluarga Wanita')?'selected':'' }}>Keluarga Wanita</option>
                        <option {{ (old('position') =='Sahabat')?'selected':'' }}>Sahabat</option>
                        <option {{ (old('position') =='Sahabat Pria')?'selected':'' }}>Sahabat Pria</option>
                        <option {{ (old('position') =='Sahabat Wanita')?'selected':'' }}>Sahabat Wanita</option>
                        <option {{ (old('position') =='Rekan Kerja')?'selected':'' }}>Rekan Kerja</option>
                        <option {{ (old('position') =='Rekan Kerja Pria')?'selected':'' }}>Rekan Kerja Pria</option>
                        <option {{ (old('position') =='Rekan Kerja Wanita')?'selected':'' }}>Rekan Kerja Wanita</option>
                      </select> 

                      @error('position')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>

                <div class="form-group row">
                  <label for="rsvp_count" class="col-md-4 col-form-label text-md-right"># of Guests<span class="text-danger">*</span></label>

                  <div class="col-md-6">
                      <input id="rsvp_count" 
                              type="number"
                              min="1"
                              max="4" 
                              class="form-control @error('rsvp_count') is-invalid @enderror" 
                              name="rsvp_count" 
                              value="{{ old('rsvp_count') }}" 
                              autocomplete="rsvp_count" 
                              placeholder="rsvp_count"
                              autofocus>

                      @error('rsvp_count')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>

                <div class="form-group row">
                  <label for="seat" class="col-md-4 col-form-label text-md-right">Seat</label>

                  <div class="col-md-6">
                      <input id="seat" 
                              type="text" 
                              class="form-control @error('seat') is-invalid @enderror" 
                              name="seat" 
                              value="{{ old('seat') }}" 
                              autocomplete="seat" 
                              placeholder="Seat"
                              autofocus>

                      @error('seat')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>

                <div class="form-group row">
                  <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                  <div class="col-md-6">
                      <input id="email" 
                              type="email" 
                              class="form-control @error('email') is-invalid @enderror" 
                              name="email" 
                              value="{{ old('email') }}" 
                              autocomplete="email" 
                              placeholder="Email"
                              autofocus>

                      @error('email')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>

                <div class="form-group row">
                  <label for="phone" class="col-md-4 col-form-label text-md-right">Phone</label>

                  <div class="col-md-6">
                      <input id="phone" 
                              type="tel" 
                              class="form-control @error('phone') is-invalid @enderror" 
                              name="phone" 
                              value="{{ old('phone') }}" 
                              autocomplete="phone" 
                              placeholder="phone (e.g. +628000000000)"
                              autofocus>

                      @error('phone')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>
                
              </div>
              <div class="card-footer" style="display: flex">
                <button class="btn btn-primary" type="submit"><i class="nav-icon fas fa-plus"></i> Add Guest</button>
              </div>
            </form>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Multiple Input (Upload Excel)</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" method="POST" action="{{env('APP_URL')}}/guests/excel" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="form-group row">
                  <label for="guest_list" class="col-md-4 col-form-label text-md-right">Upload File</label>

                  <div class="col-md-6">
                      <input id="guest_list" 
                              type="file"
                              accept=".xlsx"
                              class="form-control @error('guest_list') is-invalid @enderror" 
                              name="guest_list" 
                              value="{{ old('guest_list') }}" 
                              autocomplete="guest_list" 
                              autofocus>
                      <a href="{{env('APP_URL')}}/template/Template_Upload-Empty.xlsx">template - empty</a><br>
                      <a href="{{env('APP_URL')}}/template/Template_Upload-Filled_Example.xlsx">template - filled example</a>

                      @error('guest_list')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>
                
              </div>
              <div class="card-footer" style="display: flex">
                <button class="btn btn-primary" type="submit"><i class="nav-icon fas fa-plus"></i> Add Guest</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <checker-toaster title="Create Status" msg-success="Data has been stored" msg-failed="Data gagal"/>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
@endsection