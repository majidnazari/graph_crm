@extends('layouts.index')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>داشبورد نماینده</h1>
            </div>
          </div> 
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-lg-6 col-xs-12">
            <!-- small box -->
            <div class="small-box bg-blue">
              <div class="inner">
                <h3 class="mt-5 font-weight-bold" >دانش آموزان من </h3>
              </div>
              <div class="icon">
                <i class="fa fa-street-view"></i>
              </div>
              <a href="#" class="small-box-footer">
                <i class="fa fa-arrow-circle-left"></i>
                مشاهده جزئیات 
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-6 col-xs-12">
            <!-- small box -->
            <div class="small-box bg-green">
              <div class="inner">
                <h3 class="mt-5 font-weight-bold" >وضعیت مالی من</h3>
              </div>
              <div class="icon">
                <i class="fa fa-donate"></i>
              </div>
              <a href="#" class="small-box-footer">
                <i class="fa fa-arrow-circle-left"></i>
                مشاهده جزئیات 
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-6 col-xs-12">
            <!-- small box -->
            <div class="small-box bg-yellow">
              <div class="inner">
                <h3 class="mt-5 font-weight-bold" >ورودی های من</h3>
              </div>
              <div class="icon">
                <i class="fa fa-address-book"></i>
              </div>
              <a href="#" class="small-box-footer">
                <i class="fa fa-arrow-circle-left"></i>
                مشاهده جزئیات 
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-6 col-xs-12">
            <!-- small box -->
            <div class="small-box bg-red">
              <div class="inner">
                <h3 class="mt-5 font-weight-bold" > دریافت بخش نامه ها</h3>
              </div>
              <div class="icon">
                <i class="fa fa-bell"></i>
              </div>
              <a href="#" class="small-box-footer">
                <i class="fa fa-arrow-circle-left"></i>
                مشاهده جزئیات 
              </a>
            </div>
          </div>
          <!-- ./col -->
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
    
</script>
@endsection
