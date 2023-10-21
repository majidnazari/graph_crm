@extends('layouts.index')
@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>کاربران</h1>
            </div>
            <div class="col-sm-6">
              <!--
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">DataTables</li>
              </ol>
              -->
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                    <a class="btn btn-success" href="{{ route('user_all_create') }}">کاربر جدید</a>
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <h3 class="text-center">
                    فیلتر
                 </h3>
                 <form method="post">
                     @csrf
                     <div class="row">
                         <div class="col">
                             <div class="form-group">
                                 <label for="name">نام و نام خانوادگی</label>
                                 <input type="text" class="form-control" id="name" name="name" placeholder="نام و نام خانوادگی" value="{{ isset($name) ? $name : '' }}" />
                             </div>
                         </div>
                         <div class="col" style="padding-top: 32px;">
                            <a class="btn btn-success" onclick="theSearch()" href="#">
                                جستجو
                            </a>
                            <img id="loading" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
                        </div>
                     </div>
                 </form>
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>نام کاربری</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>گروه</th>
                    <th>#</th>
                  </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->
@endsection

@section('js')
<!-- DataTables -->
<!-- <script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script> -->

<script src="../../plugins/DataTables/DataTables-1.13.4/js/jquery.dataTables.js"></script>  
<script src="../../plugins/DataTables/DataTables-1.13.4/js/dataTables.bootstrap4.js"></script>  


<!-- page script -->
<script>
        var table;
        function theSearch(){
            $('#loading').css('display','inline');
            table.ajax.reload();
            return false;
        }
        function destroy(e){
            if(!confirm('آیا مطمئنید؟')){
                e.preventDefault();
              }
        }
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


          table = $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "paginate": {
                    "previous": "قبل",
                    "next": "بعد"
                },
                "emptyTable":     "داده ای برای نمایش وجود ندارد",
                "info":           "نمایش _START_ تا _END_ از _TOTAL_ داده",
                "infoEmpty":      "نمایش 0 تا 0 از 0 داده",
            },
            "columnDefs": [   ////define column 1 and 6
                    {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    },
                    {
                        "searchable": false,
                        "orderable": false,
                        "targets": 6
                    },
            ],
            "order": [[1, 'asc']], /// sort columns 2
            processing: true,
            serverSide: true,
            ajax: {
                "type": "POST",
                "url": "{{ route($route) }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                    data['name'] = $("#name").val();
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                    $('#example2_paginate').removeClass('dataTables_paginate');
                    $('#loading').css('display','none');
                }

            },
            columns: [
                { data: null},
                { data: 'id' },
                { data: 'email' },
                { data: 'first_name' },
                { data: 'last_name' },
                { data: 'groups_id' },
                { data: 'end'}

             ]
          });
          table.on('draw.dt', function () {
            var info = table.page.info();
            table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + info.start;
            });
        });

          $('#name').on('keypress',function(e) {
            if(e.which == 13) {
               $('#loading').css('display','inline');
               table.ajax.reload();
               return false;
            }
         });
        });

  </script>

@endsection
