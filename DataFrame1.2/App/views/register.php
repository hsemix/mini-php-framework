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
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/<?=$resource?>dist/css/fonts/font-awesome/css/font-awesome.min.css">
  <link rel="icon" href="/<?=$resource?>dist/img/fav-icon.png" type="image/x-icon" />

 <!-- Theme style -->
  <link rel="stylesheet" href="/<?=$resource?>dist/css/AdminLTE.css">
  <link rel="stylesheet" href="/<?=$resource?>dist/css/skin-blue.css">
  <link rel="stylesheet" href="/<?=$resource?>plugins/iCheck/square/blue.css">
   <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition register-page" >
    <div class="text-center" style="height:100%;width:100%;">
           <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
             <a href="/">
                  <button type="submit" class="btn btn-primary btn-block btn-flat"> Back </button>
           </a>     
        </div>
        <!-- /.col -->
      </div>
            <a href="https://play.google.com/store/apps/details?id=com.ionicframework.musa853179" target="_black">
                  <img src="/<?=$resource?>dist/img/musawo.jpg" alt="musawo app" style="max-width:100%;"/>
          </a>
     <!-- /.form-box -->
  </div>
<!-- /.register-box -->

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
