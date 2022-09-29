@extends('layouts.index')

@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
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
              <h1> گزارش یاد آورها </h1> 
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
     
      
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                
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
                                <label for="first_name">نام </label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder=" نام " value="{{ isset($first_name)?$first_name:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="last_name"> نام خانوادگی</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder=" نام خانوادگی" value="{{ isset($last_name)?$last_name:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="phone">تلفن</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="تلفن"  value="{{ isset($phone)?$phone:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="products_id">محصول</label>
                                <select  id="products_id" name="products_id" class="form-control select2" onchange="theChange()">
                                    <option value="">همه</option>
                                    @foreach ($products as $item)
                                        @if(isset($products_id) && $products_id==$item->id)
                                        <option value="{{ $item->id }}" selected >
                                        @else
                                        <option value="{{ $item->id }}" >
                                        {{ ($item->collection && $item->collection->parent) ? $item->collection->parent->name : ''}}
                                        {{ ($item->collection && $item->collection->parent) ? '->' : ''}}
                                        {{ ($item->collection) ? $item->collection->name : ''}}
                                        {{ ($item->collection) ? '->' : ''}}
                                        {{ $item->name }}
                                        @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if(Gate::allows('purchases'))
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
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="from_date">از تاریخ</label>
                                <input type="text" class="form-control" id="from_date_persian" placeholder="از تاریخ"  value="{{ isset($from_date)?$from_date:'' }}" readonly />
                                <input type="hidden" id="from_date" name="from_date" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="to_date">تا تاریخ</label>
                                <input type="text" class="form-control" id="to_date_persian" placeholder="تا تاریخ"  value="{{ isset($to_date)?$to_date:'' }}" readonly />
                                <input type="hidden" id="to_date" name="to_date" />
                            </div>
                        </div>
                        @endif
                        <div class="col" style="padding-top: 32px;">
                            <a class="btn btn-success" onclick="theSearch()" href="#">
                                جستجو
                            </a>
                            <img id="loading" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
                        </div>
                    </div>
                </form>

                <table id="remindertbl" class="table table-bordered table-hover"  >
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th> دانش آموز </th>
                    <th> محصول  </th>
                    <th> تلفن  </th>
                    <th>پشتیبان</th>
                    
                    <th> تاریخ  یادآور </th>
                    <th>تاریخ یادآور بعدی</th>
                    <th> توضیحات </th>                   
                  </tr>
                  </thead>
                  <tbody>
                      @foreach ($reminders as $index => $item)                      
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->student->first_name. '  ' .$item->student->last_name }}</td>
                        <td>{{ $item->product }}</td>
                        <td>{{ $item->student->phone }}</td>
                        <td>{{ $item->student->supporter->first_name. '  ' .$item->student->supporter->last_name }}</td>
                        <td>{{ jdate($item->created_at)->format("Y/m/d") }}</td>
                        <td>{{ jdate($item->next_call)->format("Y/m/d") }}</td>
                        <td>{{ $item->description }}</td>
                        
                       
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
<script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- page script -->
<script>
 let table = "";

   
    function theSearch(){
     
      // $.post("{{ route('searchIndex') }}",{flag:1,year:$("#year").val(),month:$("#month").val()},function(res){
      //   console.log("the res is:"+ res);
      // });
     // alert($("#name").val());
       // $(myself).prop('disabled',true);
         $('#loading').css('display','inline');
         table.ajax.reload();
        return false;
    }
    function theChange(){     
        // $(myself).prop('disabled',true);
         $('#loading').css('display','inline');        
         table.ajax.reload();
        return false;
    }
    $('#from_date_persian').MdPersianDateTimePicker({
            targetTextSelector: '#from_date_persian',
            targetDateSelector: '#from_date',
        });
        $('#to_date_persian').MdPersianDateTimePicker({
            targetTextSelector: '#to_date_persian',
            targetDateSelector: '#to_date',
        });
    $(function () { 
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      table = $('#remindertbl').DataTable({
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
        "columnDefs": [   ////define column 1 and 5
        {
            "searchable": false,
            "orderable": false,
            "targets": [0,7]
        },
        //{ "type": "pstring", "targets": [2,3,4] }
        ],

        "order": [[3, 'desc']], /// sort columns 2
        serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('supporter_students_reminders_get_with_AJAX') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                    data['first_name'] = $("#first_name").val();
                    data['last_name'] = $("#last_name").val();
                    data['supporters_id'] = $("#supporters_id").val();
                    data['phone'] = $("#phone").val();
                    data['products_id'] = $("#products_id").val();
                    data['from_date_persian'] = $("#from_date_persian").val();
                    data['to_date_persian'] = $("#to_date_persian").val();
                   // data['month'] = $("#month").val();
                   // data['year'] = $("#year").val();
                    
                    //data['sanad_year'] = $('#sanad_year').val();
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                  //let total_remain=response.responseJSON.total_give_price - response.responseJSON.total_supporter ;
                    $('#loading').css('display','none');  
                    // $("#total_get_price").html(Number(response.responseJSON.total_get_price).toLocaleString());
                    // $("#total_give_price").html(Number(response.responseJSON.total_give_price).toLocaleString());
                    // $("#total_supporter").html(Number(response.responseJSON.total_supporter).toLocaleString());
                    // $("#total_remain").html(Number(total_remain).toLocaleString());
                    
                    //$('#theBtn').prop('disabled',false);
                     //var obj = JSON.parse( response );
                    //console.log("result is: " + JSON.stringify(response));
                    //console.log("total_r1 is: " + response.responseJSON.total_r1);
                }
            },
            columns: [                
                { data: 'row'},               
                { data: 'student' },                   
                { data: 'product' },                   
                { data: 'phone' },                   
                { data: 'supporter' },
                { data: 'created_at' },
                { data: 'next_call' },
                { data: 'description' } ,             
                //{ data: 'end' } ,             
                // { data: 'student_fullname' },               
                // { data: 'total_cost' },
                // { data: 'total_get' },
                // { data: 'supporter_percent' },
                // { data: 'total_give' },                
                // { data: 'end' },
               // { data: 'supporter_id' }
                // { data: 'number' },
                // { data: 'description' },
                // { data: 'updated_at' },
                // { data : 'total_cost'},
                // { data : 'total'},
                // { data : 'supporter_percent'},
            ],
      });

     

      $(".btn-danger").click(function(e){
          if(!confirm('آیا مطمئنید؟')){
            e.preventDefault();
          } 
      });
    });
    
  </script>
@endsection
