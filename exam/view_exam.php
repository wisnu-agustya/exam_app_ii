<?php
include "../cfg/general.php";
include "../control/inc_function.php";
include "../control/inc_function2.php";
include "view_exam_cmd.php";
connectdb();
$cmd="cekKodeUjian"; //default
if (!isset($_POST["cmd"])){
	//called from login,, collect student data
	if (!cekStudentLogin()){
		header('location:../log.php');
	}
	$user_id=$_SESSION["user_id"];
	$id_peserta=$_SESSION["id_peserta"];
	$user_name=$_SESSION["user_name"];
	$user_group=$_SESSION["user_group"];
	$exam_group=$_SESSION["exam_group"];
}else{
	$cmd=$_POST["cmd"];
	$user_id=$_POST["user_id"];
	$id_peserta=$_POST["id_peserta"];
	//echo "peserta".$id_peserta;
	$user_name=$_POST["user_name"];
}
$js="";
?>
<html>
<body >
  <head>
  	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/img/icon.png" type="image/gif" sizes="16x16">
	<style>

* {
    box-sizing: border-box;
}

/* Create two equal columns that floats next to each other */
.column-left {
  float: left;
    margin-left: 5%;
    width: 60%;
    padding: 10px;
    height: 445px; /* Should be removed. Only for demonstration */

}
.column-right {
  background-color: #31708f;
    float: left;
    width: 28%;
    padding: 10px;
    margin-top: 10px;
    height: 445px; 
    border-radius: 10px; /* Should be removed. Only for demonstration */
}
.notice {
	color: #000;
    text-shadow: 0px 0px 6px #fff;
    background-color: #fff;
    border-radius: 10px;
    font-size: 12px;
    padding: 5px 15px;
}


/* Clear floats after the columns */
	table {
		background: #fff;
		border-radius:5px;
		margin-left:auto; 
        margin-right:auto; 
        width: 90%;
		font-weight:600;
      }
    td {
		padding-left:5px;
          height: 25px;
      }
	</style>
  </head>
<body >
	
<?php
	switch ($cmd){
		case "cekKodeUjian":
			$kodeUjian=getKodeUjian(); //kode ujian sesuai token
			if ($kodeUjian==""){
				//belum punya kode ujian
				echo ("<script LANGUAGE='JavaScript'>
				    window.alert('Please Login Again');
				    window.location.href='../logout.php';
				    </script>");
				die('Error Kode Ujian');
			}else{
				$remainingtime=0; //value will be updated inside getStatusUjian() function
				//cek session ujian
				if (cekSession()) {
					header("location:exam.php");
					//print_r('Redirect to exam');
				}else {
					
					if (cekOpportunity()) {
						if ($res=cekSessionExist()) {
							cancelLogin($_SESSION['id_peserta'], $_SESSION['exam_group']);
							$n = cekPermitCust();
							$idses = $_SESSION['cust_group'];
							$views = cekViewResult($idses);
							$view = mysqli_fetch_array($views);;
							if ($view[2] == 'true') {
								echo "<h3 style='text-align:center;color:red;'>Mohon Maaf Anda Sudah mengikuti ujian</h3>";
								viewResultExam($res);
							} else {
								$v = viewResultNone();
								echo ($v);
							}
							die();
						} else{
							$rs=mysqli_query($GLOBALS['link'],"SELECT id_schedule from exam_schedule where exam_group=".$_SESSION['exam_group']);
							$row=mysqli_fetch_row($rs);
							if (cekTimeSchedule($row[0])) {
							}else{
								cancelLogin($_SESSION['id_peserta'],$_SESSION['exam_group']);
								echo ("<script LANGUAGE='JavaScript'>
										window.alert('Login time is Limit \\nPlease Contact Administrator');
										window.location.href='../logout.php';
										</script>");
								die('Error Time Ujian');
							}
							echo ("<script LANGUAGE='JavaScript'>
											window.alert('Login Success');
											</script>");
							echo "<div style=\"background-image:url('../assets/img/class.jpg'); background-size:100%;height:560px;\">";
							startPage();
							echo "</div>";
							die();
						}
					} else{
						$res=cekSessionExist();
						cancelLogin($_SESSION['id_peserta'], $_SESSION['exam_group']);
						$n = cekPermitCust();
						$idses = $_SESSION['cust_group'];
						$views = cekViewResult($idses);
						$view = mysqli_fetch_array($views);;
						if ($view[2] == 'true') {
							echo "<h3 style='text-align:center;color:red;'>Mohon Maaf Anda Sudah mengikuti ujian</h3>";
							viewResultExam($res);
						} else {
							$v = viewResultNone();
							echo ($v);
						}
					}
				}
			}
			break;
		case "start":
			echo "<body style='text-align:center;background: #7e8683;'>
			<br>
			<br>
			<h2>Please Wait ..... </h2>
				<img style='margin-top:7%;margin-bottom:3%;width:50px;' src='../assets/img/loading.gif'>
			<h2>Generate Exam</h2>
				  </body>
				 ";
			//	 die();
			$participant_id=$_POST['participant_id'];
			$exam_group=$_POST['exam_group'];
			$program_id=$_POST['program_id'];
			$voucher_id=$_POST['voucher_id'];
			clearQuestion($exam_group,$participant_id);
			generateQuestion($program_id,$exam_group,$participant_id);
			sessionUjian($exam_group,$participant_id,$program_id);
			$ex=explode('.',$participant_id);
			//jika ujian pertama maka voucher akan berkurang
			if ($ex[1]==1) {
			 	useVoucher($voucher_id,$exam_group,$participant_id);
			}
			//show soal
			header("location:exam.php");
			die('redirect halaman mengerjakan ujian');
			break;
		case 'cancel':
			$participant_id=$_POST['participant_id'];
			$exam_group=$_POST['exam_group'];
			cancelLogin($participant_id,$exam_group);
			header("location:../logout.php");
			break;
	}
?>
</body>
