<!DOCTYPE html>
<html>
<?php
include "cfg/general.php";
include "control/inc_function.php";
include "control/inc_function2.php";
connectdb();
// if (studentLoginCek()){
//     header('location:halaman_ujian.php');
// }
if (studentLoginCek()){
    header('location:exam/view_exam.php');
}
$user_id="";
$password="";
$loginSukses=0;
$js="";
if (isset($_POST["user_id"])){
  $user_id=$_POST["user_id"];
  $password=$_POST["password"];
    $t=examStudentLogin($user_id,$password);
    if ($t==1){
        $sql = "SELECT * FROM `exam_participants` WHERE `id_student` =$id_participant AND `exam_group`= $exam_group";
        $rs=mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
        if (mysqli_fetch_row($rs) == 0) {
          examParticipants($id_participant,$exam_group);
        }
        $n=cekLogStu($user_id,$password);
        if (cekSessionFront($_SESSION["id_peserta"],$_SESSION["exam_group"])) {
          header('location:exam/view_exam.php');
        }else if ($n == 1) {
          header('location:exam/view_exam.php');
        }else {
          cancelLogin($_SESSION['id_peserta'],$_SESSION['exam_group']);
          echo ("<script LANGUAGE='JavaScript'>
              window.alert('Sorry, you can\'t sign in, because the allocation of exam participants is full.');
              window.location.href='logout.php';
              </script>");
        }
    }elseif ($t==2){
      $loginSukses=0;
      $js="alert(\"You still have an active session!\\r\\n Please contact administrator!\");\r\n";
    }else{
      $loginSukses=0;
      $js="alert(\"Invalid Identification Id/Exam Token!\");\r\n";
    }
}
?>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/datepicker3.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="icon" href="assets/img/icon.png" type="image/gif">
  <style>

* {
    box-sizing: border-box;
}

/* Create two equal columns that floats next to each other */
.column-left {
  float: left;
    margin-left: 5%;
    width: 65%; 
    padding: 10px;
    height: 300px; /* Should be removed. Only for demonstration */

}
.column-right {
  background-color: #f9f9f9;
    float: left;
    width: 28%;
    padding: 10px;
    height: 250px; 
    border-radius: 10px; /* Should be removed. Only for demonstration */

}
.btn-primary{
  background-color: #001992;
  border-color: #001992;
}
.btn-primary:hover{
  background-color: #062eef;
  border-color: #062eef;
}

  </style>
</head>
<body style="background-image:url('assets/img/background1.jpg'); background-size: 100%;">
<div class="row" style="width: 100%;">
<div class="col-sm-12" style="margin-top: 35px; margin-bottom: 95px; padding: 15px;" >
  <div class="column-left" >
    <h1 style="color: #FFF;margin: 25px 0px 0px 0px;text-shadow: -2px -2px 3px #000;"><b>DON'T WAIT FOR</b></h1>
    <h1 style="color: #FFF;margin: 0px;font-size: 45px;text-shadow: -2px -2px 3px #000;"><b>OPPORTUNITY.</b></h1>
    <h1 style="color: #ffe003;margin:0px;font-size: 62px;text-shadow: -2px -2px 3px black;"><b>CREATE IT.</b></h1>
  </div>
  <div class="column-right">
    <h2 align=center style="color: #001992;text-shadow: 0px 0px 3px white;margin-bottom: 5px;"><b> Student Login</b></h2>

  <br> <form role="form" id="flogin" name="flogin" method="post" action="">
            <fieldset>
              <div class="form-group">
                <input type="text" class="form-control" name="user_id" id="user_id" placeholder="Identification">
              </div>
              <div class="form-group">
                <input type="password" name="password" id="password"  class="form-control" placeholder="Exam Token">
              </div>      
              <br> 
                <!-- Change this to a button or input when using this as a form -->
              <input type="submit" class="btn btn-primary btn-block" id="btlogin" name="btlogin" value="LOGIN">
            </fieldset>
          </form>
  </div>
</div>
</div>
<div style="background-color: #000; height: 75px; position: fixed; width: 100%;bottom:0">
  <div class="row">
    <div class="col-sm-4"><img src="assets/img/logo_trust.png" style="max-height: 50px;padding:5px;margin-top: 10px;margin-left: 25px"></div>
    <div class="col-sm-4"></div>
    <div class="col-sm-4" style="text-align: right;" ><img src="assets/img/ms_logo.png" style="padding: 5px;padding-right: 25px;max-height: 78px;"></div>
  </div>
</div>
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script language="javascript">
  <?php echo $js;?>
</script>
</body>
</html>
