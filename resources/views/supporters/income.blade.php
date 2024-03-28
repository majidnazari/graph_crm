@extends('layouts.index')

@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="/dist/css/custom.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .morepanel{
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
              <h1>درآمد پشتیبان</h1>
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
                                <label for="from_date">از تاریخ</label>
                                <input type="text" id="from_date" name="from_date" class="form-control pdate" value="{{ ($from_date)?jdate($from_date)->format("Y/m/d"):jdate()->format("Y/m/d") }}" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="to_date">تا تاریخ</label>
                                <input type="text" id="to_date" name="to_date" class="form-control pdate" value="{{ ($to_date)?jdate($to_date)->format("Y/m/d"):jdate()->format("Y/m/d") }}" />
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
                    <th>نام دانش‌ آموز</th>
                    <th>تاریخ</th>
                    <th>محصول</th>
                    <th>قیمت کل(تومان)</th>
                    <th>کارمزد(درصد)</th>
                    <th>سهم پشتیبان(تومان)</th>
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center alert alert-success" id="price" style="display:none"></div>
                </div>
            </div>
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
<script>
    var table = "";
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        table = $("#example2").DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "language": {
                "paginate": {
                    "previous": "قبل",
                    "next": "بعد"
                },
                "emptyTable": "داده ای برای نمایش وجود ندارد",
                "info": "نمایش _START_ تا _END_ از _TOTAL_ داده",
                "infoEmpty": "نمایش 0 تا 0 از 0 داده",
            },
            columnDefs: [ { orderable: false, targets: [0,6,7] } ],
            "order": [[1, 'asc']], /// sort columns 1
            serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('supporter_student_income_post') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                    data["from_date"] = $('#from_date').val();
                    data["to_date"] = $('#to_date').val();
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                    var sum = JSON.parse(response.responseText).sum;
                    if(sum != 0){
                        $('#price').css('display','block');
                        $('#price').text(' جمع کل '+ sum + ' تومان ');
                    }else{
                        $('#price').css('display','none');
                    }
                }

            },
            columns: [
                { data: 'row'},
                { data: 'id' },
                { data: 'students_id' },
                { data: 'created_at' },
                { data: 'products_id' },
                { data: 'price'},
                { data: 'wage'},
                { data: 'portion'}
            ],
        });


    });
</script>


@endsection
