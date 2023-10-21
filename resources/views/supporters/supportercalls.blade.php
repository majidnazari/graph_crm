@php
// dd($request);
$persons = [
    "student"=>"دانش آموز",
    "father"=>"پدر",
    "mother"=>"مادر",
    "other"=>"غیره"
];
@endphp
@extends('layouts.index')

@section('css')
<style>
    .students, .studenttags{
        display: none;
    }
</style>
<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>
                  تماس های
                  توسط
                 {{ $supporter->first_name }}
                 {{ $supporter->last_name }}
              </h1>
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
                                 <label for="fullName">نام و نام خانوادگی دانش آموز</label>
                                 <input type="text" id="fullName" name="fullName" class="form-control" onkeypress="handle(event)" >
                             </div>
                         </div>
                         <div class="col" style="padding-top: 32px;">
                             <a class="btn btn-success" onclick="table.ajax.reload(); return false;" href="#">
                                 جستجو
                             </a>
                         </div>
                     </div>
                 </form>
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>دانش آموز</th>
                    <th>محصول</th>
                    <th>اطلاع رسانی</th>
                    <th>پاسخگو</th>
                    <th>نتیجه</th>
                    <th>یادآور</th>
                    <th>پاسخگو بعد</th>
                    <th>تاریخ تماس</th>
                    <th>توضیحات</th>
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
    function destroy(event){
        if(!confirm('آیا مطمئنید؟')){
            event.preventDefault();
          }
    }

    function showStudents(index){
        $("#students-" + index).toggle();

        return false;
    }
    function showStudentTags(index){
        $("#studenttags-" + index).toggle();

        return false;
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
        columnDefs: [ { orderable: false, targets: [0,11] }],
        "order": [[1, 'asc']], /// sort columns 1
        serverSide: true,
        processing: true,
        ajax: {
            "type": "POST",
            "url": "{{ route($route,$id) }}",
            "dataType": "json",
            "contentType": 'application/json; charset=utf-8',
            "data": function (data) {
                data['from_date'] = "{{ $from_date}}";
                data['to_date'] = "{{ $to_date }}";
                data['products_id'] = "{{ $products_id}}";
                data['notices_id'] = "{{ $notices_id }}";
                data['replier_id'] = "{{ $replier_id}}";
                data['sources_id'] = "{{ $sources_id}}";
                data['id'] = "{{ $id}}";
                data['fullName'] = $('#fullName').val();
                return JSON.stringify(data);
            },
            "complete": function(response) {
            }
        },
        columns: [
            { data: 'row'},
            { data: 'id' },
            { data: 'students_id' },
            { data: 'products_id' },
            { data: 'notices_id' },
            { data: 'replier' },
            { data: 'call_results_id' },
            { data: 'next_call' },
            { data: 'next_to_call' },
            { data: 'created_at' },
            { data: 'description' },
            { data: 'end' },
        ],
    });
});
function handle(e){
    if(e.keyCode === 13){
        e.preventDefault(); // Ensure it is only this code that runs
        table.ajax.reload();
        return false;
    }
}
  </script>
@endsection
