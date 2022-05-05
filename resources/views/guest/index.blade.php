@extends('layouts.admin')

@section('content-wrapper')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Guest List</h1>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered yajra-datatable">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nama</th>
                      <th>Jlh. Tamu</th>
                      <th>Seat</th>
                      <th>Datang</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
              {{-- <a href="{{ route('dashboard.export') }}" class="btn btn-sm btn-info float-left">Export to Excel</a> --}}
              <a href="{{ route('guest.create') }}" class="btn btn-sm btn-primary float-left mr-2">Add Guest</a>
              <a href="" class="btn btn-sm btn-outline-primary float-left">Export to Excel</a>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
@endsection

@section('js')
<script>
  $(function () {
    
    var table = $('.yajra-datatable').DataTable({
        data: [
          [1, "Hagai", 2, "B16", ""],
          [2, "Manael", 2, "B17", ""],
        ],
        // processing: true,
        // serverSide: true,
        // ajax: "{{ route('guest.list') }}",
        // columns: [
        //     {data: 'id', name: 'id'},
        //     {data: 'name', name: 'name'},
        //     {data: 'rsvp_count', name: 'rsvp_count'},
        //     {data: 'seat', name: 'seat'},
        //     {data: 'attended_at', name: 'attended_at'},
        // ]
    });
    
  });
</script>
@endsection