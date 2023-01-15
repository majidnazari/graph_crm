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
<section class="content-header ">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1> گزارش لاگ دانش آموزان مرج شده </h1>
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
            
             <div class="col-2">
                <label for="successfull_id">وضعیت</label>
                <select id="successfull_id" name="successfull_id" class="form-control" onchange="theChange()">
                    <option value="-1">همه</option>
                    <option value="0">ناموفق</option>
                    <option value="1">موفق</option>
                   
                </select>

            </div>

            <div class="col-3">
                <label for="supporter_id">کاربر</label>
                <select id="supporter_id" name="supporter_id" class="form-control select2" onchange="theChange()">
                    <option value="0">همه</option>
                    @foreach ($supporter as $item)
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
                <label for="main_student_id">دانش آموز اصلی</label>
                <select id="main_student_id" name="main_student_id" class="form-control select2" onchange="theChange()">
                    <option value="0">همه</option>
                    @foreach ($main_student as $item)
                    @if(isset($main_student_id) && $main_student_id==$item->id)
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
                <label for="merged_student_id">دانش آموز مرج شده</label>
                <select id="merged_student_id" name="merged_student_id" class="form-control select2" onchange="theChange()">
                    <option value="0">همه</option>
                    @foreach ($merged_student as $item)
                    @if(isset($merged_student_id) && $merged_student_id==$item->id)
                    <option value="{{ $item->id }}" selected>
                        @else
                    <option value="{{ $item->id }}">
                        @endif
                        {{ $item->first_name }} {{ $item->last_name }}
                    </option>
                    @endforeach
                </select>

            </div>

            <div class="col-1">
                <div class="form-group">
                    <label for="to_date">&nbsp;</label>
                    <a href="#" class="btn btn-success form-control" onclick="theSearch()">جستجو</a>
                    <img id="loading" src="/dist/img/loading.gif" style="height: 20px;display: none;" />
                </div>
            </div>


        </div>
    </form>

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
                    <table id="mergetbl" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>وضعیت</th>
                                <th>دانش آموز اصلی</th>
                                <th>تلفن دانش آموز اصلی</th>
                                <th>تلفن ۱ دانش آموز اصلی</th>
                                <th>تلفن ۲ دانش آموز اصلی</th>
                                <th>تلفن ۳ دانش آموز اصلی</th>
                                <th>تلفن ۴ دانش آموز اصلی</th>
                                <th>شماره موبایل دانش آموز اصلی</th>
                                <th>تلفن مادر دانش آموز اصلی</th>
                                <th>تلفن پدر دانش آموز اصلی</th>
                                <th> دانش آموز مرج شده </th>
                                <th>تلفن دانش آموز مرج شده</th>
                                <th>تلفن ۱ دانش آموز مرج شده</th>
                                <th>تلفن ۲ دانش آموز مرج شده</th>
                                <th>تلفن ۳ دانش آموز مرج شده</th>
                                <th>تلفن ۴ دانش آموز مرج شده</th>
                                <th>شماره موبایل دانش آموز مرج شده</th>
                                <th>تلفن مادر دانش آموز مرج شده</th>
                                <th>تلفن پدر دانش آموز مرج شده</th>
                                <th> کاربر </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($merged_students as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{$item->successfull }}</td>
                                <td>{{ $item->current_student_fullname }}</td>
                                <td>{{ $item->old_student_fullname }}</td>

                               
                                <!-- <td>{{ jdate($item->updated_at)->format("Y/m/d") }}</td> -->
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
<script src="/plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- page script -->
<script>
    let table = "";

    

    function theSearch() {

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
        $("#Content_Div_Class").css("width","130%"); // set layout size bigger when get into log page
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#supporter_id").select2({
            ajax: {
                url: "{{ route('searchSupporterForMergeStudent') }}",
                dataType: 'json',
                delay: 0,
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
            placeholder: 'حداقل باید ۲ کاراکتر وارد کنید',
            minimumInputLength: 2,
        });


        $('#main_student_id').select2({
          ajax: {
            url: "{{ route('searchMainStudentForMergeStudent') }}",
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

        $('#merged_student_id').select2({
          ajax: {
            url: "{{ route('searchMergedStudentForMergeStudent') }}",
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

        table = $('#mergetbl').DataTable({
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
                    "targets": [0, 19]
                },
                //{ "type": "pstring", "targets": [2,3,4] }
            ],

            "order": [1, "asc"], /// sort columns 4
            serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('search_index_Student') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function(data) {
                    // console.log("the data is:");
                    // console.log(data);
                    //alert($("#receipt_date_ser").val());
                    // data['name'] = "fFF";//$("#name").val();
                    data['successfull_id'] = $("#successfull_id").val();
                    data['user_id'] = $("#user_id").val();
                    data['supporter_id'] = $("#supporter_id").val();
                    data['main_student_id'] = $("#main_student_id").val();
                    data['merged_student_id'] = $("#merged_student_id").val();
                    //   data['student_id'] = $("#student_id").val();
                    //   data['receipt_date_ser'] = $("#receipt_date_ser").val();
                    //   data['month'] = $("#month").val();
                    //   data['year'] = $("#year").val();

                    //data['sanad_year'] = $('#sanad_year').val();
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                    $('#loading').css('display', 'none');
                    // console.log("the result is :");
                    // console.log(response);
                    //   let total_remain = response.responseJSON.total_give_price - response.responseJSON.total_supporter;
                    //   $('#loading').css('display', 'none');
                    //   $("#total_get_price").html(Number(response.responseJSON.total_get_price).toLocaleString());
                    //   $("#total_give_price").html(Number(response.responseJSON.total_give_price).toLocaleString());
                    //   $("#total_supporter").html(Number(response.responseJSON.total_supporter).toLocaleString());
                    // $("#total_remain").html(Number(total_remain).toLocaleString());

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
                    data: 'successfull'
                },                
                {
                    data: 'current_student_fullname'
                },
                {
                    data: 'current_student_phone'
                },
                {
                    data: 'current_student_phone1'
                },
                {
                    data: 'current_student_phone2'
                },
                {
                    data: 'current_student_phone3'
                },
                {
                    data: 'current_student_phone4'
                },
                {
                    data: 'current_student_student_phone'
                },
                {
                    data: 'current_student_mother_phone'
                },
                {
                    data: 'current_student_father_phone'
                },

                {
                    data: 'old_student_fullname'
                },
                {
                    data: 'old_student_phone'
                },
                {
                    data: 'old_student_phone1'
                },
                {
                    data: 'old_student_phone2'
                },
                {
                    data: 'old_student_phone3'
                },
                {
                    data: 'old_student_phone4'
                },
                {
                    data: 'old_student_student_phone'
                },
                {
                    data: 'old_student_mother_phone'
                },
                {
                    data: 'old_student_father_phone'
                },


                {
                    data: 'user_fullname_updater'
                }
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