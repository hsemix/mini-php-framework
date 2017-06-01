<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Musawo app </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/<?=$resource?>bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/<?=$resource?>dist/css/fonts/font-awesome/css/font-awesome.min.css">
  <link rel="icon" href="/<?=$resource?>dist/img/fav-icon.png" type="image/x-icon" />
  
  <link rel="stylesheet" href="/<?=$resource?>plugins/iCheck/square/blue.css">

 <!-- Theme style -->
  <link rel="stylesheet" href="/<?=$resource?>dist/css/AdminLTE.css">
  <link rel="stylesheet" href="/<?=$resource?>dist/css/skin-blue.css">
   <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="../../index2.html"><span class="logo-lg"><img src="/<?=$resource?>dist/img/musawo-logo.png" alt="musawo app"  class="logox hidden-xs hidden-sm" /> </span></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg"> Log in  </p>

    <form action="/login" method="post" class="musawo-forms">
      <div class="form-group has-feedback">
        <input type="text" name="username" class="form-control" placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div> 
      <input type="hidden" name="token" id="token" value="<?=csrf_token();?>" />
      <div class="form-group has-feedback">
        <input type="password" name="password" class="form-control" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <div class="clearfix"></div>
        <div class="alert hide"></div>
        <!-- /.col -->
      </div>
    </form>
    <!-- /.social-auth-links -->

    <a href="/reset">I forgot my password</a><br>
    <a href="/" class="text-center">Register a new membership</a>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="/<?=$resource?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="/<?=$resource?>plugins/forms/jquery.form.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/<?=$resource?>bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/<?=$resource?>plugins/iCheck/icheck.min.js"></script>
<script src="/<?=$resource?>dist/js/musawo.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
