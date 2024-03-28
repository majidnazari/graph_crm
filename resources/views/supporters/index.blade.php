@extends('layouts.index')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .students, .studenttags{
        display: none;
    }
</style>
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>پشتیبان ها</h1>
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
                    <a class="btn btn-success" href="{{ route('user_supporter_create') }}">پشتیبان جدید</a>
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>دانش آموزان</th>
                    <th>محل کار</th>
                    <th>فروش</th>
                    <th>برچسب ها</th>
                    <th>نام کاربری</th>
                    <th>رمز عبور</th>
                    <th>پیام</th>
                  </tr>
                  </thead>
                  <tbody>
                      @foreach ($supporters as $index => $item)
                      @php
                        $purchaseCount = 0;
                        $tags = [];
                      @endphp
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->first_name }}</td>
                        <td>{{ $item->last_name }}</td>
                        @if($item->students && count($item->students)>0)
                        <td>
                            <a target="_blank" href="{{ route('supporter_allstudents', ['id' => $item->id, 'level' => "all"]) }}">{{ count($item->students) }}</a>
                            @php $purchaseCount = 0; @endphp
                            @foreach ($item->students as $sitem)
                                @php
                                    $purchaseCount += count($sitem->purchases);
                                @endphp
                            @endforeach
                        </td>
                        @else
                        <td>0</td>
                        @endif
                        <td>{{ $item->work_address }}</td>
                        <td>{{ $purchaseCount }}</td>
                        @if($item->students && count($item->students)>0)
                        <td>
                            <a onclick="return showStudentTags({{ $index }});" href="#">برچسب ها</a>
                            <div class="studenttags" id="studenttags-{{ $index }}">
                                @foreach ($item->students as $student)
                                @foreach ($student->studenttags as $sitem)
                                @if(!isset($tags[$sitem->tags_id]))
                                <span >
                                    {{ $sitem->tag->name }}
                                </span><br/>
                                @php
                                    $tags[] = $sitem->tags_id;
                                @endphp
                                @endif
                                @endforeach
                                @endforeach
                            </div>
                        </td>
                        @else
                        <td></td>
                        @endif
                        <td>{{ $item->email }}</td>
                        <td>
                            {{ $item->pass }}
                            <a href="#" onclick="return changePass({{ $item->id }});">
                                <i class="fa fa-edit" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td>
                            <a target="_blank" href="{{ route('message_user', $item->id) }}">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                            </a>
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
<!-- <script src="../../plugins/datatables/jquery.dataTables.js"></script>  -->
<!-- <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script> -->

<script src="../../plugins/DataTables/DataTables-1.13.4/js/jquery.dataTables.js"></script>  
<script src="../../plugins/DataTables/DataTables-1.13.4/js/dataTables.bootstrap4.js"></script>  

<!-- <script src="../../plugins/DataTables/DataTables-1.13.4/css/dataTables.bootstrap4.css"></script> -->
<!-- page script -->
<script>
    function showStudents(index){
        // $(".students").hide();
        $("#students-" + index).toggle();

        return false;
    }
    function showStudentTags(index){
        // $(".students").hide();
        $("#studenttags-" + index).toggle();

        return false;
    }
    function changePass(user_id) {
        // alert(user_id);
        var password = prompt('لطفا رمز عبور جدید را وارد کنید:');
        if(!confirm(`آیا رمز عبور به ${password} تغییر یابد؟`)){
            return false;
        }

        $.post('{{ route("user_supporter_changepass") }}', {
            user_id,
            password,
            _token: "{{ csrf_token() }}"
        }, function(res){
            console.log(res);
            if(res.error==null)
                window.location.reload();
        });
        return false;
    }
    $(function () {
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
        columnDefs: [ { orderable: false, targets: [0,9] },  { "type": "pstring", "targets": [2,3,5] } ],
        "order": [[1, 'asc']], /// sort columns 1
      });

      $(".btn-danger").click(function(e){
          if(!confirm('آیا مطمئنید؟')){
            e.preventDefault();
          }
      });
    });
  </script>
@endsection
