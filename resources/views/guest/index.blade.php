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
              <div class="container-fluid">
                <div class="row">
                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                      <div class="inner">
                        <h3>
                          <span id="guests_count"></span> / 
                          <span id="guests_all"></span>
                        </h3>
                        <p># of Guests</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-bag"></i>
                      </div>
                    </div>
                  </div>

                  <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                      <div class="inner">
                        <h3>
                          <span id="attendances_count"></span> / 
                          <span id="attendances_all"></span>
                        </h3>
                        <p># of Occupied Seat</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-bag"></i>
                      </div>
                    </div>
                  </div>


                  <div class="col-lg-6 col-6">
                    <div class="small-box bg-danger">
                      <div class="inner">
                        <h3>
                          <span id="name"></span> @ 
                          <span id="rsvp_count"></span> pax - 
                          <span id="seat"></span>
                        </h3>
                        <p>Last Check-in Guest</p>
                      </div>
                      <div class="icon">
                        <i class="ion ion-bag"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

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
  <checker-toaster title="Update Status" msg-success="Guest has been updated" msg-failed="Failed"/>
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
            {data: 'id', name: 'guests.id'},
            {data: 'name', name: 'guests.name'},
            {data: 'rsvp_count', name: 'guests.rsvp_count'},
            {data: 'seat', name: 'guests.seat'},
            {
              data: 'attended_at', 
              name: 'guests.attended_at',
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
            {data: 'id', name: 'action', "orderable": "false"},
            {data: 'qr_code', name: 'qr_code'},
        ],
        order: [
            [4, "desc"]
        ],
        columnDefs: [
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
                    if (row['attended_at'] === null) {
                      html += `
                        <a href='/guests/${data}/toggle/true')' data-toggle='tooltip' data-placement='top' title='Mark Attended'>
                        <i class='fa fa-plus fa-action'></i></a>
                      `
                    }
                    else {
                      html += `
                        <a href='/guests/${data}/toggle/false')' data-toggle='tooltip' data-placement='top' title='Mark Unattended'>
                        <i class='fa fa-minus fa-action'></i></a>
                      `
                    }
                     
                    return html;
                },
                "targets": 5,
                "searchable": false
            },
            { "visible": false,  "targets": [ 6 ], "searchable": false }
        ]
    });

    table.on( 'draw', function (data) {
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

    var last_data = null;

    function checkGuest() {
      $.ajax({
                url: "{{env('APP_URL')}}/guests/check",
            }).done(function(data) {
                data = JSON.parse(data);
                $('#guests_count').html(data.guests_count);
                $('#guests_all').html(data.guests_all);
                $('#attendances_count').html(data.attendances_count);
                $('#attendances_all').html(data.attendances_all);

                data.name = data.name.split(" ")[0];

                $('#name').html(data.name);
                $('#rsvp_count').html(data.rsvp_count);
                $('#seat').html(data.seat);

                if (last_data != null && last_data.name != data.name) {
                  table.ajax.reload();
                }
                last_data = data;
            });
      setTimeout(function () {
          checkGuest();
      }, 1500);
    }
    checkGuest();
    
  });
</script>
@endsection