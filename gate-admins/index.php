<?php
include "../cfg/general.php";
include "../control/inc_function.php";
connectdb();
if (cekAdminLogin()){
    header('location:dashboard.php');
}
$user_id="";
$password="";
$loginSukses=0;
$js="";
if (isset($_POST["user_id"])){
    $user_id=$_POST["user_id"];
    $password=$_POST["password"];
    if (adminLogin($user_id,$password)){
        autoFinish();
        header('location:dashboard.php');
    }else{
        $loginSukses=0;
        $js="alert(\"Invalid User Id/Password!\");\r\n";
    }
}
?>
<link rel="icon" href="../assets/img/icon.png" type="image/gif">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../assets/css/login-style.css">
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<div class="container login-container">
  <div class="row">
    <div class="col-md-8 login-form-1" style='padding-bottom:3.1%'>
	  <div style='background: #000000cc;height: 110px;margin-top: 32%; border-top:2px solid #fff;border-bottom:2px solid #fff;'>
		<div class='row'>
		<div class='col-md-6' style='padding-top:3%;padding-left:30px'>
		<img src='../assets/img/logo_trust.png'style='max-height:55px'>
		</div>
		<div class='col-md-6' style='text-align:right;padding-top:2%;padding-right:30px'>
		<img src='../assets/img/Logo_ms.png' style='max-height:75px'>
		</div>
		</div>
	  </div>
        
    </div>
    <div class="col-md-4 login-form-2" style='padding-bottom:12%; border-left:2px solid #fff;'>
	  <h3>Login</h3>
        <form id="flogin" name="flogin" method="post" action="">
          <div class="form-group">
			<input type="text" class="form-control" name="user_id" id="user_id" placeholder="Username *" maxlength='25'>
          </div>
          <div class="form-group">
            <input type="password" name="password" id="password"  class="form-control" placeholder="Password *" maxlength='25'>
          </div>
          <div class="form-group">
			<input type="submit" class="btnSubmit" id="btlogin" name="btlogin" value="Login">
          </div>
        </form>
    </div>
  </div>
</div>