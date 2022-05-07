@extends('layouts.admin')

@section('content-wrapper')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Scanner Configuration</h1>
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
              <h3 class="card-title">Scanner Settings</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form role="form" method="POST" action="{{env('APP_URL')}}/scanners" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="form-group row">
                  <label for="background_img" class="col-md-4 col-form-label text-md-right">Background Image</label>

                  <div class="col-md-6">
                      <input id="background_img" 
                              type="file"
                              accept="image/gif, image/jpeg, image/png"
                              class="form-control @error('background_img') is-invalid @enderror" 
                              name="background_img" 
                              value="{{ old('background_img') }}" 
                              autocomplete="background_img" 
                              autofocus>
                      <a href="{{ $scanner->backgroundImageLink() }}">{{ ($scanner->background_img)?'Current background image':'' }}</a>

                      @error('background_img')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>
                
              </div>
              <div class="card-footer" style="display: flex">
                <button class="btn btn-primary" type="submit"><i class="nav-icon fas fa-edit"></i> Save Changes</button>
              </div>
            </form>
          </div>
        </div>

      </div>

      <checker-toaster title="Update Status" msg-success="Scanner has been updated" msg-failed="Data gagal"/>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
@endsection