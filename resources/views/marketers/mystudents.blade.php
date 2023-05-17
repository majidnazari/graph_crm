@php
    $majors = [
        "mathematics"=>"ریاضی",
        "experimental"=>"تجربی",
        "humanities"=>"انسانی",
        "art"=>"هنر",
        "other"=>"دیگر"
    ];
    $egucation_levels = [
        "6"  => "ششم",
        "7"  => "هفتم",
        "8"  => "هشتم",
        "9"  => "نهم",
        "10"  => "دهم",
        "11"  => "یازدهم",
        "12"  => "دوازدهم",
        "13" => "فارغ التحصیل",
        "14" => "دانشجو",
        null => ""
    ];
@endphp
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
              <h1>دانش آموزان</h1>
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
                    <a class="btn btn-success" href="{{ route('marketercreatestudents') }}">دانش آموز جدید</a>
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <form method="post">
                    <div class="row" >
                        @csrf
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">نام و نام خانوادگی</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="نام و نام خانوادگی" value="{{ isset($name)?$name:'' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phone">تلفن</label>
                                <input type="number" class="form-control" id="phone" name="phone" placeholder="تلفن"  value="{{ isset($phone)?$phone:'' }}" />
                            </div>
                        </div>
                        <div class="col-md-4" style="padding-top: 32px;">
                            <button class="btn btn-success">
                                جستجو
                            </button>
                        </div>
                    </div>
                </form>
                </div>

                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>تلفن</th>
                  </tr>
                  </thead>
                  <tbody>
                      @foreach ($students as $index => $item)
                      <tr style="cursor:pointer" >
                        <td onclick="$('.morepanel').hide();$('#morepanel-{{ $index }}').toggle();">{{ $index + 1 }}</td>
                        <td onclick="$('.morepanel').hide();$('#morepanel-{{ $index }}').toggle();">{{ $item->id }}</td>
                        <td onclick="$('.morepanel').hide();$('#morepanel-{{ $index }}').toggle();">{{ $item->first_name }}</td>
                        <td onclick="$('.morepanel').hide();$('#morepanel-{{ $index }}').toggle();">{{ $item->last_name }}</td>
                        <td onclick="$('.morepanel').hide();$('#morepanel-{{ $index }}').toggle();">{{ $item->phone }}</td>
                      </tr>
                      <tr class="morepanel" id="morepanel-{{ $index }}">
                          <td colspan="5">
                              <div class="container">
                                <div class="row">
                                    <div class="col">
                                        تراز یا رتبه سال قبل :
                                        {{ $item->last_year_grade }}
                                    </div>
                                    <div class="col">
                                        مشاور :
                                        {{ ($item->consultant)?$item->consultant->first_name . ' ' . $item->consultant->last_name:'' }}
                                    </div>
                                    <div class="col">
                                        شغل پدر یا مادر :
                                        {{ $item->parents_job_title }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        شماره منزل :
                                        {{ $item->home_phone }}
                                    </div>
                                    <div class="col">
                                        مقطع :
                                        {{ $egucation_levels[$item->egucation_level] }}
                                    </div>
                                    <div class="col">
                                        شماره موبایل والدین :
                                        {{ $item->father_phone }}
                                        {{ $item->mother_phone }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        مدرسه :
                                        {{ $item->school }}
                                    </div>
                                    <div class="col">
                                        معدل :
                                        {{ $item->average }}
                                    </div>
                                    <div class="col">
                                        رشته تحصیلی :
                                        {{ $majors[$item->major] }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <a href="{{ route('student_edit', ["call_back"=>'marketermystudents', "id"=>$item->id]) }}">
                                            ویرایش مشخصات
                                        </a>
                                    </div>
                                    <div class="col">
                                        تاریخ ثبت دانش آموز :
                                        {{ jdate(strtotime($item->created_at))->format("Y/m/d") }}
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <a href="#" onclick="$('#students_index').val({{ $index }});preloadTagModal();$('#tag_modal').modal('show'); return false;">
                                            برچسب روحیات اخلاقی
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <a href="#" onclick="$('#students_index').val({{ $index }});preloadTagModal();$('#tag_modal').modal('show'); return false;">
                                            برچسب نیازهای دانش آموز
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <a target="_blank" href="{{ route('student_purchases', $item->id) }}">
                                            گزارش خریدهای قطعی دانش آموز
                                        </a>
                                    </div>
                                </div>
                              </div>
                          </td>
                      </tr>
                      @endforeach
                  </tbody>
                  <!--
                  <tfoot>
                  <tr>
                    <th>Rendering engine</th>
                    <th>Browser</th>
                    <th>Platform(s)</th>
                    <th>Engine version</th>
                    <th>CSS grade</th>
                  </tr>
                  </tfoot>
                  -->
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
<script>
    let students = @JSON($students);
    function changeSupporter(studentsIndex,id){
        if(students[studentsIndex]){
            var students_id = id;
            var supporters_id = $("#supporters_id_" + studentsIndex).val();
            $("#loading-" + studentsIndex).show();
            $.post('{{ route('student_supporter') }}', {
                students_id,
                supporters_id
            }, function(result){
                $("#loading-" + studentsIndex).hide();
                if(result && result.error != null){
                    alert(result.error);
                }
                table.ajax.reload();
            }).fail(function(){
                $("#loading-" + studentsIndex).hide();
                alert('خطای بروز رسانی');
                table.ajax.reload();
            });
        }
        return false;
    }
    function preloadTagModal(){
        $("input.tag-checkbox").prop('checked', false);
        $("input.collection-checkbox").prop('checked', false);
        var studentsIndex = parseInt($("#students_index").val(), 10);
        if(!isNaN(studentsIndex)){
            if(students[studentsIndex]){
                console.log(students[studentsIndex].studenttags);
                for(studenttag of students[studentsIndex].studenttags){
                    $("#tag_" + studenttag.tags_id).prop("checked", true);
                }
                console.log(students[studentsIndex].studentcollections);
                for(studentcollection of students[studentsIndex].studentcollections){
                    $("#collection_" + studentcollection.collections_id).prop("checked", true);
                }            }
        }
    }

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
            }
        });
    });
  </script>
@endsection
