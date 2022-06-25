@extends('layouts.index')

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
                                  <select  id="supporter_id" name="supporter_id" class="form-control select2" onchange="theChange()">
                                      <option value="0">همه</option>
                                      @foreach ($supporters as $item)
                                          @if(isset($supporter_id) && $supporter_id==$item->id)
                                          <option value="{{ $item->id }}" selected >
                                          @else
                                          <option value="{{ $item->id }}" >
                                          @endif
                                          {{ $item->first_name }} {{ $item->last_name }}
                                          </option>
                                      @endforeach
                                  </select>
                                
                              </div>
                             <div class="col-1">
                                <label for="month">ماه</label>
                                  <select  id="month" name="month" class="form-control select2" onchange="theChange()">
                                  <option value="0">همه</option>
                                       @foreach ($sanad_month as $item)
                                          @if(isset($sanad_month) && $sanad_month==$item)
                                          <option value="{{ $item }}" selected >
                                          @else
                                          <option value="{{ $item }}" >
                                          @endif
                                          {{ $item  }}
                                          </option>
                                      @endforeach 
                                  </select> 
                                </div>
                              <div class="col-1">
                              <label for="year">سال</label>
                                  <select  id="year" name="year" class="form-control select2" onchange="theChange()">
                                  <option value="0">همه</option>
                                      @foreach ($sanad_year as $item)
                                          @if(isset($sanad_year) && $sanad_year==$item)
                                          <option value="{{ $item }}" selected >
                                          @else
                                          <option value="{{ $item }}" >
                                          @endif
                                          {{ $item }} 
                                          </option>
                                      @endforeach 
                                  </select> 
                              </div>
                              <div class="col-3">
                                  <div class="form-group">
                                      <label for="to_date">&nbsp;</label>
                                      <a href="#" class="btn btn-success form-control" onclick="theSearch()" >جستجو</a>
                                      <img id="loading" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
                                  </div>
                               </div> 
                
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
                    <th> تاریخ  سند </th>
                    <th>شرح</th>
                   
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
                        <td>{{ $item->description }}</td>
                        <!-- <td>{{ $item->id }}</td> -->
                       
                        <!-- <td>{{ $item->type > 0 ? number_format($item->total) : '' }}</td>  -->
                        <!-- <td>{{ $item->supporter->first_name. ' ' . $item->supporter->last_name }}</td> -->
                        <td>{{ number_format($item->total_cost) }}</td>
                        <td>{{ $item->type > 0 ?   number_format($item->total) : 0 }}</td>
                      
                        <td>{{ $item->type > 0 ?  number_format(ceil($item->total * $item->supporter_percent / 100)) : ""}}</td>
                        <td>{{ $item->type < 0 ? number_format($item->total) : ''}}</td> 
                        <!-- <td>{{ $item->type && $item->type < 0 ? 'بدهکار' : 'بستانکار' }}</td> -->
                        <td> <a class="btn btn-info" href="{{ route('sanad_edit',$item->id) }}"> ویرایش  </a> </td>
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
                        <td colspan='6'>
                                جمع کل:
                        </td>
                        <!-- <td colspan='1'> {{number_format($sanads->sum('total_creditor'))}} </td> -->
                        <td colspan='1'> {{number_format($sanads->sum('total_price'))}} </td>
                       
                        <td colspan='1'> {{number_format($sanads->sum('total_supporter'))}} </td>
                        <td colspan='1'> {{number_format($sanads->sum('total_debtor'))}} </td>
                        <td colspan='1'> {{number_format($sanads->sum('total_supporter')-$sanads->sum('total_debtor'))}} </td>
                        
                        
                        <!--<td colspan='2'> {{number_format($sanads->sum('total_total_cost'))}} </td> -->
                        
                      </tr>
                </table>
                <form method="post">
                        @csrf
                        <div class="row">
                                      <div class="col-3">
                                        <label for="supporter_amount_edit">ویرایش سهم پشتیبان</label>
                                          <input type="text"  id="supporter_amount_edit" name="supporter_amount_edit" class="form-control select2" value="">
                                      </div>  
                                      <div class="col-3">
                                       
                                          <input type="text"  id="supporter_id_edit" name="supporter_id_edit" class="form-control select2" value="">
                                      </div> 
                                      <div class="col-3">
                                       
                                          <input type="text"  id="year_id_edit" name="year_id_edit" class="form-control select2" value="">
                                      </div> 
                                      <div class="col-3">
                                       
                                          <input type="text"  id="month_id_edit" name="month_id_edit" class="form-control select2" value="">
                                      </div>   
                        </div>
                        <div class="col-3">
                                  <div class="form-group">
                                      <label for="to_date">&nbsp;</label>
                                      <a href="#" class="btn btn-success form-control" onclick="theEditAll()" >اعمال تغییرات</a>
                                      <img id="loading" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
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
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- page script -->
<script>
 let table = "";

    function theEditAll(){     
        // $(myself).prop('disabled',true);
         $('#loading').css('display','inline');        
         table.ajax.reload();
        return false;
    }
    function theSearch(){
     
      // $.post("{{ route('searchIndex') }}",{flag:1,year:$("#year").val(),month:$("#month").val()},function(res){
      //   console.log("the res is:"+ res);
      // });
     // alert($("#name").val());
        //$(myself).prop('disabled',true);
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
    $(function () { 
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
      table = $('#sanadtbl').DataTable({
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
            "targets": [0,9]
        },
        //{ "type": "pstring", "targets": [2,3,4] }
        ],

        "order": [[1, 'asc']], /// sort columns 2
        serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('searchIndex') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                   // data['name'] = "fFF";//$("#name").val();
                    data['supporter_id'] = $("#supporter_id").val();
                    data['month'] = $("#month").val();
                    data['year'] = $("#year").val();
                    //data['sanad_year'] = $('#sanad_year').val();
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                    $('#loading').css('display','none');
                    //$('#theBtn').prop('disabled',false);
                    //console.log("res" + data['name']);
                }
            },
            columns: [                
                { data: 'row'},
                { data: 'supporter' },                   
                { data: 'number' },
                { data: 'updated_at' },
                { data: 'description' },               
                { data: 'total_cost' },
                { data: 'total_get' },
                { data: 'supporter_percent' },
                { data: 'total_give' },                
                { data: 'end' },
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
