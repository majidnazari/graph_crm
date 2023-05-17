@extends('layouts.index')

@section('css')
<style>
table tr {
    cursor: pointer;
}
</style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>صندوق دریافت</h1>
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
                    @if($user)
                    <a class="btn btn-success" href="{{ route('message_user_create', $user->id) }}">پیام جدید</a>
                    @else
                    <a class="btn btn-success" href="{{ route('message_create') }}">پیام جدید</a>
                    <a class="btn btn-success" href="{{ route('messages_outbox') }}">صندوق ارسال</a>
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
                    <th>ایجاد کننده</th>
                    <th>پیام</th>
                    <th>ضمیمه</th>
                    <!--<th>گیرنده ها</th>-->
                    <th>#</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($messages as $index => $item)
                      <tr>
                        <td href="{{ route('message_message_create', $item->id) }}">{{ $index + 1 }}</td>
                        <td href="{{ route('message_message_create', $item->id) }}">{{ $item->id }}</td>
                        <td href="{{ route('message_message_create', $item->id) }}">{{ ($item->user)?$item->user->first_name . ' ' . $item->user->last_name:'-' }}</td>
                        <td href="{{ route('message_message_create', $item->id) }}">{!! Str::limit(str_replace("\n", "<br/>", $item->message), 100) !!}</td>
                        @if($item->attachment)
                        <td><a target="_blank" href="/uploads/{{ $item->attachment }}">ضمیمه</a></td>
                        @else
                        <td href="{{ route('message_message_create', $item->id) }}"></td>
                        @endif
                        <!--
                        <td>
                            @foreach ($item->flows as $fitem)
                                <span class="alert alert-primary">
                                {{ $fitem->user->first_name }} {{ $fitem->user->last_name }} {{ jdate(strtotime($fitem->created_at))->format("Y/m/d") }}
                                </span>
                            @endforeach
                        </td>
                        -->
                        <td>
                            @if(Gate::allows('parameters'))
                            <a class="btn btn-danger" href="{{ route('message_delete', $item->id) }}">
                                حذف
                            </a>
                            @endif
                        </td>
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
        $('table td').click(function(){
            if($(this).attr('href')){
                window.location = $(this).attr('href');
                return false;
            }
        });
    //   $("#example1").DataTable();
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
            "targets": [0,4,5]
        },
        { "type": "pstring", "targets": [2,3] }
        ],
        "order": [[1, 'asc']], /// sort columns 2
      });

      $(".btn-danger").click(function(e){
          if(!confirm('آیا مطمئنید؟')){
            e.preventDefault();
          }
      });
    });
  </script>
@endsection
