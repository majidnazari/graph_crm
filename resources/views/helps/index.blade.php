@extends('layouts.index')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>آموز و راهنما</h1>
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
                    @if(Gate::allows('parameters'))
                    <a class="btn btn-success" href="{{ route('help_create') }}">آموزش جدید</a>
                    @endif
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>عنوان</th>
                    <th>فایل</th>
                    <th>زمان</th>
                    @if(Gate::allows('parameters'))
                    <th>#</th>
                    @endif
                  </tr>
                  </thead>
                  <tbody>
                      @foreach ($helps as $index => $item)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->link }}</td>
                        <td>{{ jdate($item->created_at)->format("Y/m/d") }}</td>
                        @if(Gate::allows('parameters'))
                        <td>
                            <a class="btn btn-primary" href="{{ route('help_edit', $item->id) }}">
                                ویرایش
                            </a>
                            <a class="btn btn-danger" href="{{ route('help_delete', $item->id) }}">
                                حذف
                            </a>
                        </td>
                        @endif
                      </tr>
                      @endforeach
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
    $(function () {
    $(".btn-danger").click(function(e){
        if(!confirm('آیا مطمئنید؟')){
          e.preventDefault();
        }
    });
      $('#example2').DataTable({
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
        "columnDefs": [   ////define column 1 and 5
        {
            "searchable": false,
            "orderable": false,
            "targets": [0,5]
        },
        { "type": "pstring", "targets": 2 },
        ],
        "order": [[1, 'asc']], /// sort columns 2
      });


    });
  </script>
@endsection
