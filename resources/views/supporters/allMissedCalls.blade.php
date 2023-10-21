@extends('layouts.index')

@section('css')
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

            <div class="col-sm-12">
              <h1>
                تعداد کل تماس های بی پاسخ
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
                    <th>عنوان</th>
                    <th>توضیحات</th>
                    <th>دانش آموز</th>
                    <th>محصول</th>
                    <th>پاسخ دهنده</th>
                    <th>نفر بعدی برای تماس</th>
                    <th>اطلاع رسانی</th>
                    <th>#</th>
                  </tr>
                  </thead>
                  <tbody>
                      @if(!empty($all_missed_calls))
                      @foreach ($all_missed_calls as $index => $item)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->description}}</td>
                        <td>{{ $item->student->first_name}} {{ $item->student->last_name}}</td>
                        <td>{{ $item->product ? $item->product->name : '-'}}</td>
                        <td>{{ $persons[$item->replier] }}</td>
                        <td>{{ $persons[$item->next_to_call] }}</td>
                        <td>{{ $item->notice ? $item->notice->name : '-'}}</td>
                        <td>
                          <form method="get" action="{{ route('supporter_students') }}" >
                            <input type="hidden" name="students_id" value="{{$item->students_id}}" />
                            <input type="hidden" name="calls_id" value="{{$item->id}}" />
                            <button class="btn btn-primary">
                              تماس
                            </button>
                          </form>
                        </td>
                      </tr>
                      @endforeach
                      @endif
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
    function showStudents(index){
        // $(".students").hide();
        $("#students-" + index).toggle();

        return false;
    }
    function showStudentTags(index){
        // $(".students").hide();
        $("#studenttags-" + index).toggle();

        return false;
    }
    $(".btn-danger").click(function(e){
        if(!confirm('آیا مطمئنید؟')){
          e.preventDefault();
        }
    });
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
        "columnDefs": [   ////define column 1 and 8
        {
            "searchable": false,
            "orderable": false,
            "targets": [0,8]
        },
        { "type": "pstring", "targets": [1,2,3,4,5,6,7] }
        ],
        "order": [[1, 'asc']], /// sort columns 2
      });


    });
  </script>
@endsection
