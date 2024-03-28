@extends('layouts.index')
@section('css')
<link href="/plugins/select2/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
            <h1>خروجی اکسل از پروفایل دانش آموزان</h1>
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
          <div class="card-body">

            <h3 class="text-center">
               فیلتر
            </h3>
            <form method="post">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="students_select"> دانش آموزان</label>
                            <select  id="students_select" name="students_select" class="form-control">
                                <option value="all">همه</option>
                                <option value="students">لیست  دانش آموزان</option>
                                <option value="archive_students">دانش آموزان لیست آرشیو</option>
                                <option value="black_students">دانش آموزان لیست سیاه</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="from_date">از تاریخ</label>
                            <input type="text" id="from_date" name="from_date" class="form-control pdate" value="{{ ($from_date)?jdate($from_date)->format("Y/m/d"):jdate()->format("Y/m/d") }}" />
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="from_date">تا تاریخ</label>
                            <input type="text" id="to_date" name="to_date" class="form-control pdate" value="{{ ($to_date)?jdate($to_date)->format("Y/m/d"):jdate()->format("Y/m/d") }}" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="egucation_level">مقطع</label>
                            <select  id="egucation_level" name="egucation_level" class="form-control">
                                <option value="">همه</option>
                                @foreach ($egucation_levels as $key => $item)
                                    @if($key!=null)
                                    @if(isset($egucation_level) && $egucation_level==$key)
                                    <option value="{{ $key }}" selected >
                                    @else
                                    <option value="{{ $key }}" >
                                    @endif
                                    {{ $item }}
                                    </option>
                                    @endif
                                @endforeach
                                    <option value="">
                                     نامشخص
                                    </option>
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
                            <label for="major">رشته</label>
                            <select  id="major" name="major" class="form-control">
                                <option value="">همه</option>
                                @foreach ($majors as $key => $item)
                                    @if(isset($egucation_level) && $egucation_level==$key)
                                    <option value="{{ $key }}" selected >
                                    @else
                                    <option value="{{ $key }}" >
                                    @endif
                                    {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="supporters_id">پشتیبان</label>
                            <select  id="supporters_id" name="supporters_id" class="form-control select2">
                                <option value="">همه</option>
                                @foreach ($supports as $item)
                                    @if(isset($supporters_id) && $supporters_id==$item->id)
                                    <option value="{{ $item->id }}" selected >
                                    @else
                                    <option value="{{ $item->id }}" >
                                    @endif
                                    {{ $item->first_name }} {{ $item->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col" style="padding-top: 32px;">
                        <button type="submit" class="btn btn-primary">
                            دریافت
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
<script src="/plugins/select2/js/select2.full.min.js"></script>
<script>
    $('select.select2').select2();
</script>
@endsection
