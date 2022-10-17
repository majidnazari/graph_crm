@extends('layouts.index')

@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  .morepanel {
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
        <h1>سند </h1>
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

  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>

  <form method="post">
    @csrf
    <div class="row">
      <div class="col-3">
        <label for="supporter_id">پشتیبان</label>
        <select id="supporter_id" name="supporter_id" class="form-control select2" onchange="theChange()">
          <option value="0">همه</option>
          @foreach ($supporters as $item)
          @if(isset($supporter_id) && $supporter_id==$item->id)
          <option value="{{ $item->id }}" selected>
            @else
          <option value="{{ $item->id }}">
            @endif
            {{ $item->first_name }} {{ $item->last_name }}
          </option>
          @endforeach
        </select>

      </div>

      <div class="col-3">
        <label for="student_id">دانش آموز</label>
        <select id="student_id" name="student_id" class="form-control select2" onchange="theChange()">
          <option value="0">همه</option>
    
          @if(isset($student_id) && $student_id==$item->id)
          <option value="{{ $item->id }}" selected>
            @else
          <option value="{{ $item->id }}">
            @endif
            {{ $item->first_name }} {{ $item->last_name }}
          </option>
          
        </select>

      </div>

      <div class="col-1">
        <label for="month">ماه</label>
        <select id="month" name="month" class="form-control " onchange="theChange()">
          <option value="0">همه</option>
          @foreach ($sanad_month as $item)
          @if(isset($sanad_month) && $sanad_month==$item)
          <option value="{{ $item }}" selected>
            @else
          <option value="{{ $item }}">
            @endif
            {{ $item  }}
          </option>
          @endforeach
        </select>
      </div>
      <div class="col-1">
        <label for="year">سال</label>
        <select id="year" name="year" class="form-control " onchange="theChange()">
          <option value="0">همه</option>
          @foreach ($sanad_year as $item)
          @if(isset($sanad_year) && $sanad_year==$item)
          <option value="{{ $item }}" selected>
            @else
          <option value="{{ $item }}">
            @endif
            {{ $item }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="col">
        <div class="form-group">
          <label for="receipt_date_ser"> تاریخ دریافت پول </label>

          @if (isset($sanad) && isset($sanad->receipt_date))
          <input type="text" class="form-control pdate" id="receipt_date_ser" name="receipt_date_ser" value="{{ jdate($sanad->receipt_date)->format('Y/m/d') }}" />
          @else
          <input type="text" id="receipt_date_ser" name="receipt_date_ser" class="form-control pdate" value="" />
          @endif

        </div>
      </div>

      <div class="col">
        <div class="form-group">
          <label for="to_date">&nbsp;</label>
          <a href="#" class="btn btn-success form-control" onclick="theSearch()">جستجو</a>
          <img id="loading" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
        </div>
      </div>
      <!-- <div class="col">
        <div class="form-group">
          <label for="to_date">&nbsp;</label>
          <a href="#" class="btn btn-success form-control" onclick="theSearchExcel()">خروجی اکسل</a>
          <img id="loadingExcel" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
        </div>
      </div> -->

    </div>
  </form>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">

          <h3 class="card-title">
            <a class="btn btn-success" href="{{ route('sanad_create') }}">سند جدید</a>
          </h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <table id="sanadtbl" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>ردیف</th>
                <th>پشتیبان</th>
                <th>شماره سند </th>
                <th> تاریخ سند </th>
                <th> تاریخ دریافت وجه </th>
                <th>شرح</th>
                <th>نام دانش آموز</th>

                <!-- <th>بستانکار</th> -->
                <!-- <th>مانده</th> -->
                <!-- <th>کد</th> -->
                <th>قیمت کل</th>
                <th>قیمت دریافتی</th>


                <th>سهم پشتیبان</th>
                <th>پرداختی موسسه</th>
                <!-- <th>نوع</th> -->
                <th>ویرایش</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($sanads as $index => $item)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->supporter->first_name. ' ' . $item->supporter->last_name }}</td>
                <td>{{ $item->number}} </td>
                <td>{{ jdate($item->updated_at)->format("Y/m/d") }}</td>
                <td>{{ jdate($item->receipt_date)->format("Y/m/d") }}</td>
                <td>{{ $item->description }}</td>
                
                <!-- <td>{{ $item->id }}</td> -->

                <!-- <td>{{ $item->type > 0 ? number_format($item->total) : '' }}</td>  -->
                <!-- <td>{{ $item->supporter->first_name. ' ' . $item->supporter->last_name }}</td> -->
                <td>{{ number_format($item->total_cost) }}</td>
                <td>{{ $item->type > 0 ?   number_format($item->total) : 0 }}</td>

                <td>{{ $item->type > 0 ?  number_format(ceil($item->total * $item->supporter_percent / 100)) : ""}}</td>
                <td>{{ $item->type < 0 ? number_format($item->total) : ''}}</td>
                <!-- <td>{{ $item->type && $item->type < 0 ? 'بدهکار' : 'بستانکار' }}</td> -->
                <td> <a class="btn btn-info" href="{{ route('sanad_edit',$item->id) }}"> ویرایش </a> </td>
                <!-- <td>{{ $item->name }}</td>
                        <td></td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('school_edit', $item->id) }}">
                                ویرایش
                            </a>
                            <a class="btn btn-danger" href="{{ route('school_delete', $item->id) }}">
                                حذف
                            </a>
                        </td> -->
              </tr>
              @endforeach

            </tbody>
            <tr>
              <td colspan='8'>
                جمع کل:
              </td>
              <!-- <td colspan='1'> {{number_format($sanads->sum('total_creditor'))}} </td> -->
              <td colspan='1' id='total_get_price'> {{number_format($sanads->sum('total_price'))}} </td>

              <td colspan='1' id='total_supporter'> {{number_format($sanads->sum('total_supporter'))}} </td>
              <td colspan='1' id='total_give_price'> {{number_format($sanads->sum('total_debtor'))}} </td>
              <td colspan='1' id='total_remain'> {{number_format($sanads->sum('total_supporter')-$sanads->sum('total_debtor'))}} </td>


              <!--<td colspan='2'> {{number_format($sanads->sum('total_total_cost'))}} </td> -->

            </tr>
          </table>
          <form method="post">
            @csrf
            <div class="row">
              <div class="col-3">
                <label for="supporter_amount_edit">ویرایش سهم پشتیبان</label>
                <input type="text" id="supporter_amount_edit" name="supporter_amount_edit" class="form-control select2" value="">
              </div>

              <!-- <div class="col-3">
                                       
                                          <input type="text"  id="year_id_edit" name="year_id_edit" class="form-control select2" value="{{$sanad_from}}">
                                      </div> 
                                      <div class="col-3">
                                       
                                          <input type="text"  id="month_id_edit" name="month_id_edit" class="form-control select2" value="{{$sanad_to}}">
                                      </div>    -->

              <div class="col-3">
                <div class="form-group">
                  <label for="to_date">&nbsp;</label>
                  <a href="#" class="btn btn-success form-control" onclick="theEditAll()">اعمال تغییرات</a>
                  
                </div>
              </div>
              <div>
              <span style="color:red"> ***
                   ابتدا نام پشتیبان و ماه و سال انتخاب شود سپس در باکس سهم پشتیبان درصدی که می خواهید برای کلیه موارد سرچ شده اعمال شود برای پشتیبان را وارد نمایید.
                   </span>
              </div>
            </div>
          </form>
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
<script src="/plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- page script -->
<script>
  let table = "";

  function theEditAll() {
    let supporter_id = 0;
    let month = 0;
    let year = 0;
    let supporter_amount_edit = 0;

    supporter_id = $("#supporter_id").val() > 0 ? $("#supporter_id").val() : 0;
    month = $("#month").val() > 0 ? $("#month").val() : 0;
    year = $("#year").val() > 0 ? $("#year").val() : 0;
    if (supporter_id == 0 || month == 0 || year == 0) {

      alert("لطفا پشتیبان و ماه و سال را وارد نمایید.");
      return false;
    } else {
      if ($("#supporter_amount_edit").val().trim() != "") {
        supporter_amount_edit = $("#supporter_amount_edit").val();

      } else {
        alert("لطفا مقدار سهم پشتیبان را وارد نمایید.");
        return false;
      }
    }


    var data = {
      "supporter_id": supporter_id,
      "month": month,
      "year": year,
      "supporter_amount_edit": supporter_amount_edit
    };

    $.post("{{ route('editAllSupporter') }}", data, function(res) {
      // console.log("the res is:"+ res);
      if (res) {
        alert("ویرایش با موفقیت انجام شد.");
        location.reload();
      }
    });

    return false;
  }

  function theSearch() {

    // $.post("{{ route('searchIndex') }}",{flag:1,year:$("#year").val(),month:$("#month").val()},function(res){
    //   console.log("the res is:"+ res);
    // });
    // alert($("#name").val());
    //$(myself).prop('disabled', true);
    $('#loading').css('display', 'inline');
    table.ajax.reload();
    return false;
  }

  function theChange() {
    // $(myself).prop('disabled',true);
    $('#loading').css('display', 'inline');
    table.ajax.reload();
    return false;
  }
  $(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $("#supporter_id").select2();
    $('#student_id').select2({
      ajax: {
        url: "{{ route('searchStudentForSanad') }}",
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            q: params.term, // search term
            //page: params.page
          };
        },
        processResults: function(data) {
          //console.log("data" , data);
          return {
            results: data
          };
        },
        cache: true
      },
      placeholder: 'حداقل باید ۳ کاراکتر وارد کنید',
      minimumInputLength: 3,

    });

    table = $('#sanadtbl').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      language: {
        paginate: {
          previous: 'قبل',
          next: 'بعد'
        },
        emptyTable: 'داده ای برای نمایش وجود ندارد',
        info: 'نمایش _START_ تا _END_ از _TOTAL_ داده',
        infoEmpty: 'نمایش 0 تا 0 از 0 داده',
        proccessing: 'در حال بروزرسانی'
      },
      "columnDefs": [ ////define column 1 and 5
        {
          "searchable": false,
          "orderable": false,
          "targets": [0, 9]
        },
        //{ "type": "pstring", "targets": [2,3,4] }
      ],

      "order": [
        [1, 'asc']
      ], /// sort columns 2
      serverSide: true,
      processing: true,
      ajax: {
        "type": "POST",
        "url": "{{ route('searchIndex') }}",
        "dataType": "json",
        "contentType": 'application/json; charset=utf-8',

        "data": function(data) {
          //alert($("#receipt_date_ser").val());
          // data['name'] = "fFF";//$("#name").val();
          data['supporter_id'] = $("#supporter_id").val();
          data['student_id'] = $("#student_id").val();
          data['receipt_date_ser'] = $("#receipt_date_ser").val();
          data['month'] = $("#month").val();
          data['year'] = $("#year").val();

          //data['sanad_year'] = $('#sanad_year').val();
          return JSON.stringify(data);
        },
        "complete": function(response) {
          let total_remain = response.responseJSON.total_give_price - response.responseJSON.total_supporter;
          $('#loading').css('display', 'none');
          $("#total_get_price").html(Number(response.responseJSON.total_get_price).toLocaleString());
          $("#total_give_price").html(Number(response.responseJSON.total_give_price).toLocaleString());
          $("#total_supporter").html(Number(response.responseJSON.total_supporter).toLocaleString());
          $("#total_remain").html(Number(total_remain).toLocaleString());

          //$('#theBtn').prop('disabled',false);
          //var obj = JSON.parse( response );
          //console.log("result is: " + JSON.stringify(response));
          //console.log("total_r1 is: " + response.responseJSON.total_r1);
        }
      },
      columns: [{
          data: 'row'
        },
        {
          data: 'supporter'
        },
        {
          data: 'number'
        },
        {
          data: 'updated_at'
        },
        {
          data: 'receipt_date'
        },
        {
          data: 'description'
        },
        {
          data: 'student'
        },
        {
          data: 'total_cost'
        },
        {
          data: 'total_get'
        },
        {
          data: 'supporter_percent'
        },
        {
          data: 'total_give'
        },
        {
          data: 'end'
        },
        // { data: 'supporter_id' }
        // { data: 'number' },
        // { data: 'description' },
        // { data: 'updated_at' },
        // { data : 'total_cost'},
        // { data : 'total'},
        // { data : 'supporter_percent'},
      ],
    });



    $(".btn-danger").click(function(e) {
      if (!confirm('آیا مطمئنید؟')) {
        e.preventDefault();
      }
    });
  });
</script>
@endsection