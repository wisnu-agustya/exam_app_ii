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
    header('location:exam/view_exam.php');
  }elseif ($t==2){
    $loginSukses=0;
    $js="alert(\"You still have an active session!\\r\\n Please contact administrator!\");\r\n";
  }else{
    $loginSukses=0;
    $js="alert(\"Invalid User Id/Password!\");\r\n";
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
</head>
<body>
  <div class="row">
    <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
      <div class="login-panel panel panel-default">
        <div class="panel-heading">Log in
        </div>
        <div class="panel-body">
          <form role="form" id="flogin" name="flogin" method="post" action="">
            <fieldset>
              <div class="form-group">
                <input type="text" class="form-control" name="user_id" id="user_id" placeholder="Username">
              </div>
              <div class="form-group">
                <input type="password" name="password" id="password"  class="form-control" placeholder="Password">
              </div>       
                <!-- Change this to a button or input when using this as a form -->
              <input type="submit" class="btn btn-lg btn-primary btn-block" id="btlogin" name="btlogin" value="LOGIN">
            </fieldset>
          </form>
        </div>
      </div>
    </div><!-- /.col-->
  </div><!-- /.row -->    
<script src="assets/js/jquery-1.11.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script language="javascript">
  <?php echo $js;?>
</script>
</body>
</html>
