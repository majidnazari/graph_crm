@extends('layouts.index')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>
                  کلاس های
                  {{ $student->first_name }} {{ $student->last_name }} [{{ $student->phone }}]
              </h1>
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
                <form method="POST" action="{{ route('student_class_add', ['student_id' => $student->id]) }}">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                            <select id="class_rooms_id" name="class_rooms_id" class="form-control">
                                <option disabled selected value="">
                                    کلاس
                                </option>
                                @foreach ($classes as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="col">
                            <button class="btn btn-danger form-control">
                                افزودن کلاس
                            </button>
                        </div>
                    </div>
                </form>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th>کد</th>
                    <th>کلاس</th>
                    <th>#</th>
                  </tr>
                  </thead>
                  <tbody>
                      @foreach ($student->studentclasses as $index => $item)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->class_rooms_id }}</td>
                        <td>{{ $item->class->name }}</td>
                        <td>
                            <a class="btn btn-danger" href="{{ route('student_class_delete', ['student_id' => $item->students_id, 'id' => $item->id]) }}">
                                حذف
                            </a>
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
        }
      });

      $(".btn-danger").click(function(e){
          if(!confirm('آیا مطمئنید؟')){
            e.preventDefault();
          }
      });
    });
  </script>
@endsection
