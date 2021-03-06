<?php
include "../cfg/general.php";
include "../control/inc_function.php";
include "../control/inc_function2.php";
include "view_exam_cmd.php";
echo '
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
';
connectdb();
if (!isset($_SESSION["exam_group"])) {
  echo ("<script LANGUAGE='JavaScript'>
        window.alert('Plaese Login Again !\\n(Exam Group Not exist)');
        window.location.href='../';
        </script>");
}

//cek session ujian runing (durasi ujian)
$remainingtime=getRemainingTime();
if ($remainingtime<=0) {
  $time_menit=0;
  $time_detik=0;
} else {
  $time_menit=intVal($remainingtime/60);
  $time_detik=$remainingtime % 60;
}
$sql = "SELECT a.idstudents, a.fname, b.logo from students a inner join customer b on SUBSTRING_INDEX(a.our_id,'.','1') = b.id_customer where a.idstudents = '".$_SESSION['user_id']."' ";
$rs=mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
$result = mysqli_fetch_row($rs);
$logo=$result[2];
//update jawaban ujian
if (isset($_POST['no'])) {
  $act=$_POST['act'];
  switch ($act) {
     case 'SetAnswer':
      $no = $_POST['no'];
      $nomorSoal = $no;
      $id_soal = $_POST['id_soal'];
      updateRunQuest($_POST['answer'],$id_soal,'False');
      // echo "<script>window.location.href = window.location.href;</script>";
      break;
    case 'Prev':
      $no = $_POST['no'];
      $nomorSoal = $no-1;
      $id_soal = $_POST['id_soal'];
      if ($_POST['answer']!=null) {
        updateRunQuest($_POST['answer'],$id_soal,$_POST['mark']);
      } else if ($_POST['mark']=='true') {
        updateUndecided($id_soal,'True');
      }
      echo "<script>window.location.href = '?id=".$nomorSoal."';</script>"; 
      break;
    case 'Next':
      $no = $_POST['no'];
      $nomorSoal = $no + 1;
      $id_soal = $_POST['id_soal'];
      if ($_POST['answer'] != null) {
        updateRunQuest($_POST['answer'], $id_soal, $_POST['mark']);
      } else if ($_POST['mark'] == 'true') {
        updateUndecided($id_soal, 'True');
      }
      echo "<script>window.location.href = '?id=".$nomorSoal."';</script>";
      break;
    case 'Review':
      $no = $_POST['no'];
      $nomorSoal = $no ;
      $id_soal = $_POST['id_soal'];
      if ($_POST['answer'] != null) {
        updateRunQuest($_POST['answer'], $id_soal, $_POST['mark']);
      }
     
      echo "<script>window.location.href = 'review.php';</script>";
      break;
    case 'Mark':
      $no = $_POST['no'];
      $nomorSoal = $no;
      $id_soal = $_POST['id_soal'];
      updateUndecided($id_soal, 'True');
      echo "<script>window.location.href = '?id=".$nomorSoal."';</script>";
      break;
    case 'Unmark':
      $no = $_POST['no'];
      $nomorSoal = $no;
      $id_soal = $_POST['id_soal'];
      if ($_POST['answer'] != null) {
        updateUndecided($id_soal, 'False');
      } else {
        updateUndecided($id_soal, (null));
      }
      echo "<script>window.location.href = '?id=".$nomorSoal."';</script>";
      break;
    default:
      $no = $_POST['no'];
      $id_soal = $_POST['id_soal'];
      //print_r($no.'='.$id_soal.'='.$_POST['mark']);
      if ($_POST['answer'] != null) {
        updateRunQuest($_POST['answer'], $id_soal, $_POST['mark']);
      } else if ($_POST['mark'] == 'true') {
        updateUndecided($id_soal, 'True');
      }
      $nomorSoal = $no;
      echo "<script>window.location.href = '?id=".$nomorSoal."';</script>"; 
      break;
  }
} else {
  $nomorSoal = 1;
}
//----------------------------------------END EXAM--------------------------------------------------
if (isset($_POST['cmd'])) {
  switch ($_POST['cmd']) {
    case 'end':
      //jika user control memperbolehkan lihat hasil maka eksekusi jika tidak kasih keterangan
      $data = showResultExam($_POST['student_id'], $_POST['exam_group']);
      $idses = $_SESSION['cust_group'];
      $views = cekViewResult($idses);
      $view = mysqli_fetch_array($views);;
      if ($view[2] == 'true') {
        echo ($data);
      } else {
        $v = viewResultNone();
        echo ($v);
      }
      endSession($_POST['student_id'], $_POST['exam_group']);
      die();
      break;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <title>Exam</title>
  <!-- Bootstrap Core CSS -->
    <!-- <link href="../assets/css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- Custom CSS -->
    <link href="../assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="../assets/css/exam-style.css" rel="stylesheet">
    <link rel="icon" href="../assets/img/icon.png" type="image/gif" sizes="16x16">
   <style type="text/css">
     .col-md-2, .col-md-10{
        padding-right: 5px;
        padding-left: 5px;
      }
      .custom-heading{
        height: 25px;
        color: #fff;
        text-shadow: 1px 1px #000; 
        padding: 1px 10px;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        background-image: url(../assets/img/heading-panel.png);
        background-size: cover;
        border-bottom: 3px solid #ffbc00;
      }
      .btn-danger {
        background-color: #d6251f;
        border : 1px solid #fff; 
      }
      .btn-danger:hover {
        background-color: #e4130c;
        border : 1px solid #fff; 
      }
      .btn-warning {
        background-color: #ffbc00;
      }
      .btn-success {
        background-color: #00b842;
      }
   </style>
    <!-- Custom JS -->
  <script src="../assets/js/jquery-1.11.1.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
  <script>
    var intCountDown = <?php echo $remainingtime; ?>;
    function countDown(){
      if(intCountDown < 0){ 
         window.alert('Times Up');
         window.location.href='end_exam.php';
        cntdwn.innerText = 'Done';
        return;
      } seconds_left  = intCountDown;
        
            seconds  = Math.floor(seconds_left / 1) % 60;
            minutes  = Math.floor(seconds_left / 60);
            
        setElement('countdown-minutes', minutes);
            setElement('countdown-seconds', seconds);
      cntdwn.innerText = intCountDown--;
      setTimeout("countDown()",1000);
    }

     function setElement(id, value) {
            if (value.length < 2) {
                value = "0" + value;
            }
          window.document.getElementById(id).innerHTML = value;
        }
</script>
</head>
<body>
  <div id="wrapper">
  <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0; background-image: url(../assets/img/header.png); background-size: cover; position: fixed; width: 100% ">
      <div class="col-lg-1" style="    text-align: center;    padding: 5px;">
          <img style="max-height:70px;" src="../assets/img/logo/<?= $result[2] ?>">
        </div>
        <div class="col-lg-4">
          <h4 style="color: #fff;text-shadow: 1px 1px #000;">ID Student : <?= $result[0] ?></h4>
          <h4 style="color: #fff;text-shadow: 1px 1px #000;">Name : <?= $result[1] ?></h4>
        </div>  
        <div class="col-lg-2" style="text-align: center;">
          <!--<img style="max-height:65px;padding-top: 15px;" src="../assets/img/trust-150X38.png">-->
          <img style="max-height:65px;padding-top: 15px;" src="../assets/img/logo_trust.png">
        </div>
        <div class="col-lg-1">
          <body onload="countDown()">
        <div id="cntdwn" style="visibility: hidden;" ></div>
      </body>
        </div>
        <div class="col-lg-4" style="text-align: right; margin-bottom:10px ">
          <h4 style="color: #fff;text-shadow: 1px 1px #000;">
            Remaining Time : 
            <b <?php if ($time_menit < 10) {
                echo 'style="color:#d9534f"';
              } ?> ><span id="countdown-minutes"></span></b> Minutes 
            <b <?php if ($time_menit < 10) {
                echo 'style="color:#d9534f"';
              } ?> ><span id="countdown-seconds"></span></b> Second
          </h4>
          <button class = "btn btn-sm btn-danger" value="Review" onclick="formSubmitReview()">Review Exam</button>
        </div>
        <div class="navbar-header">
    </div>
        <!-- /.navbar-header -->
      <div class="nav navbar-top-links navbar-right"></div>
    </nav>
    <div id="page-wrapper">
     <div> 
      <div class="row">
       <?php
      if ($remainingtime <= 0) {
        die('Waktu Sudah Habis');
      }

      if (isset($_GET['id'])) {
        $nomorSoal=$_GET['id'];
      }else{
        $nomorSoal=1;
      }
      viewSoal($_SESSION["id_peserta"], $_SESSION["exam_group"], $nomorSoal);
      ?>
      </div>
     </div>
    </div>
<!-- Footer -->
<!--     <div style="background-image: url(../assets/img/heading-panel.png);background-size: cover;border-top: 2px solid #ffbc00; text-align: right;padding: 10px;">
      <img src="../assets/img/trust-150X38.png" style="text-align: right; max-width: 150px">
      
    </div>
  </div> -->

<!-- Modal -->
  <div class="modal fade" id="end_exam" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="
            background-color: #d9534f;
            border-radius: 5px 5px 0px 0px;
            color : #fff;
        ">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Info</h4>
        </div>
        <div class="modal-body">
          <p>Anda Memilih untuk mengakhiri ujian ini <br>
             Setelah anda mengakhiri ujian <br>
             anda tidak dapat kembali ke halaman ujian ini dan jawaban anda akan tersimpan</p>
             <br><br>
            <p>Anda Yakin Mengakhiri Ujian?</p>
          <div style="text-align: center;">
            <form action="" method="POST">
              <input type="hidden" name="cmd" value="end">
              <input type="hidden" name="student_id" value="<?= $_SESSION['id_peserta'] ?>">
              <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
              <input type="hidden" name="exam_group" value="<?= $_SESSION['exam_group'] ?>">
              <button class="btn btn-sm btn-success"> Ya </button>
              <button class="btn btn-sm btn-danger" data-dismiss="modal"> Tidak</button>
            </form>
          </div>
       </div>
      </div>
    </div>
  </div>
</body>
</html>
 <script>
// Add active class to the current button (highlight it)
var header = document.getElementById("opsi_answer");
var btns = header.getElementsByClassName("btn");
for (var i = 0; i < btns.length; i++) {
  btns[i].addEventListener("click", function() {
    var current = document.getElementsByClassName("active");
    current[0].className = current[0].className.replace(" active", "");
    this.className += " active";
  });
}
</script>
