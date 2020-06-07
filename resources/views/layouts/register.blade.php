<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>
    @section('page_title')
          {{ env('APP_NAME') }}
    @show
  </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="dist/css/ionicons.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Bootstrap 4 RTL -->
  <link rel="stylesheet" href="dist/css/bootstrap.min.css">
  <!-- Custom style for RTL -->
  <link rel="stylesheet" href="/dist/css/custom.css">
</head>
<body class="hold-transition register-page" style="background-image: url('/dist/img/photo2.png');background-size: cover;" >
  <div class="register-box">
    <div class="register-logo">
      <a> {{ env('APP_NAME') }}</a>
    </div>

    <div class="register-box-body card p-3">
      <p class="login-box-msg">ثبت نام نماینده</p>
      <form action="../../index.html" method="post">
        <div class="input-group mb-3">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
          <input type="text" class="form-control" placeholder="نام" id="fname" name="fname" >
          
        </div>
        <div class="input-group mb-3">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
          <input type="text" class="form-control" placeholder="نام خانوادگی" id="lname" name="lname" >
        </div>
        <div class="input-group mb-3">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
          <input type="text" class="form-control" placeholder="تلفن همراه" id="mobile" name="mobile" >
        </div>
        
      </form>
      <div class="row" >
        <div class="col-md-4 col-sm-6 col-xs-12" >
          <button class="btn btn-primary btn-block btn-flat"> ادامه </button>
        </div>
        <div class="col-md-8 col-sm-6 col-xs-12 mt-2" >
          <a href="login" class="text-center">قبلا عضو شده ام</a>
        </div>
      </div>
    </div><!-- /.form-box -->
  </div><!-- /.register-box -->
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
