@extends('layouts.index')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>دانش آموزان همگام شده</h1>
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
                {{-- <h3 class="card-title">
                    <a class="btn btn-success" href="{{ route('merge_students_create') }}">همگام سازی جدید</a>
                </h3> --}}
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>ردیف</th>
                    <th> دانش آموز اصلی</th>
                    <th>فرعی ۱</th>
                    <th>فرعی ۲</th>
                    <th>فرعی ۳</th>
                  </tr>
                  </thead>
                  <tbody>
                      @foreach ($mergedStudents as $index => $item)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ ($item->mainStudent) ? $item->mainStudent->first_name : '-' }}
                            {{ ($item->mainStudent) ? $item->mainStudent->last_name : '-' }}
                           -{{($item->mainStudent) ? $item->mainStudent->phone : '-'}}
                        </td>
                        <td>
                            <a href="{{ $item->auxilaryStudent ? route('student_edit',['call_back' => 'supporter_students','id' => $item->auxilaryStudent->id]) : '#'}}" target="_blank">
                                {{ ($item->auxilaryStudent) ? $item->auxilaryStudent->first_name : '' }}
                                {{ ($item->auxilaryStudent) ? $item->auxilaryStudent->last_name : '' }}
                                -{{($item->auxilaryStudent) ? $item->auxilaryStudent->phone : ''}}
                            </a>
                        </td>
                        <td>
                            <a href="{{ $item->secondAuxilaryStudent ? route('student_edit',['call_back' => 'supporter_students','id' => $item->secondAuxilaryStudent->id]) : '#'}}" target="_blank">
                            {{ ($item->secondAuxilaryStudent) ? $item->secondAuxilaryStudent->first_name : '' }}
                            {{ ($item->secondAuxilaryStudent) ? $item->secondAuxilaryStudent->last_name : '' }}
                            -{{($item->secondAuxilaryStudent) ? $item->secondAuxilaryStudent->phone : ''}}
                            </a>
                        </td>
                        <td>
                            <a href="{{ $item->thirdAuxilaryStudent ? route('student_edit',['call_back' => 'supporter_students','id' => $item->thirdAuxilaryStudent->id]): '#'}}" target="_blank">
                            {{ ($item->thirdAuxilaryStudent) ? $item->thirdAuxilaryStudent->first_name : '' }}
                            {{ ($item->thirdAuxilaryStudent) ? $item->thirdAuxilaryStudent->last_name : '' }}
                            -{{($item->thirdAuxilaryStudent) ? $item->thirdAuxilaryStudent->phone : ''}}
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
      $('#example').DataTable({
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
