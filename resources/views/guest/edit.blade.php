@extends('layouts.admin')

@section('content-wrapper')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Guest Edit</h1>
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
            <form role="form" method="POST" action="{{ env('APP_URL') }}/guests/{{ $guest->id }}">
              @csrf
              <div class="card-body">
                <div class="form-group row">
                  <label for="name" class="col-md-4 col-form-label text-md-right">Name<span class="text-danger">*</span></label>

                  <div class="col-md-6">
                      <input id="name" 
                              type="text" 
                              class="form-control @error('name') is-invalid @enderror" 
                              name="name" 
                              value="{{ old('name') ?? $guest->name }}" 
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
                      <input id="position" 
                              type="text" 
                              class="form-control @error('position') is-invalid @enderror" 
                              name="position" 
                              value="{{ old('position') ?? $guest->position }}" 
                              autocomplete="position" 
                              placeholder="Position (e.g. Keluarga Pria, Sahabat)"
                              autofocus>

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
                              value="{{ old('rsvp_count') ?? $guest->rsvp_count }}" 
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
                              value="{{ old('seat') ?? $guest->seat }}" 
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
                              value="{{ old('email') ?? $guest->email }}" 
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
                              value="{{ old('phone') ?? $guest->phone }}" 
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
                <button class="btn btn-primary" type="submit"><i class="nav-icon fas fa-edit"></i> Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <checker-toaster title="Update Status" msg-success="Data has been updated" msg-failed="Data gagal"/>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
@endsection