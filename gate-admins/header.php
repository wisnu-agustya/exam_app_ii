<?php
include "../cfg/general.php";
include "../control/inc_function.php";
include "../control/inc_function2.php";
// include "/view_pic/4_pic_report_export.php";
connectdb();
if (!cekAdminLogin()){
	header('location:index.php');
}
if (isset($_POST['cmd4'])) {
switch ($_POST['cmd4']) {
		case 'Update':
			$name = $_POST['name'];
			$address = $_POST['address'];
			$phone = $_POST['phone'];
			$email = $_POST['email'];
			$dob = $_POST['dob'];
			$pob = $_POST['pob'];
			$pword = $_POST['pword'];
			$pword_c = $_POST['pword_check'];
			$uname = $_POST['username'];
			$id = $_POST['id'];
			$role = $_POST['role'];
			if ($role != 'role') {
				updateUser_1($id, $name, $dob, $pob, $address, $phone, $email, $role, $uname);
			} else {
				updateUser_2($id, $name, $dob, $pob, $address, $phone, $email, $uname);
			}
			if ($pword !="" OR $pword_c !="") {
				if ($pword == $pword_c) {
					setPass($id,$pword_c);
				}else{
					echo "<script>window.alert('Your password does not match, please try again.')</script>";
				}
			}
			break;
		default:
			die('mati');
			break;
	}
}
if ($_SESSION["admin_level"]=="Administrator"){
	$menu="admin_sm.php";
	$container="admin_container.php";
}elseif ($_SESSION["admin_level"]=="PIC"){
	$menu="pic_sm.php";
	$container="pic_container.php";
}elseif ($_SESSION["admin_level"]=="Exam Administrator"){
	$menu="exam_admin_sm.php";
	$container="exam_admin_container.php";
}elseif ($_SESSION["admin_level"] == "Student Register") {
	$menu = "register_sm.php";
	$container = "register_container.php";
}elseif ($_SESSION["admin_level"] == "Proctor") {
	$menu = "proctor_sm.php";
	$container = "proctor_container.php";
}elseif ($_SESSION["admin_level"] == "Program Manager") {
	$menu="admin_sm.php";
	$container="admin_container.php";
}elseif ($_SESSION["admin_level"] == "Admin Office") {
	$menu="admin_office_sm.php";
	$container="admin_office_container.php";
}elseif ($_SESSION["admin_level"] == "Marketing Manager") {
	$menu = "admin_marketing_sm.php";
	$container = "admin_marketing_container.php";
}elseif ($_SESSION["admin_level"] == "Support") {
	$menu = "admin_support_sm.php";
	$container = "admin_support_container.php";
}
else{
	print_r('403 Forbiden Access ');
	//$menu = "admin_support_sm.php";
	//$container = "admin_support_container.php";
}
?>
<html>
	<head>
		<title><?php echo $_SESSION["admin_level"];?></title>
		<link rel="icon" href="../assets/img/icon.png" type="image/gif">
		<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
		<link href="../assets/css/font-awesome.min.css" rel="stylesheet">
		<link href="../assets/css/datepicker3.css" rel="stylesheet">
		<link href="../assets/css/styles.css" rel="stylesheet">
		<link href="../assets/css/jquery.dataTables.min.css" rel="stylesheet">
		<script type="text/javascript" src="../assets/js/function.js"></script>
		<script src='../assets/js/jquery-1.12.0.min.js'></script>
		<link href="../assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
		<link href="../assets/css/additional-style.css" rel="stylesheet" media="screen">	
	</head>
<body >
	<?php 
	autoFinish();
	createLog();
	autoFinishSess();
	reduceUploadRejecttmp();
	//  expired();
	//  die();
	?>
	<nav class="navbar navbar-custom navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse"><span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span></button>
				<a class="navbar-brand" href="#">Computer Based <span> Assesment</span></a>
				<ul class="nav navbar-top-links navbar-right">
					<li class="dropdown"><a data-toggle="dropdown" href="#" style="color: white;"><em class="fa fa-user-circle-o"></em><?php echo $_SESSION["admin_level"];?></a>
						<ul class="dropdown-menu">
							<li>
								<div class="all-button"><a href="#" data-id="<?=$_SESSION['admin_id']?>" data-toggle="modal" data-target="#edit_akun"><em class="fa fa-user"></em> Akun</a></div>
							</li>
							<li class="divider"></li>
							<li>
								<div class="all-button"><a href="logout.php"><em class="fa fa-sign-out"></em> Logout</a></div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div><!-- /.container-fluid -->
	</nav>

	<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
		<div class="profile-sidebar">
			<div class="profile-userpic" >
			<?php
				//print_r($_SESSION['admin_group']);
					$image = getLogo($_SESSION['admin_group']);
					$logo = $image[0];
					$name = $image[1];
				?>
				<img src="../assets/img/logo/<?=$logo?>" class="img-responsive" alt="" style="
    /*float: left;*/
    margin:auto;
    /*max-width: 70px;*/
    max-height: 70px;
">			
			</div>
			<div class="profile-usertitle" style="float: none; margin:0px ">
				<div class="profile-usertitle-name" style="font-size: 18px; text-align: center;"><?=$name?></div>
			</div>
			<div class="clear"></div>
		</div>
		<hr style=" margin-top: 0px;margin-bottom: 0px;   border: 0;   height: 1px;    background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgb(48, 165, 255), rgba(0, 0, 0, 0));">
		<div class="divider"></div>
		<!-- Side Menu Include -->
<?php include $menu;?>
	</div><!--/.sidebar-->
