@extends('layouts.index')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .students,
    .studenttags {
        display: none;
    }

</style>
@endsection

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-12">
                <h1>
                    دانش آموزان با تماس عدم نیاز
                </h1>
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
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>کد</th>
                                <th>نام</th>
                                <th>نام خانوادگی</th>
                                <th>کاربر ثبت کننده</th>
                                <th>منبع ورودی شماره</th>
                                <th>برچسب</th>
                                <th>داغ/سرد</th>
                                <th>توضیحات</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{--  @if(!empty($no_need_calls_students))
                            @foreach ($no_need_calls_students as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->first_name}}</td>
                                <td>{{ $item->last_name }}</td>
                        <td>{{ ($item->user)?$item->user->first_name . ' ' . $item->user->last_name:'-' }}</td>
                                <td>{{ ($item->source)?$item->source->name:'-'  }}</td>
                                @if(($item->studenttags && count($item->studenttags)>0) || ($item->studentcollections &&
                                count($item->studentcollections)>0))
                                <td>
                                    @for($i = 0; $i < count($item->studenttags);$i++)
                                        <span class="d-inline-block px-1 rounded small mt-2 bg-cyan p-1">
                                            {{ $item->studenttags[$i]->tag->name }}
                                        </span><br />
                                        @endfor
                                        @for($i = 0; $i < count($item->studentcollections);$i++)
                                            @if(isset($item->studentcollections[$i]->collection))
                                            <span class="d-inline-block px-1 rounded small mt-2 bg-warning p-1">
                                                {{ ($item->studentcollections[$i]->collection->parent) ? $item->studentcollections[$i]->collection->parent->name . '->' : '' }}
                                                {{ $item->studentcollections[$i]->collection->name }}
                                            </span><br />
                                            @endif
                                            @endfor
                                </td>
                                @else
                                <td></td>
                                @endif
                                @if($item->studenttemperatures && count($item->studenttemperatures)>0)
                                <td>
                                    @foreach ($item->studenttemperatures as $sitem)
                                    @if($sitem->temperature->status=='hot')
                                    <span class="alert alert-danger p-1">
                                        @else
                                        <span class="alert alert-info p-1">
                                            @endif
                                            {{ $sitem->temperature->name }}
                                        </span>
                                        @endforeach
                                </td>
                                @else
                                <td></td>
                                @endif
                                <td>{{ $item->description }}</td>
                            </tr>
                            @endforeach
                            @endif  --}}
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
    var table = "";
    function showStudents(index) {
        $("#students-" + index).toggle();

        return false;
    }

    function showStudentTags(index) {
        $("#studenttags-" + index).toggle();

        return false;
    }
    $(".btn-danger").click(function(e) {
        if (!confirm('آیا مطمئنید؟')) {
            e.preventDefault();
        }
    });
    $(function() {
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
            columnDefs: [ { orderable: false, targets: [0,7] } ],
            "order": [[1, 'asc']], /// sort columns 1
            serverSide: true,
            processing: true,
            ajax: {
                "type": "POST",
                "url": "{{ route('no_need_students') }}",
                "dataType": "json",
                "contentType": 'application/json; charset=utf-8',

                "data": function (data) {
                    return JSON.stringify(data);
                },
                "complete": function(response) {
                }

            },
            columns: [
                { data: 'row'},
                { data: 'id' },
                { data: 'first_name' },
                { data: 'last_name' },
                { data: 'users_id' },
                { data: 'sources_id'},
                { data: 'tags'},
                { data: 'temps'},
                { data: 'description'}
            ],
        });
    });



</script>
@endsection
