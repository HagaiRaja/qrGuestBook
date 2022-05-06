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
                      <th>Name</th>
                      <th># of Guests</th>
                      <th>Seat</th>
                      <th>Checkin At</th>
                      <th>Actions</th>
                      <th>QR Code</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
              {{-- <a href="{{env('APP_URL')}}/dashboard/export" class="btn btn-info float-left">Export to Excel</a> --}}
              <a href="{{env('APP_URL')}}/guests/create" class="btn btn-primary float-left mr-2">Add Guest</a>
              <a href="" class="btn btn-outline-primary float-left">Export to Excel</a>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->

    <div class="modal fade bd-example-modal-lg" id="qr-modal" tabindex="-1" role="dialog" aria-labelledby="qr-modal" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLongTitle">QR Code for: <span id="qr-title" class="badge badge-primary text-white"></span></h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="over">
            <img src="" id="qr-img" class="img-fluid" alt="Responsive image">
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>
@endsection

@section('js')
<script>
  $(function () {
    
    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{env('APP_URL')}}/guests/list",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'rsvp_count', name: 'rsvp_count'},
            {data: 'seat', name: 'seat'},
            {
              data: 'attended_at', 
              name: 'attended_at',
              render: function(data, type) {
                    if (type === 'display') {
                      if (data === null){
                        return `<h5><span class="badge badge-danger text-white">Unattended</span></h5>`;
                      }
                      else {
                        return `<h5><span class="badge badge-success text-white">${data}</span></h5>`;
                      }
                    }
                     
                    return data;
                }
            },
            {data: 'id', name: 'action'},
            {data: 'qr_code', name: 'qr_code'},
        ],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    html = `
                        <a href='#' data-toggle='tooltip' data-placement='top' title='See QR Code' class='see-qr' 
                            aria-atomic='${row['qr_code']}' aria-busy='${row['name']}'>
                        <i class="fas fa-eye fa-action"></i></a>
                        <a href='/guests/${data}/edit')' data-toggle='tooltip' data-placement='top' title='Edit'>
                        <i class='fa fa-edit fa-action'></i></a>
                        <a href='/guests/${data}/destroy')' data-toggle='tooltip' data-placement='top' title='Delete'>
                        <i class='fa fa-trash-alt fa-action'></i></a>
                        `
                     
                    return html;
                },
                "targets": 5,
                "searchable": false
            },
            { "visible": false,  "targets": [ 6 ], "searchable": false }
        ]
    });

    table.on( 'draw', function () {
        console.log( 'Redraw occurred at: '+new Date().getTime() );
        $('.see-qr').click(function (e) { 
          e.preventDefault();
          let qr_code = $(this).attr('aria-atomic');
          let name = $(this).attr('aria-busy');
          $('#qr-title').html(name);
          $('#qr-img').attr('src', `/temp/${qr_code}.png`);
          $('#qr-modal').modal('toggle');
        });
    } );
    
  });
</script>
@endsection