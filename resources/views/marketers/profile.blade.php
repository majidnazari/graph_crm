@extends('layouts.index')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>ویرایش پروفایل کاربر</h1>
            </div>
          </div> 
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        @if($msg!='')
          <div class="alert alert-danger alert-dismissible" > {{ $msg }} </div>
        @endif
        <form method="post" action="{{ route('marketerprofile') }}" enctype="multipart/form-data" >
          <div class="row p-2">
              @csrf
              <div class="col-md-4 col-sm-6 col-xs-12 mt-2" >
                <label for="cell_phone" >تلفن همراه</label>
                <div>{{ $marketer->cell_phone }}</div>
              </div>
              <div class="col-md-4 col-sm-6 col-xs-12 mt-2" >
                <label for="first_name" >نام</label>
              <input class="form-control"  name="first_name" id="first_name" value="{{ $marketer->first_name }}" >
              </div>
              <div class="col-md-4 col-sm-6 col-xs-12 mt-2" >
                <label for="last_name" >نام خانوادگی</label>
                <input class="form-control"  name="last_name" id="last_name" value="{{ $marketer->last_name }}" >
              </div>
              <div class="col-md-4 col-sm-6 col-xs-12 mt-2" >
                <label for="national_code" >کدملی</label>
                <input class="form-control"  name="national_code" id="national_code" value="{{ $marketer->national_code }}" >
              </div>
              <div class="col-md-4 col-sm-6 col-xs-12 mt-2" >
                <label for="birthdate" >تاریخ تولد</label>
                <input class="form-control"  name="birthdate" id="birthdate" value="{{ $marketer->birthdate }}"  >
              </div>
              <div class="col-md-4 col-sm-6 col-xs-12 mt-2" >
                <label for="home_phone" >تلفن منزل</label>
                <input class="form-control"  name="home_phone" id="home_phone" value="{{ $marketer->home_phone }}"  >
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12 mt-2" >
                <label for="address" >نشانی</label>
                <input class="form-control"  name="address" id="address" value="{{ $marketer->address }}" >
              </div>
              <div class="col-12 my-3" >اطلاعات بانکی</div>
              <div class="col-md-12 col-sm-12 col-xs-12 mt-2" >
                <label for="bank_card" >شماره کارت</label>
                <input class="form-control"  name="bank_card" id="bank_card" value="{{ $marketer->bank_card }}" style="direction: ltr !important;" >
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12 mt-2" >
                <label for="bank_shaba" >شماره شبا</label>
                <div class="row" >
                  <div class="col-11" >
                    <input  type="number" class="form-control"  name="bank_shaba" id="bank_shaba" value="{{ $marketer->bank_shaba }}" style="direction: ltr !important;"  >
                  </div>
                  <div class="col-1" >
                    IR
                  </div>
                </div>
              </div>
              <div class="col-12 my-3" >احراز هویت</div>
              <div class="col-md-12 col-sm-12 col-xs-12 mt-2" >
                <label for="image_path" >تصویر</label>
                @if($marketer->image_path)
                    <a href="{{ $marketer->image_path }}" target="_blank" >
                      <img class="col-md-4" src="{{ $marketer->image_path }}" >
                    </a>
                @endif  
                @if($marketer->enabled == 'no')
                  <input type="file"  name="image_path" id="image_path" >
                @endif
              </div>
              <div class="col-md-12 col-sm-12 col-xs-12 mt-2" >
                <label for="background" >سوابق کاری</label>
                <textarea class="form-control" name="background" id="background" rows="3" placeholder="سوابق کاری ...">{{ $marketer->background }}</textarea>              
              </div>
              <div class="col-md-4 col-sm-12 col-xs-12 mt-2" >
                <label for="education" >تحصیلات</label>
                <input class="form-control"  name="education" id="education" value="{{ $marketer->education }}" >
              </div>
              <div class="col-md-4 col-sm-12 col-xs-12 mt-2" >
                <label for="major" >رشته</label>
                <input class="form-control"  name="major" id="major" value="{{ $marketer->major }}" >
              </div>
              <div class="col-md-4 col-sm-12 col-xs-12 mt-2" >
                <label for="university" >دانشگاه</label>
                <input class="form-control"  name="university" id="university" value="{{ $marketer->university }}" >
              </div>
              <div class="col-md-4 col-sm-12 col-xs-12 mt-2" >
                <button class="btn btn-primary" >ذخیره</button>
              </div>
          </div>
        </form>
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
    
</script>
@endsection
