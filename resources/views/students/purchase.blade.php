@extends('layouts.index')
@section('css')
<style>
    .list_style_type_none {
        list-style-type: none;
    }

</style>

@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    پرداخت های
                    @if($main)
                      {{$mainTitleOfPurchasePage}}
                    @elseif($auxilary)
                      {{$auxilaryTitleOfPurchasePage}}
                    @elseif($secondAuxilary)
                      {{$secondAuxilaryTitleOfPurchasePage}}
                    @elseif($thirdAuxilary)
                      {{$thirdAuxilaryTitleOfPurchasePage}}
                    @elseif($student)
                    {{ $student->first_name}} {{$student->last_name}}
                    [{{$student->phone}}]
                    @endif
                </h1>
                <br>
                @if($main)
                {!! $relatedToMain !!}
                @elseif($auxilary)
                {!! $relatedToAuxilary!!}
                @elseif($secondAuxilary)
                {!! $relatedToSecondAuxilary !!}
                @elseif($thirdAuxilary)
                {!! $relatedToThirdAuxilary !!}
                @endif
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
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>کد</th>
                                <th>خریدکننده</th>
                                <th>شماره فاکتور</th>
                                <th>محصول</th>
                                <th>مبلغ</th>
                                <th>توضیحات</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->student->first_name }} {{ $item->student->last_name}}
                                    [{{$item->student->phone}}]</td>
                                <td>{{ $item->factor_number }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->price) }}</td>
                                <td>{{ $item->description }}</td>
                                <td>
                                </td>
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
<!-- <script src="../../plugins/datatables/jquery.dataTables.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script> -->

<script src="../../plugins/DataTables/DataTables-1.13.4/js/jquery.dataTables.js"></script>  
<script src="../../plugins/DataTables/DataTables-1.13.4/js/dataTables.bootstrap4.js"></script>  

<!-- page script -->
<script>
    $(function () {
        //   $("#example1").DataTable();
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
            },
            "columnDefs": [   ////define column 1 and 3
            {
                "searchable": false,
                "orderable": false,
                "targets": [0,7]
            },
            { "type": "pstring", "targets": [2,4,6] }

            ],
            "order": [[1, 'asc']], /// sort columns 2
          });

          $(".btn-danger").click(function(e){
              if(!confirm('آیا مطمئنید؟')){
                e.preventDefault();
              }
          });
        });

</script>
@endsection
