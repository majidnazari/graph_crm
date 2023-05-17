@extends('layouts.index')
@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
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
              @if($isSingle)
              <h1>تماس ها</h1>
              @else
              <h1>تماس پشتیبان ها</h1>
              @endif
            </div>
            <div class="col-sm-6">
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <form method="POST">
            @csrf
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label for="from_date">از تاریخ</label>
                        <input type="text" id="from_date" name="from_date" class="form-control pdate" value="{{ ($from_date)?jdate($from_date)->format("Y/m/d"):jdate()->format("Y/m/d") }}" />
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="to_date">تا تاریخ</label>
                        <input type="text" id="to_date" name="to_date" class="form-control pdate" value="{{ ($to_date)?jdate($to_date)->format("Y/m/d"):jdate()->format("Y/m/d") }}" />
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="products_id">محصول</label>
                        <select id="products_id" name="products_id" class="form-control select2" onchange="theChange()" >
                            <option value="">همه</option>
                            @foreach ($products as $item)
                                @if($products_id && $products_id == $item->id)
                                <option selected value="{{ $item->id }}">
                                    {{ ($item->parents!='-')?$item->parents . '->':'' }}{{ $item->name }}
                                </option>
                                @else
                                <option value="{{ $item->id }}">
                                    {{ ($item->parents!='-')?$item->parents . '->':'' }}{{ $item->name }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="notices_id">اطلاع رسانی</label>
                        <select id="notices_id" name="notices_id" class="form-control" onchange="theChange()">
                            <option value="">همه</option>
                            @foreach ($notices as $item)
                                @if($notices_id && $notices_id == $item->id)
                                <option selected value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                                @else
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(!$isSingle)
                <div class="col-3">
                    <div class="form-group">
                        <label for="supporters_id">پشتیبان</label>
                        <select id="supporters_id" name="supporters_id" class="form-control select2" onchange="theChange()" >
                            <option value="">همه</option>
                            @foreach ($supportersForSelectInView as $item)
                                @if($supporters_id && $supporters_id == $item->id)
                                <option selected value="{{ $item->id }}">
                                    {{ $item->first_name }} {{ $item->last_name }}
                                </option>
                                @else
                                <option value="{{ $item->id }}">
                                    {{ $item->first_name }} {{ $item->last_name }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif
                <div class="col-3">
                    <div class="form-group">
                        <label for="replier_id">پاسخ دهنده</label>
                        <select id="replier_id" name="replier_id" class="form-control" onchange="theChange()">
                            <option value="">همه</option>
                            @foreach ($persons as $key=>$item)
                                @if($replier_id && $replier_id == $key)
                                <option selected value="{{ $key }}">
                                    {{ $item }}
                                </option>
                                @else
                                <option value="{{ $key }}">
                                    {{ $item }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="to_date">منبع ورودی</label>
                        <select id="sources_id" name="sources_id" class="form-control" onchange="theChange()">
                            <option value="">همه</option>
                            @foreach ($sources as $item)
                                @if($sources_id && $sources_id == $item->id)
                                <option selected value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                                @else
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
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
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    @if(!$isSingle)
                    <th>کد</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    @endif
                    <th>تماس</th>
                    @foreach ($callResults as $item)
                        <th>
                            {{ $item->title }}
                        </th>
                    @endforeach
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

<script src="/plugins/select2/js/select2.full.min.js"></script>
<!-- page script -->
<script>
    <!-- Select2 -->

    $('select.select2').select2();
    let table = "";
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
    function showStudents(index){
        $("#students-" + index).toggle();

        return false;
    }
    function showStudentTags(index){
        $("#studenttags-" + index).toggle();

        return false;
    }
    function changePass(user_id) {
        var password = prompt('لطفا رمز عبور جدید را وارد کنید:');
        if(!confirm(`آیا رمز عبور به ${password} تغییر یابد؟`)){
            return false;
        }

        $.post('{{ route("user_supporter_changepass") }}', {
            user_id,
            password,
            _token: "{{ csrf_token() }}"
        }, function(res){
            if(res.error==null)
                window.location.reload();
        });
        return false;
    }
    $(function () {
        $(".btn-danger").click(function(e){
          if(!confirm('آیا مطمئنید؟')){
            e.preventDefault();
          }
      });
      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
      });
      table = $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": "{{ $isSingle ? false : true }}",
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
            @if($isSingle)
              columnDefs: [ { orderable: false, targets: [0,1,2,3,4,5,6,7,8,9,10] },],
            @else
              columnDefs: [ { orderable: false, targets: 0 }],
            @endif
            @if(!$isSingle)
            "order": [[1, 'asc']], /// sort columns 1
            @endif
            serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('user_supporter_calls_post') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                    data["from_date"] = $('#from_date').val();
                    data["to_date"] = $('#to_date').val();
                    data["products_id"] = $('#products_id').val();
                    data["notices_id"] = $('#notices_id').val();
                    data["supporters_id"] = $('#supporters_id').val();
                    data["replier_id"] = $('#replier_id').val();
                    data["sources_id"] = $('#sources_id').val();
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                    $('#loading').css('display','none');
                }

            },
            @if($isSingle)
            columns: [
                { data: 'row'},
                { data: 'call_count' },
                @foreach($callResults as $item)
                   { data: 'call_result' +  '{{ $item->id }}' },
                @endforeach
            ],
            @else
            columns: [
                { data: 'row'},
                { data: 'id' },
                { data: 'first_name' },
                { data: 'last_name' },
                { data: 'call_count' },
                @foreach($callResults as $item)
                   { data: 'call_result' +  '{{ $item->id }}' },
                @endforeach
            ],
            @endif
        });


    });
  </script>
@endsection
