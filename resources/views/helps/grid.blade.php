@extends('layouts.index')


@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>آموزش و راهنما</h1>
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
                    <div class="row border p-1 m-1">
                        <div class="col-md-12">
                            فیلم ها
                        </div>
                        @if(count($user_helps_video))
                        @foreach ($user_helps_video as $index => $item)
                            <div class="col-md-3 col-sm-4 col-xs-6" >
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <img class="w-100" onclick="showVideo('{{ $item->link }}')" src="/dist/img/{{$item->type}}.png" />
                                    </div>
                                    <div class="card-body">
                                        {{$item->name}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @else
                        <p class="text-danger">هیچ ویدیوی آموزشی پیدا نشد!</p>
                        @endif
                    </div>
                    <div class="row border p-1 m-1">

                        <div class="col-md-12">
                            فایل های آموزشی
                        </div>
                        @if(count($user_helps_file))
                        @foreach ($user_helps_file as $index => $item)
                            <div class="col-md-3 col-sm-4 col-xs-6" >
                                <div class="card card-outline card-primary">
                                    <div class="card-header">
                                        <img class="w-100" onclick="showVideo('{{ $item->link }}')" src="/dist/img/{{$item->type}}.png" />
                                    </div>
                                    <div class="card-body">
                                        {{$item->name}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @else
                        <p class="text-danger">هیچ فایل آموزشی پیدا نشد!</p>
                        @endif
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-12">
            <iframe id="load-frame" style="width: 100%;height: 500px;" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" ></iframe>
        </div>
    </div>
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
                "emptyTable": "داده ای برای نمایش وجود ندارد",
                "info": "نمایش _START_ تا _END_ از _TOTAL_ داده",
                "infoEmpty": "نمایش 0 تا 0 از 0 داده",
            }
        });

        $(".btn-danger").click(function (e) {
            if (!confirm('آیا مطمئنید؟')) {
                e.preventDefault();
            }
        });
        showVideo = (inp) => {
            $('#load-frame').prop('src', inp);
            $('html, body').animate({
                scrollTop: $("#load-frame").offset().top
            }, 500);
        }
    });

</script>
@endsection
