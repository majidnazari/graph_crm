@extends('layouts.index')

@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>دانش آموز</h1>
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
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                @if ($msg_success!=null && count($fails)>0)
                <div class="text-center">
                    <h3>
                       {{ count($fails) }} شماره به علت تکراری بودن ثبت نشده است.
                    </h3>
                    {{-- @foreach ($fails as $item)
                    <div>
                        {{ $item }}
                    </div>
                    @endforeach --}}
                </div>
                @endif
                <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="sources_id">منبع</label>
                            <select  id="sources_id" name="sources_id" class="form-control select2" required>
                                <option value="0"></option>
                                @foreach ($sources as $item)
                                    <option value="{{ $item->id }}" >
                                    {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col">
                            <div class="form-group">
                                <label for="concours_year">سال کنکور</label>
                                <input type="number" class="form-control" id="concours_year" name="concours_year" placeholder="سال کنکور"  value="{{ isset($concours_year)?$concours_year:'' }}" onkeypress="handle(event)" />
                            </div>
                        </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="attachment">فایل CSV <a target="_blank" href="/students.csv">مثال</a></label>
                            <label for="attachment"> یا فایل xlsx <a target="_blank" href="/students.xlsx">مثال</a></label>
                            <input type="file" class="form-control" id="attachment" name="attachment" required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <button class="btn btn-primary">
                            ذخیره
                        </button>
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
<!-- Select2 -->
<script src="/plugins/select2/js/select2.full.min.js"></script>
<script>
    $(document).ready(function(){
        $('select.select2').select2();
    });
</script>
@endsection
