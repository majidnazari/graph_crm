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
        <h1>سند</h1>
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
          <form method="POST" enctype="multipart/form-data" id="frm">
            @csrf
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label for="supporter_percent"> مبلغ کل </label>
                  @if (isset($sanad) && isset($sanad->id))
                  <input type="number" class="form-control" id="total_cost" name="total_cost" placeholder="مبلغ کل" value="" />
                  @else
                  <input type="number" class="form-control" id="total_cost" name="total_cost" placeholder="مبلغ کل" />
                  @endif
                </div>
                <div class="form-group">
                  <label for="total">قیمت دریافتی(تومان)</label>
                  @if (isset($sanad) && isset($sanad->id))
                  <input type="number" class="form-control" id="total" name="total" placeholder="قیمت دریافتی " value="{{number_format($sanad->total) }}" />
                  @else
                  <input type="number" class="form-control" id="total" name="total" placeholder="قیمت دریافتی" />
                  @endif
                </div>

                <div class="form-group" id="block_supporter">
                  <label for="supporter_percent">سهم پشتیبان(درصد)</label>
                  @if (isset($sanad) && isset($sanad->id))
                  <input type="number" class="form-control" id="supporter_percent" name="supporter_percent" placeholder="سهم" value="{{ $sanad->supporter_percent }}" />
                  @else
                  <input type="number" class="form-control" id="supporter_percent" name="supporter_percent" placeholder="سهم" />
                  @endif
                </div>
                <div class="form-group">
                  <label for="description">توضیحات</label>
                  @if (isset($sanad) && isset($sanad->id))
                  <input type="text" class="form-control" id="description" name="description" placeholder="توضیحات" value="{{ $sanad->description }}" />
                  @else
                  <input type="text" class="form-control" id="description" name="description" placeholder="توضیحات" />
                  @endif
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label for="supporter_id">پشتیبان</label>
                  <select id="supporter_id" name="supporter_id" class="form-control select2">
                    <option value="0"></option>
                    @foreach ($supports as $item)
                    @if (isset($sanad) && isset($sanad->id) && $sanad->supporter_id == $item->id)
                    <option value="{{ $item->id }}" selected>
                      @else
                    <option value="{{ $item->id }}">
                      @endif
                      {{ $item->first_name. ' ' . $item->last_name }}
                    </option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="number">شماره سند</label>
                  @if (isset($sanad) && isset($sanad->id))
                  <input type="text" class="form-control" id="number" name="number" placeholder="سند" value="{{ $sanad->number }}" />
                  @else
                  <input type="text" class="form-control" id="number" name="number" placeholder="سند" />
                  @endif
                </div>
                <div class="form-group">
                  <label for="number">بستانکار</label>
                  @if (isset($sanad) && isset($sanad->id) && isset($sanad->type) && $sanad->type
                  < 0) <input type="checkbox" class="form-control" id="type" name="type" onclick="checkType(this);" />
                  @else
                  <input type="checkbox" class="form-control" id="type" name="type" onclick="checkType(this);" checked />
                  @endif
                </div>

                <!-- <div class="form-group">
                  <label for="description">نام دانش آموز</label>
                  @if (isset($sanad) && isset($sanad->id))
                  <input type="text" class="form-control" id="student_fullname" name="student_fullname" placeholder="نام کامل دانش آموز" value="{{ $sanad->student_fullname }}" />
                  @else
                  <input type="text" class="form-control" id="student_fullname" name="student_fullname" placeholder="نام کامل دانش آموز" />
                  @endif
                </div> -->

                <div class="form-group">
                  <label for="student_id">دانش آموز</label>
                  <select id="student_id" name="student_id" class="form-control select2">
                    <option value="0"></option>
                   
                    @if (isset($sanad) && isset($sanad->id) && $sanad->supporter_id == $item->id)
                    <option value="{{ $item->id }}" selected>
                      @else
                    <option value="{{ $item->id }}">
                      @endif
                      {{ $item->first_name. ' ' . $item->last_name }}
                    </option>
                    
                  </select>
                </div>

                <div class="col">
                  <div class="form-group">
                    <label for="from_date"> تاریخ دریافت پول </label>
                    <input type="text" id="receipt_date" name="receipt_date" class="form-control pdate"
                     value="" />
                  </div>
                </div>

              </div>

            </div>

            <div class="row">
              <div class="col">
                <button class="btn btn-primary" type="button" onclick="submitForm()">
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
<script src="/plugins/select2/js/select2.full.min.js"></script>
<!-- Select2 -->
<!-- <script src="/plugins/select2/js/select2.full.min.js"></script> -->
<script>
  $(document).ready(function() {

    $("#supporter_id").select2();
    $('#student_id').select2({
      ajax: {
        url: "{{ route('searchStudentForSanad') }}",
        dataType: 'json',
        delay: 250,
        data: function(params) {
          return {
            q: params.term, // search term
            //page: params.page
          };
        },
        processResults: function(data) {
          console.log("data" , data);
          return {
            results: data
          };
        },
        cache: true
      },
      placeholder: 'حداقل باید ۳ کاراکتر وارد کنید',
      minimumInputLength: 3,

    });
  });

  function checkType(dobj) {
    if ($(dobj).prop('checked')) {
      var x = document.getElementById("block_supporter");
      x.style.display = "block";

    } else {
      var x = document.getElementById("block_supporter");
      // document.getElementById("supporter_percent").innerHTML = "100";
      $("#supporter_percent").val('100');
      x.style.display = "none";

    }
  }

  function submitForm() {
    if (!validateForm()) {
      return false;
    }
    $('#frm').submit();
  }

  function validateForm() {
    if ($('#total_cost').val() === "") {
      alert("مقدار مبلغ کل را وارد نمایید.");
      return false;
    }
    if ($('#total').val() === "") {
      alert("مقدار مبلغ دریافتی  را وارد نمایید.");
      return false;
    }
    if ($('#supporter_percent').val() === "") {
      alert(" سهم پشتیبان  را وارد نمایید.");
      return false;
    }
    if ($('#number').val() === "") {
      alert("  شماره سند  را وارد نمایید.");
      return false;
    }
    if ($('#supporter_id').val() == 0) {
      alert("   پشتیبان  را وارد نمایید.");
      return false;
    }
    if ($('#student_id').val() == 0) {
      alert("   دانش آموز  را وارد نمایید.");
      return false;
    }
    if ($('#description').val() === "") {
      alert(" توضیحات را وارد نمایید.");
      return false;
    }
    // if ($('#receipt_date').val() === "") {
    //   alert(" تاریخ دریافت وجه را وارد نمایید.");
    //   return false;
    // }
    
    return true;
  }
</script>
@endsection