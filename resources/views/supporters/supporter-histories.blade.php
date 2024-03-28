@extends('layouts.index')

@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>تاریخچه پشتیبان های دانش آموزان</h1>
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
                                <label for="supporters_id">پشتیبان</label>
                                <select  id="supporters_id" name="supporters_id" class="form-control select2" onchange="theChange()">
                                    <option value="">همه</option>
                                    @foreach ($supports as $item)
                                        @if(isset($supporters_id) && $supporters_id==$item->id)
                                        <option value="{{ $item->id }}" selected >
                                        @else
                                        <option value="{{ $item->id }}" >
                                        @endif
                                        {{ $item->first_name }} {{ $item->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="name">نام و نام خانوادگی</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="نام و نام خانوادگی دانش آموز" value="{{ isset($name)?$name:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="name">تلفن</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="تلفن دانش آموز" value="{{ isset($phone)?$phone:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="user_name">نام و نام خانوادگی کاربر ایجادکننده</label>
                                <input type="text" class="form-control" id="user_name" name="user_name" placeholder="نام و نام خانوادگی  کاربر ایجاد کننده" value="{{ isset($user_name)?$user_name:'' }}" onkeypress="handle(event)" autocomplete="off"/>
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
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>تلفن</th>
                    <th>کاربر ثبت کننده</th>
                    <th>پشتیبان</th>
                    <th>تاریخ ایجاد</th>
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
<!-- Select2 -->
<script src="/plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<!-- <script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script> -->

<script src="../../plugins/DataTables/DataTables-1.13.4/js/jquery.dataTables.js"></script>  
<script src="../../plugins/DataTables/DataTables-1.13.4/js/dataTables.bootstrap4.js"></script>  

<!-- page script -->
<script type="text/javascript">
    $('select.select2').select2();
    function theSearch(){
        $('#loading').css('display','inline');
        table.ajax.reload();
        return false;
    }
    function theChange(){
        $('#loading').css('display','inline');
        table.ajax.reload();
        return false;
    }
    function handle(e){
        if(e.keyCode === 13){
            $('#loading').css('display','inline');
            e.preventDefault(); // Ensure it is only this code that runs
            table.ajax.reload();
            return false;
        }
    }
</script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".btn-danger").click(function(e){
            if(!confirm('آیا مطمئنید؟')){
                e.preventDefault();
            }
        });
        $('select.select2').select2();

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
                "proccessing": "در حال بروزرسانی"
            },
            columnDefs: [ { orderable: false, targets: 0 } ],
            "order": [[2, 'asc']], /// sort columns 2 default sort by students_id
            serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('student_supporter_histories_post') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                    data['supporters_id'] = $("#supporters_id").val();
                    data['name'] = $("#name").val();
                    data['phone'] = $("#phone").val();
                    data['user_name'] = $("#user_name").val();
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                    $('#loading').css('display','none');
                }

            },
            columns: [
                { data: 'row'},
                { data: 'id' },
                { data: 'first_name' },
                { data: 'last_name' },
                { data: 'phone' },
                { data: 'users_id'},
                { data: 'supporters_id'},
                { data: 'created_at'}
            ],

        });


    });
</script>
@endsection
