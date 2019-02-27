<?php

function connectdb(){
	$host     = $GLOBALS["db_host"];
	$username = $GLOBALS["db_user"];
	$password = $GLOBALS["db_password"];
	$db_name  = $GLOBALS["db_name"];
	$GLOBALS['link'] = mysqli_connect($host,$username,$password,$db_name);
    if(!$GLOBALS['link']) {
        echo "alert('Unable to connect to database network.')";
        // die();
    }
    //Select database
    $db = mysqli_select_db($GLOBALS['link'],$db_name);
    if(!$db) {
        die("Unable to select database");
  }
}

// ================================================================================================================
// Login Admin
function encrypt($object,$salt){
	$newsalt = md5($salt);
	$res = sha1($object.$newsalt);
	return $res;
}
function adminLogin($user_id,$password){
	$sql="SELECT a.user_auth,a.level_auth,b.fname,b.cust_group,a.pword FROM auth a INNER JOIN users b ON a.user_auth=b.id_user 
	WHERE a.level_auth IN ('Administrator','PIC','Exam Administrator','Student Register','Proctor','Program Manager','Admin Office','Marketing Manager','Support')
		and a.uname='".sqlValue($user_id)."'";
				//echo $sql;
	$rs=mysqli_query($GLOBALS['link'],$sql);
	while ($row=mysqli_fetch_row($rs)) {
		$pass = encrypt($password,$row[0]);
		if ($row[4]==$pass) {
			$_SESSION["admin_id"]=$row[0];
			$_SESSION["admin_level"]=$row[1];
			$_SESSION["admin_name"]=$row[2];
			$_SESSION["admin_group"]=$row[3];
			$_SESSION["admin_login"]=1;
			return true;
		}
	}
	return false;
}

function cekAdminLogin(){
	if (isset($_SESSION["admin_level"])){
		if (($_SESSION["admin_level"]=="Administrator") or 
			($_SESSION["admin_level"]=="PIC") or 
			($_SESSION["admin_level"]=="Exam Administrator") or
			($_SESSION["admin_level"]=="Student Register") or
			($_SESSION["admin_level"]=="Proctor")or
			($_SESSION["admin_level"]=="Program Manager")or
			($_SESSION["admin_level"]=="Admin Office")or
			($_SESSION["admin_level"]=="Marketing Manager")or
			($_SESSION["admin_level"]=="Support")
			)
		{
			if ($_SESSION["admin_login"]==1) {
				return true;
			} else {
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}
// ================================================================================================================
// Login Students
function examParticipants($id_student,$exam_group){
	$sql = "INSERT INTO `exam_participants`(`exam_group`, `id_student`) VALUES ($exam_group,'".sqlValue($id_student)."')";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
}

// function cekTimeSchedule($id_schedule){
// 	$sql="SELECT count(*) from exam_schedule 
// 	WHERE DATE = CURDATE() 
// 	AND start_time+ INTERVAL 15 minute >= CURRENT_TIME() 
// 	AND CURRENT_TIME() + INTERVAL 15 minute>=start_time 
// 	AND id_schedule = $id_schedule";
// 		//die($sql);
// 	$rs=mysqli_query($GLOBALS['link'],$sql);
// 	$row=mysqli_fetch_row($rs);
// 	if ($row[0]>0) {
// 		return true;
// 	} else {
// 		return false;
// 	}
// }
function cekTimeSchedule($id_schedule){
	$sql="SELECT start_time from exam_schedule 
	WHERE DATE = CURDATE()
	AND id_schedule = $id_schedule";
		//die($sql);
	$rs=mysqli_query($GLOBALS['link'],$sql);
	$today = timeVar();
	$row=mysqli_fetch_row($rs);
	$start = strtotime("+15 minutes", strtotime($row[0]));
	$end = strtotime("+15 minutes", strtotime($today[1]));
	$startTime = date('H:i:s', $start);
	$endTime = date('H:i:s', $end);
	if ($startTime>=$today[1] and $endTime >= $row[0]) {
		return true;
	} else {
		return false;
	}
}

function cekLogStu($user_id,$password){
	$sql = "SELECT a.id_schedule,a.exam_group,a.token, b.count_stu 
	FROM exam_schedule a INNER JOIN exam_group b ON a.exam_group = b.exam_code
	WHERE a.`status`='run' AND a.token='$password';";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	$dat = mysqli_fetch_array($res);
	$idg = $dat[1];
	$alo = $dat[3];
	$qc = "SELECT COUNT(a.id_student) n FROM exam_participants a INNER JOIN exam_group b ON a.exam_group=b.exam_code 
	WHERE exam_group = '$idg'";
	$equ_student =  mysqli_query($GLOBALS['link'],$qc) or die(mysqli_error($GLOBALS['link']));
	$r = mysqli_fetch_array($equ_student) ;
	$equst = $r[0];
	if ($equst<=$alo) {
		$par_log = 1;
	}else {
		$par_log = 0;
	}
	return $par_log;
}

// ================================================================================================================

// ================================================================================================================
// Cek PIC
function cekPIC($id){
	$sql = "SELECT a.user_auth,b.fname FROM auth a INNER JOIN users b ON a.user_auth=b.id_user WHERE a.level_auth = 'PIC'  AND b.cust_group='$id'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	return $res;
}
// ================================================================================================================
// cek view result customer
function cekViewResult($id){
	$sql = "SELECT a.* FROM customer_set a WHERE a.id_customer='$id'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	return $res;
}
// ================================================================================================================
// Add New Customer
function addCustomer($name,$address,$phone,$email,$logo,$result){
	$carikode = mysqli_query($GLOBALS['link'], "SELECT MAX(id_customer) FROM customer") or die (mysqli_error($GLOBALS['link']));
		// menjadikannya array
		$datakode = mysqli_fetch_array($carikode);
		// jika $datakode
if ($datakode) {
    $nilaikode = substr($datakode[0], 1);
    // menjadikan $nilaikode ( int )
    $kode = (int) $nilaikode;
    // setiap $kode di tambah 1
    $kode = $kode + 1;
    $new_id = "C".str_pad($kode, 4, "0", STR_PAD_LEFT);
} else {
    $new_id = "C0001";
}
	$sql="INSERT INTO `customer`(id_customer,`cust_name`, `address`, `phone_off`, `email_off`, `logo`) VALUES ('$new_id','".sqlValue($name)."','".sqlValue($address)."','".sqlValue($phone)."','".sqlValue($email)."','".sqlValue($logo)."')";
	mysqli_query($GLOBALS['link'],$sql) or die($sql);
		$sql_setting = "INSERT IGNORE INTO `customer_set`(id_customer,`set_view_result`) VALUES ('$new_id','$result')";
		$res_setting = mysqli_query($GLOBALS['link'], $sql_setting) or die($sql_setting);
}
function showCustomer(){
	$res = mysqli_query($GLOBALS['link'],"SELECT * FROM customer");
	return $res;
}
// ================================================================================================================
// PIC voucher
function picVoucher($idg){
	$sql = "SELECT a.id_voucher,c.program_name,b.available_val
	FROM transact_voucher a  
	INNER JOIN tmp_voucher b ON a.id_voucher=b.id_voucher
	INNER JOIN programs c ON a.id_program=c.id_program 
	WHERE a.id_customer='$idg'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	$dat = array();
	while ($row = mysqli_fetch_array($res)){
		$dat[] = $row;
	}
	return $dat;
}

function  viewVoucher(){
	$id = $_SESSION['admin_group'];
	$sql = "SELECT a.id_voucher,c.program_name, a.type_voucher,b.available_val 
	FROM transact_voucher a INNER JOIN tmp_voucher b ON a.id_voucher=b.id_voucher 
	LEFT JOIN programs c ON a.id_program=c.id_program WHERE a.id_customer='$id'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	$dat = array();
	while ($row=mysqli_fetch_array($res)) {
		$dat[] = $row; 
	}
	return $dat;
}

function  vouPre(){
	$id = $_SESSION['admin_group'];
	$sql = "SELECT SUM(b.available_val) jum FROM transact_voucher a INNER JOIN tmp_voucher b ON a.id_voucher=b.id_voucher 
	LEFT JOIN programs c ON a.id_program=c.id_program 
	WHERE a.id_customer='$id' AND a.type_voucher ='Prepaid'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	$row = mysqli_fetch_array($res);
	if ($row[0] == true) {
		return "Prepaid : ".$row[0] ;
	}else {
		return "Pretpaid : -";
	}
}

function  vouPost(){
	$id = $_SESSION['admin_group'];
	$sql = "SELECT SUM(b.available_val) jum FROM transact_voucher a INNER JOIN tmp_voucher b ON a.id_voucher=b.id_voucher 
	LEFT JOIN programs c ON a.id_program=c.id_program 
	WHERE a.id_customer='$id' AND a.type_voucher ='Postpaid'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	$row = mysqli_fetch_array($res);
	if ($row[0] == true) {
		return "Postpaid : ".$row[0] ;
	}else {
		return "Postpaid : -";
	}
}


// ================================================================================================================
// PIC User
function addPIC($id,$name,$address,$phone,$email){
	$id_user = $id.'.1';	
	$sql="INSERT INTO `users`(`id_user`, `fname`, `phone`, `email`, `alamat`, `cust_group`, `privilege`) VALUES  ('$id_user','".sqlValue($name)."','$phone','".sqlValue($email)."','".sqlValue($address)."','".sqlValue($id)."','PIC')";
	$insert_user = mysqli_query($GLOBALS['link'],$sql) or die('Error : '.mysqli_error($GLOBALS['link']).'<br>'.$sql);
	if ($insert_user) {
		$pword = generatePassword();
		$pass = encrypt($pword,$id_user);
		$sql1="INSERT INTO `auth`( `user_auth`, `uname`, `pword`, `level_auth`) VALUES  ('$id_user','$id_user','".sqlValue($pass)."','PIC')";
		mysqli_query($GLOBALS['link'],$sql1) or die('Error : '.mysqli_error($GLOBALS['link']).'<br>'.$sql1);
	}
	$result['id_user'] = $id_user;
	$result['pass'] = $pword;
	return $result;
}

function editPIC($id){
	$sql ="SELECT fname,alamat,phone,email FROM users where id_user = '".sqlValue($id)."'";
	$res = mysqli_query($GLOBALS['link'],$sql);
	return $res;
}

function updatePIC ($id,$name,$address,$phone,$email){
	$sql="UPDATE `users` SET `fname`='".sqlValue($name)."', `phone`='".sqlValue($phone)."', `email`='".sqlValue($email)."', `alamat`='".sqlValue($address)."' WHERE id_user='".sqlValue($id)."'";
	mysqli_query($GLOBALS['link'],$sql) or die('Error : '.mysqli_error($GLOBALS['link']).'<br>'.$sql);
}
// ================================================================================================================
// Add New Users
function showUsers($id_g){
	$res = mysqli_query($GLOBALS['link'], "SELECT a.id_user,a.fname,a.phone,a.email,b.level_auth FROM users a INNER JOIN auth b ON a.id_user=b.user_auth WHERE a.cust_group = '$id_g'") or die('ssss');
	return $res;
}

function newIdUser($id_g){
	$qry = mysqli_query($GLOBALS['link'],"SELECT MAX(CONVERT(SUBSTRING(a.id_user,7),UNSIGNED INTEGER)) AS n FROM db_exam.users a WHERE a.cust_group= '$id_g'");
	$find = mysqli_fetch_assoc($qry);
	$no = $find['n'];
	$no_id = (int) $no; 
	$id = $no_id + 1;
	$new_id = $id_g.'.'.$id ;
	return $new_id;
}

function addUsers($name, $address, $phone, $email, $dob, $pob, $pword, $pword_c,$id_g,$new_id,$role,$uname){
	$pass = encrypt($pword, $new_id);
	$sql_1 = "INSERT INTO auth(user_auth,uname,pword,level_auth) VALUES('$new_id','$uname','$pass','$role')";
	$res_1 = mysqli_query($GLOBALS['link'], $sql_1) or die($sql_1 .'<br>' . mysqli_error($GLOBALS['link']));
	if ($res_1 == true) {
		$sql = "INSERT INTO users(`id_user`,`fname`,`tempat_lahir`,`tanggal_lahir`,`phone`,`email`,`alamat`,`cust_group`) 
		VALUES('$new_id','" . sqlValue($name) . "','$pob','$dob','$phone','$email','$address','$id_g')";
		$res = mysqli_query($GLOBALS['link'], $sql) or die($sql . '<br>' . mysqli_error($GLOBALS['link']));
		$js = "<script>alert('The user has been successfully added.');</script>";
	}else {
		$js = "<script>alert('Insert is not successful, because the username was already used.');</script>";
	}
	if ($js == true) {
		return $js;
	}
	mysqli_close($sql);
	mysqli_close($sql_1);
}
// ================================================================================================================
// Edit User
function editUser($id){
	$sql = "SELECT a.id_user,a.fname,a.tempat_lahir,a.tanggal_lahir,a.alamat,a.phone,a.email,b.uname,b.level_auth
	FROM users a INNER JOIN auth b ON a.id_user=b.user_auth
	WHERE a.id_user='$id'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	return $res;
}
function updateUser_1($id, $name, $dob, $pob, $address, $phone, $email, $role, $uname){
	$sql_1 = "UPDATE users SET fname='$name', tempat_lahir='$pob', tanggal_lahir='$dob', alamat='$address',
		phone='$phone', email='$email' WHERE id_user='$id'";
		$res_1 = mysqli_query($GLOBALS['link'], $sql_1);
	$sql_2 = "UPDATE auth SET uname='$uname', level_auth='$role' WHERE user_auth='$id'";
		$res_2 = mysqli_query($GLOBALS['link'], $sql_2);
}
function updateUser_2($id, $name, $dob, $pob, $address, $phone, $email, $uname){
	$sql = "UPDATE users SET fname='$name', tempat_lahir='$pob', tanggal_lahir='$dob', alamat='$address',
		phone='$phone', email='$email' WHERE id_user='$id'";
		$res = mysqli_query($GLOBALS['link'], $sql);
	$sql_2 = "UPDATE auth SET uname='$uname' WHERE user_auth='$id'";
		$res_2 = mysqli_query($GLOBALS['link'], $sql_2);
}
function setPass($id, $pword_c){
	$pass = encrypt($pword_c,$id);
	$sql = "UPDATE auth SET pword = '$pass'  WHERE user_auth = '$id' ";
	$res = mysqli_query($GLOBALS['link'], $sql);
}
// ================================================================================================================
// Delete User
function delUser($id){
	$sql_1 = "DELETE FROM users WHERE id_user='$id'";
	$res_1 = mysqli_query($GLOBALS['link'], $sql_1);
	$sql_2 = "DELETE FROM auth WHERE user_auth='$id'";
	$res_2 = mysqli_query($GLOBALS['link'], $sql_2);
}
// ================================================================================================================
// Table Student
function viewEquExm($id){
	if (isset($id)) {
		$sql = "SELECT MAX(SUBSTRING_INDEX(b.id_student,'.','-1')) X FROM 
		(SELECT a.id_student FROM exam_percentage a 
		WHERE SUBSTRING_INDEX(a.id_student,'.','1') = '$id' GROUP BY a.id_student) b";
		$res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']).$sql.'errno');
		$dat = mysqli_fetch_array($res);
		return $dat[0];
	// return $sql;
	}
}

function showStudents($id_g, $valin){
	$id = $_SESSION['admin_group'];
	$sql = "SELECT a.* FROM (SELECT aa.idstudents,aa.fname,aa.email 
	FROM students aa WHERE SUBSTRING_INDEX(aa.our_id,'.',1)='$id') a 
	WHERE a.idstudents LIKE '%$valin%' OR a.fname LIKE '%$valin%'";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn');
	$dat = array();
	while ($row = mysqli_fetch_array($res)) {
		$dat[] = $row;
	}
	return $dat;
}

function showAllStudents($id_g)
{
	$id = $_SESSION['admin_group'];
	$sql = "SELECT a.* FROM (SELECT aa.idstudents,aa.fname,aa.email 
	FROM students aa WHERE SUBSTRING_INDEX(aa.our_id,'.',1)='$id') a";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn');
	$dat = array();
	while ($row = mysqli_fetch_array($res)) {
		$dat[] = $row;
	}
	return $dat;
}

function countStudent(){
	$id = $_SESSION['admin_group'];
	$sql = "SELECT COUNT(SUBSTRING_INDEX(a.our_id,'.',1)) cn FROM students a WHERE SUBSTRING_INDEX(a.our_id,'.',1) = '$id'";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn'); ;
	$dat = mysqli_fetch_array($res);
	return $dat[0];
}

function datStudent($ids){
	$sql = "SELECT a.idstudents,a.fname,a.email FROM students a WHERE a.idstudents = '$ids'" ;
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn');
	$row = mysqli_fetch_array($res);
	return $row;
}

function datlastExam($ids){
	$sql = "SELECT a.id_session,a.start_time,SUBSTRING_INDEX(a.id_student,'.','-1') cnt FROM exam_session a 
	WHERE a.id_session = (SELECT MAX(aa.id_session) FROM exam_session aa 
	WHERE SUBSTRING_INDEX(aa.id_student,'.','1') = $ids)";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn');
	$row = mysqli_fetch_array($res);
	return $row; 
}

function recordExamStu($ids,$id_prog){
	$sql = "SELECT a.id_student,c.program_name,d.date,d.start_time,b.exam_code,c.id_program,e.notif
	FROM exam_session a INNER JOIN 
		(SELECT aa.exam_code,SUBSTRING_INDEX(SUBSTRING_INDEX(aa.group_name,'.','2'),'.','-1') id_program 
		FROM exam_group aa) b ON a.exam_code = b.exam_code
	LEFT JOIN programs c ON b.id_program = c.id_program
	LEFT JOIN exam_schedule d ON d.exam_group = b.exam_code 
	INNER JOIN user_test e on a.id_student = e.id_peserta 
	WHERE SUBSTRING_INDEX(a.id_student,'.','1') = $ids 
	AND c.id_program = '$id_prog' AND a.exam_code=e.exam_code  ";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn');
	$dat = array();
	while ($row = mysqli_fetch_array($res)) {
		$dat[] = $row;
	}
	return $dat;
}

function recExamStu($ids){
	$sql = "SELECT a.id_student,c.program_name,d.date,d.start_time,b.exam_code
	FROM exam_session a INNER JOIN 
		(SELECT aa.exam_code,SUBSTRING_INDEX(SUBSTRING_INDEX(aa.group_name,'.','2'),'.','-1') id_program 
		FROM exam_group aa) b ON a.exam_code = b.exam_code
	LEFT JOIN programs c ON b.id_program = c.id_program
	LEFT JOIN exam_schedule d ON d.exam_group = b.exam_code 
	WHERE SUBSTRING_INDEX(a.id_student,'.','1') = $ids";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn');
	$dat = array();
	while ($row = mysqli_fetch_array($res)) {
		$dat[] = $row;
	}
	return $dat;
}

function concResult($group,$val){
	$sql = "SELECT a.exam_code, c.pass_grade
	FROM exam_group a JOIN transact_voucher b ON a.id_voucher = b.id_voucher
	JOIN programs c ON b.id_program = c.id_program
	WHERE a.exam_code = $group";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn');
	$row = mysqli_fetch_array($res);
	$equ = $row[1];
	if ($val >= $equ){
    $grad = "(L)";
  }else{
    $grad = "(<font color=\"red\">TL</font>)";
	}
	return $grad;
}

function resultExam($id,$idg){
	$sql = "SELECT  a.id, b.subject_name, a.percentage_true, a.percentage_false, a.percentage_null,b.id_subject FROM exam_percentage a LEFT JOIN subject_ls b ON a.id_subject=b.id_subject WHERE SUBSTRING_INDEX(a.id_student,'.',1)= '$id' AND a.exam_code='$idg'";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('errn');
	$dat = array();
	while ($row = mysqli_fetch_array($res)) {
		$dat[] = $row;
	}
	return $dat;
}

// ================================================================================================================
// Add Student
function fileSetup(){
	$ga = $_SESSION['admin_group'];
	$errmsg = "";
	$file1 = $_FILES["file1"]["name"];
	$outfilename = "";
	if ($file1 != ""){
		$temp = explode(".", $file1);
		for ($ofn = 1; $ofn < count($temp); $ofn++) {
			$outfilename .= $ga.".".$temp[$ofn - 1].".";
		}
		//$outfilename.=gmdate('YmdHis') . "." . end($temp);
		$outfilename .= date('YmdHis') . "." . end($temp);
	}
	if ($outfilename != "") {
		$isuploaded = 0;
		$apppath = gotoAppPath();
		//$destdir = $apppath . $GLOBALS["tmp-dir"];
		$destdir = "../" . $GLOBALS["tmp-dir"];
		if (!is_dir($destdir)) {
			$d = mkdir($destdir);
			if (!$d) {
				$errmsg .= "Upload Failed! Unable to create directory!";
			}
		}
		if ($errmsg == "") {
			$filetmp = $_FILES["file1"]["tmp_name"];
			$server_path = $destdir . $outfilename;
			$s = move_uploaded_file($filetmp, $server_path);
			if ($s) {
				$isuploaded = 1;
			} else {
				$errmsg .= "<script>alert('Upload Failed.');</script>";
			}
		}
	} else {
    $errmsg .= "<script>alert('File not selected!');</script>";
	}
	if ($errmsg != "") {
		echo $errmsg;
		return "";
	} else {
		return $outfilename;
	}
}

function import_excel_students($filename){
	$filename_path="../".$GLOBALS["tmp-dir"].$filename;
	$ext = strtolower(array_pop(explode(".", $filename)));
	if ($ext=="xls"){
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}else{
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	}
	$objPHPExcel = $objReader->load($filename_path);
	$objWorksheet = $objPHPExcel->getActiveSheet();
	$into_fields= "file_tmp,no,idstudent,fname,email";
	$rows = $objWorksheet->getHighestRow();
  for ($row=2;$row<=$rows;$row++){
		$data="'".$_SESSION["import_id"]."'";
  	for ($col=0; $col < 4; $col++){
  		$data.=",'";
  		if ($col==0){
  			//nim, tidak boleh spasi
				$idstudent =$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
				$idstudent =str_replace(" ","", $idstudent);
  			$data.=sqlValue($idstudent);
  		}else{
  			$data.=sqlValue($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
  		}
  		$data.="'";
  	}
		$sql="INSERT INTO students_up_tmp(".$into_fields.") values (".$data.")";
  	mysqli_query($GLOBALS['link'],$sql); //semua data upload insert tb students_up_tmp dahulu
	}
  //filter out duplicate
  $sql = "INSERT INTO students_duplicate_tmp SELECT aa.* FROM students_up_tmp aa 
		WHERE aa.file_tmp='{$_SESSION["import_id"]}' GROUP BY aa.idstudent HAVING COUNT(*) > 1";
		mysqli_query($GLOBALS['link'], $sql); // cek duplicate data upload dan jika ada input ke data duplicate
	
  $sql = "DELETE FROM students_up_tmp WHERE idstudent IN (SELECT idstudent FROM students_duplicate_tmp 
		WHERE file_tmp='{$_SESSION["import_id"]}')";
		mysqli_query($GLOBALS['link'], $sql); // delete data dupilcate di table students_up_tmp

	$sql = "DELETE FROM students_duplicate_tmp WHERE `no` ='0' AND file_tmp='{$_SESSION["import_id"]}'";
		mysqli_query($GLOBALS['link'], $sql); //delete duplicate data jika data mempunyai nilai kosong

	$sql = "DELETE FROM students_reject_tmp WHERE idstudent IN (SELECT idstudent FROM students_up_tmp)";
		mysqli_query($GLOBALS['link'], $sql); // delete data students_reject_tmp jika ada data sama dengan table tudents_up_tmp
		
	$sql = "INSERT INTO students_reject_tmp SELECT a.* FROM students_up_tmp a INNER JOIN students b ON a.idstudent=b.idstudents 
		WHERE SUBSTRING_INDEX(b.our_id,'.',1) ='{$_SESSION["admin_group"]}'";
		mysqli_query($GLOBALS['link'], $sql); // insert ke students_reject_tmp jika data siswa sudah ada di table students

	$sql = "DELETE FROM students_up_tmp WHERE idstudent IN (SELECT idstudent FROM students_reject_tmp)";
		mysqli_query($GLOBALS['link'], $sql); // delete data students_up_tmp jika data sama dengan table tudents_reject_tmp
		mysqli_close($GLOBALS['link']); 
	}

function reduceUploadRejecttmp(){
	$id = $_SESSION['admin_group'];
	$date = date('Ymd');
	$sql = "DELETE FROM students_reject_tmp WHERE SUBSTRING_INDEX(file_tmp,'.',1) = '$id' 
		AND SUBSTRING((SUBSTRING_INDEX(SUBSTRING_INDEX(file_tmp,'.',-2),'.',1)),1,8) < '$date'";
	$sql_1 = "DELETE FROM students_duplicate_tmp WHERE SUBSTRING_INDEX(file_tmp,'.',1) = '$id' 
		AND SUBSTRING((SUBSTRING_INDEX(SUBSTRING_INDEX(file_tmp,'.',-2),'.',1)),1,8) < '$date'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	$res_1 = mysqli_query($GLOBALS['link'], $sql_1);
}

// ================================================================================================================
// Saving Student
function saveStudents($filename){
	$date = date('dmy');
	$time = date('h');
	$datetime = date('Y-m-d h:m:i');
	$q_b = mysqli_query($GLOBALS['link'], "SELECT MAX(CONVERT(SUBSTRING(a.batch_id,12),UNSIGNED INTEGER)) FROM students a");
	if ($dat_b = mysqli_num_rows($q_b) != 0) {
		$dat_b = mysqli_fetch_array($q_b);
		$sqe_b = $dat_b[0];
	}
	$id_b = $date . '.' . $time . '.T';
	$q_o = mysqli_query($GLOBALS['link'], "SELECT MAX(CONVERT(SUBSTRING(a.our_id,15),UNSIGNED INTEGER)) FROM students a");
	if ($dat_o = mysqli_num_rows($q_o) != 0) {
		$dat_o = mysqli_fetch_array($q_o);
		$sqe_o = $dat_o[0];
	}
	$id_o = $_SESSION['admin_group'] . '.' . $date . '.T';
	$sql = "SELECT a.idstudent,a.fname,a.email FROM students_up_tmp a WHERE a.file_tmp='$filename'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	while ($row = mysqli_fetch_array($res)) {
		$sql_in = "INSERT INTO students(idstudents, fname, email, batch_id, our_id, date) VALUES('{$row[0]}','{$row[1]}', '{$row[2]}','$id_b$sqe_b', '$id_o$sqe_b', '$datetime')";
		$r = mysqli_query($GLOBALS['link'], $sql_in);
		$sqe_b++;
		$sqe_o++;
	}
	mysqli_close($GLOBALS['link']);
}
function savePointStudent($id,$name,$email){
	$date = date('dmy');
	$time = date('h');
	$datetime = date('Y-m-d h:m:i');
	$q_b = mysqli_query($GLOBALS['link'], "SELECT MAX(CONVERT(SUBSTRING(a.batch_id,12),UNSIGNED INTEGER)) FROM students a");
	if ($dat_b = mysqli_num_rows($q_b) != 0) {
		$dat_b = mysqli_fetch_array($q_b);
		$sqe_b = $dat_b[0]+1;
	}
	$id_b = $date . '.' . $time . '.T'.$sqe_b;
	$q_o = mysqli_query($GLOBALS['link'], "SELECT MAX(CONVERT(SUBSTRING(a.our_id,15),UNSIGNED INTEGER)) FROM students a");
	if ($dat_o = mysqli_num_rows($q_o) != 0) {
		$dat_o = mysqli_fetch_array($q_o);
		$sqe_o = $dat_o[0]+1;
	}
	$id_o = $_SESSION['admin_group'].'.'.$date.'.T'.$sqe_o;
	$sql ="INSERT INTO students(idstudents, fname, email, batch_id, our_id, date) VALUES('$id','$name','$email','$id_b','$id_o','$datetime')";
	$res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
	if ($res === true) {
		return $id;
	}else{
		return false;
	}
}

// resulting add students
function resPointStudent($id){
	$sql = "SELECT a.idstudents,a.fname,a.email FROM students a WHERE a.idstudents = '$id'";
	$res = mysqli_query($GLOBALS['link'], $sql) or die('error '.$sql);
	return $res;
}
function resViewStudent($session_file){
	$res = mysqli_query($GLOBALS['link'], "SELECT a.idstudent,a.fname,a.email FROM students_up_tmp a  where file_tmp = '$session_file' ") or die('err');
	return $res;
}
function resViewStudentReject($session_file){
	$res = mysqli_query($GLOBALS['link'], "SELECT a.idstudent,a.fname,a.email FROM students_reject_tmp a where file_tmp = '$session_file' ") or die('err');
	return $res;
}
function resViewStudentDupli($session_file){
	$res = mysqli_query($GLOBALS['link'], "SELECT a.idstudent,a.fname,a.email FROM students_duplicate_tmp a  where file_tmp = '$session_file' ") or die('err');
	return $res;
}
function resViewStudentRemidial($session_file){
	$id = $_SESSION['admin_group'];
	$res = mysqli_query($GLOBALS['link'], "SELECT a.file_tmp,a.idstudent,a.fname,a.email,b.our_id,b.submited
	FROM students_reject_tmp a INNER JOIN students_remidial b ON a.idstudent=b.nim WHERE SUBSTRING_INDEX(b.our_id,'.',1) = '$id'") or die('err');
	return $res;
}
function updateStudentRemidi($arr,$arrs){
	foreach ($arrs as $id) {
		// echo($id);
		if (in_array($id,$arr)) {
			// echo('found');
			$sql = "UPDATE `students_remidial` SET `submited` = 'Y' WHERE `nim` = '$id'";
			$res = mysqli_query($GLOBALS['link'], $sql);
		}else {
			// echo('not found');
			$sql = "UPDATE `students_remidial` SET `submited` = 'N' WHERE `nim` = '$id'";
			$res = mysqli_query($GLOBALS['link'], $sql);
		}
	}
	// die();
	// }
}
function resRemidial($session_file){
	$rej = resViewStudentRemidial($session_file);
	if ($row = mysqli_num_rows($rej) != 0) {
		$par_1 = 1;
		$no = 1;
		$table = '<br><h4>Students Remidial</h4>';
		$table .=
			'<table><thead><tr>
				<th scope = "col" > CK </th>
        <th scope = "col" > No </th>
        <th scope = "col" > NIM </th>
        <th scope = "col" > Name </th>
        <th scope = "col" > Email </th>
        <th scope = "col" > Status </th>
				</tr></thead><tbody>';
		$table .= '<form action="" method="POST">'; 
		while ($row = mysqli_fetch_array($rej)) {
			$table .= '<tr><td>
			<div class=\"checkbox\"><label>';
			$table .= '<input  type="checkbox" id="voucher" name="id_cek[]" value="'.$row[1].'"';
			if ($row[5]=='Y') {
				$table .='checked>';
			}else {
				$table .='>';
			}
			$table .= '<input  type="hidden" id="voucher" name="id_all[]" value="' . $row[1] . '">'; 
			$table .='</label>
			</div>
			</td><td>' . $no . '</td><td>' . $row[1] . '</td><td>' . $row[2] . '</td><td>' . $row[3] . '</td>';
			if ($row[5]=='Y') {
				$table .='<td><b>Sudah Diajukan</b></td></tr>';
			}else {
				$table .='<td><b>Belum Diajukan</b></td></tr>';
			}
			$no++;
		}
		$table .= '</tbody></table>';
		$table .= '<br><button class="btn btn-warning btn-xs" name="cmd" value="submit_remidi">Submit Remidi</button>';
		$table .= '</form>';
	}
	return $table;
}

function resPoint($id){
	$res = resPointStudent($id);
	if ($row = mysqli_num_rows($res) != 0) {
		$par_0 = 1 ; 
		$no = 1;
		$table = '<br><h4>Student succes to upload</h4>' ;
		$table .=
			'<table><thead><tr>
        <th scope = "col" > No </th>
        <th scope = "col" > NIM </th>
        <th scope = "col" > Name </th>
        <th scope = "col" > Email </th>
				</tr></thead><tbody>';
		while ($row = mysqli_fetch_array($res)) {
			$table .= '<tr><td>' . $no . '</td><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>'. $row[2] .'</td></tr>';
			$no++;
		}
	$table .= '</tbody></table>';
	}
	return $table;
}
function resStudent($session_file){
	$res = resViewStudent($session_file);
	if ($row = mysqli_num_rows($res) != 0) {
		$par_0 = 1 ; 
		$no = 1;
		$table = '<br><h4>Student success to upload</h4>' ;
		$table .=
			'<table><thead><tr>
        <th scope = "col" > No </th>
        <th scope = "col" > NIM </th>
        <th scope = "col" > Name </th>
        <th scope = "col" > Email </th>
				</tr></thead><tbody>';
		while ($row = mysqli_fetch_array($res)) {
			$table .= '<tr><td>' . $no . '</td><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>'. $row[2] .'</td></tr>';
			$no++;
		}
	$table .= '</tbody></table>';
	}
	return $table;
}
function resStudentRej($session_file){
	$res = resViewStudentReject($session_file);
	if ($row = mysqli_num_rows($res) != 0) {
		$par_1 = 1;
		$no = 1;
		$table = '<br><h4>Students already exist</h4>';
		$table .=
			'<table ><thead><tr>
        <th scope = "col" > No </th>
        <th scope = "col" > NIM </th>
        <th scope = "col" > Name </th>
        <th scope = "col" > Email </th>
				</tr></thead><tbody>';
		while ($row = mysqli_fetch_array($res)) {
			$table .= '<tr><td>' . $no . '</td><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' . $row[2] . '</td></tr>';
			$no++;
		}
		$table .= '</tbody></table>';
	}
	return $table;
}
function resStudentDup($session_file){
	$res = resViewStudentDupli($session_file);
	if ($row = mysqli_num_rows($res) != 0) {
		$par_2 = 1 ;
		$no = 1;
		$table = '<br><h4>Students duplicate data input</h4>';
		$table .=
			'<table ><thead><tr>
        <th scope = "col" > No </th>
        <th scope = "col" > NIM </th>
        <th scope = "col" > Name </th>
        <th scope = "col" > Email </th>
				</tr></thead><tbody>';
		while ($row = mysqli_fetch_array($res)) {
			$table .= '<tr><td>' . $no . '</td><td>' . $row[0] . '</td><td>' . $row[1] . '</td><td>' . $row[2] . '</td></tr>';
			$no++;
		}
		$table .= '</tbody></table>';
	}
	return $table;
}
// ================================================================================================================
// reset form upload
function resetImport(){
	$cmd = mysqli_query($GLOBALS['link'], "TRUNCATE students_up_tmp") or die('err');
	$cmd = mysqli_query($GLOBALS['link'], "TRUNCATE students_reject_tmp") or die('err');
	$cmd = mysqli_query($GLOBALS['link'], "TRUNCATE students_duplicate_tmp") or die('err');
	// die(); 
}
// ================================================================================================================
// Create Program
function addProgram($name,$subject,$margin,$tot,$duration,$passgrade){
	$sql= "SELECT max(id_program) from programs";
	$carikode = mysqli_query($GLOBALS['link'],$sql) or die (mysqli_error($GLOBALS['link']));
		// menjadikannya array
		$datakode = mysqli_fetch_array($carikode);
		// jika $datakode
	if ($datakode) {
	    $nilaikode = substr($datakode[0], 1);
	    // menjadikan $nilaikode ( int )
	    $kode = (int) $nilaikode;
	    // setiap $kode di tambah 1
	    $kode = $kode + 1;
	    $new_id = "P".str_pad($kode, 4, "0", STR_PAD_LEFT);
	} else {
	    $new_id = "P0001";
	}
	$sql="INSERT INTO `programs`(`id_program`, `program_name`,sum_question,margin,duration,pass_grade) VALUES ('$new_id','".sqlValue($name)."','$tot','".sqlValue($margin)."',$duration,$passgrade)";
	mysqli_query($GLOBALS['link'],$sql) or die($sql);
	foreach ($subject as $key => $value) {
		if ($value['percent']>0) {
			$sql_1="INSERT INTO `program_detail`(`id_program`, `id_subject`, `percent`) VALUES  
			('$new_id','".sqlValue($value['subject_id'])."','".sqlValue($value['percent'])."')";
			mysqli_query($GLOBALS['link'],$sql_1) or die($sql);
		}
	}
}
function deleteProgram($id){
	$sql = "DELETE FROM programs where id_program = '$id'";
	$res = mysqli_query($GLOBALS['link'],$sql);
	$sql = "DELETE FROM program_detail where id_program = '$id'";
	$res = mysqli_query($GLOBALS['link'],$sql);
}
function editProgram($id){
	$sql = "SELECT a.id_program,a.program_name,a.margin,b.id_subject,b.percent,b.id,a.sum_question,a.duration
	from programs a join program_detail b on a.id_program = b.id_program where a.id_program = '$id'";
	$res = mysqli_query($GLOBALS['link'],$sql);
	return $res;
}
function showAllProgram(){
	$id = $_SESSION['admin_group'];
	$sql = "SELECT * FROM programs ";
	$res = mysqli_query($GLOBALS['link'],$sql);
	return $res;
}

function showProgram(){
	$id = $_SESSION['admin_group'];
	$sql = "SELECT a.id_program, b.program_name FROM transact_voucher a INNER JOIN programs b ON a.id_program=b.id_program 
	WHERE a.id_customer='$id' GROUP BY a.id_program";
	die($sql);
	$res = mysqli_query($GLOBALS['link'],$sql);
	return $res;
}

function updateProgram($id,$name,$subject,$margin,$percent,$no_id,$tot,$duration){
	$sql = "UPDATE `programs` SET `program_name`='".sqlValue($name)."',`margin`=$margin, duration=$duration, sum_question=$tot WHERE `id_program`='$id'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die('Error');
	foreach ($subject as $key => $value) {
		if ($percent[$key]>0) {
			if ($no_id[$key]==0) {
					$sql_1="INSERT INTO `program_detail`(`id_program`, `id_subject`, `percent`) VALUES  
				('$id','".sqlValue($value)."','".sqlValue($percent[$key])."')";
				mysqli_query($GLOBALS['link'],$sql_1) or die($sql);
			} else {
				$sql_1="UPDATE `program_detail` SET `id_subject`='".sqlValue($value)."',`percent`='".sqlValue($percent[$key])."' WHERE `id`= '$no_id[$key]'";
				mysqli_query($GLOBALS['link'],$sql_1) or die($sql);
			}
		}
	}
}
// ================================================================================================================
// Classroom Manager
function showClass(){
	$id_cust = $_SESSION['admin_group'];
	$sql = "SELECT a.* FROM exam_class a WHERE a.id_customer = '$id_cust'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	return $res;
}
function showClassSchedule($classroom){
	$sql = "SELECT a.date,a.start_time,a.`status`,a.id_schedule 
	from exam_schedule a where a.classroom= '$classroom' and a.`status`!='finish' AND a.date >= CURRENT_DATE()";
	$res = mysqli_query($GLOBALS['link'], $sql);
	return $res;
}

function tableClass(){
	$res = showClass();
	$no = 1;
	$table .=
		'<table class="table table-xs" id="tbClass"><thead><tr>
			<th scope = "col" > No </th>
			<th scope = "col" > Class Names</th>
			<th scope = "col" > Active Schedules</th>
			<th scope = "col" > More</th>
		</tr></thead><tbody>';
	while ($row = mysqli_fetch_array($res)) {
		$table .= '<tr><td>' . $no . '</td><td>' . $row[2] . '</td><td>';
		$classSche = showClassSchedule($row[0]);
		while ($result =  mysqli_fetch_row($classSche)) {
			$strtime = $result[0].' '.$result[1];
			if ($result[2]=='run') {
				$table .=
				'<a href=?pg=pic_sch_detail&CC='.$result[3].' style="text-decoration: none;">
					<div class="btn-group btn-group-xs">
						<button type="button " class="btn btn-primary"><i class="fa fa-play" aria-hidden="true"></i> RUN</button>
						<button type="button " class="btn btn-default">'. date('l, H:m, d/M',strtotime($strtime)).'</button>
					</div>
				</a>&nbsp;';
			} elseif ($result[2]=='init') {
				$table .= 
				'<div class="btn-group btn-group-xs">
					<button type="button " class="btn btn-default"><i class="fa fa-clock-o" aria-hidden="true"></i> INIT</button>
					<button type="button " class="btn btn-default">'.date('l, H:m, d/M',strtotime($strtime)).'</button>
				</div>&nbsp;';
			} else{
				$table .= 
				'<a href=?pg=pic_sch_detail&CC='.$result[3].' style="text-decoration: none;">
					<div class="btn-group btn-group-xs">
						<button type="button " class="btn btn-warning"><i class="fa fa-window-close" aria-hidden="true"></i> CLOSED</button>
						<button type="button " class="btn btn-default">'.date('l, H:m, d/M',strtotime($strtime)).'</button>
					</div>
				</button></a>&nbsp;';
			}
		}
		// <button type="button" class="btn btn-outline btn-success btn-xs">Status : '.$result[2].'<br>Sch:  '.$result[0].', '.$result[1].'</button>
		$table .= '</td><td>';
		$table .= '<button type="button" class="btn btn-xs btn-info"  data-id="'.$row[0].'" data-toggle="modal" data-target="#editclass">Edit</button>';
		$table .= '</td></tr>';
		$no++;
	}
	$table .= '</tbody></table>';
	return $table;
}

function genIdClass($id_g){
	$sql = "SELECT MAX(CONVERT(SUBSTRING(a.id_class,9),UNSIGNED INTEGER)) max FROM exam_class a";
	$res = mysqli_query($GLOBALS['link'], $sql);
	$dat = mysqli_fetch_array($res);
	$newdat = $dat[0]+1; 
	$id = 'CL'.$id_g.'.'.$newdat;
	return $id;
}

function createClass($id,$id_g,$class){
	$sql = "INSERT INTO `exam_class` (`id_class`, `id_customer`, `name_class`) 
	VALUES ('$id', '$id_g', '$class')" ;
	$res =  mysqli_query($GLOBALS['link'],$sql);
	mysqli_close($GLOBALS['link']);
}

function detClass($class){
	$sql = "SELECT name_class FROM exam_class WHERE id_class = '$class'" ;
	$res =  mysqli_query($GLOBALS['link'],$sql);
	$row = mysqli_fetch_array($res);
	return $row[0];
}

function editClass($class,$nameclass){
	$sql = "UPDATE exam_class SET name_class ='$nameclass' WHERE id_class = '$class'";
	mysqli_query($GLOBALS['link'],$sql);
	mysqli_close($GLOBALS['link']);
}
// ================================================================================================================
// Create Exam
function timeVar(){
  $timeNow = date('H:i:s');
  $dateNow = date('Y-m-d');
  return array($dateNow,$timeNow);
}

function genGroupName($id_v){
	$sql = "SELECT a.id_customer,a.id_program FROM transact_voucher a WHERE a.id_voucher = '$id_v'";
	$res = mysqli_query($GLOBALS['link'], $sql);
	if($dat = mysqli_fetch_array($res)){
		$idCust = $dat[0];
		$idProg = $dat[1];
		$qnm = "SELECT MAX(CONVERT(SUBSTRING(a.group_name,14),UNSIGNED INTEGER)) max FROM exam_group a";
		$resnm = mysqli_query($GLOBALS['link'], $qnm);
		$r = mysqli_fetch_array($resnm);
		$maxid = $r[0] + 1;
		$newName = $idCust.'.'.$idProg.'.G'.$maxid;
	}
	return $newName;
}

function showExamVoucher($id_v){
	$sql = "SELECT a.id_voucher,b.cust_name,c.program_name,d.available_val,a.type_voucher
	FROM transact_voucher a INNER JOIN customer b on a.id_customer=b.id_customer 
	INNER JOIN programs c on a.id_program=c.id_program
	INNER JOIN tmp_voucher d ON a.id_voucher=d.id_voucher WHERE a.id_voucher='".sqlValue($id_v)."'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	return $res;
}

function showProctor ($id){
	$sql = "SELECT a.id_user, a.fname from users a inner join auth b on a.id_user = b.user_auth 
		where  b.level_auth='Proctor' and a.cust_group ='".sqlValue($id)."'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	return $res;
}

function planVoucher($id_voucher,$quota){
	$sel = "SELECT a.available_val,a.plan_val FROM tmp_voucher a WHERE id_voucher ='$id_voucher'";
	$res = mysqli_query($GLOBALS['link'],$sel) or die(mysqli_error($GLOBALS['link']));
	if ($row = mysqli_fetch_array($res)) {
		$cnt = $row[1];
		$cnt_ava = $row[0];
	}else {
		$cnt = 0 ;
		$cnt_ava = $row[0];
	}
	$equ = $cnt + $quota;
	$equ_val = $cnt_ava - $quota ;
	$sql = "UPDATE tmp_voucher SET available_val = '$equ_val',plan_val = '$equ' WHERE id_voucher ='$id_voucher'";
	$result = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
}

function equSession($idg){
  $sql = "SELECT COUNT(a.id_student) cnt FROM exam_session a WHERE a.exam_code='$idg'";
  $res =  mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
  $row_ses = mysqli_fetch_array($res);
	$count = $row_ses[0];
  return $count;
}


function createLog(){
  list($dn,$tn) = timeVar();
  $sql = "SELECT a.date,a.start_time, a.id_schedule,a.exam_group,a.token,b.count_stu, b.id_voucher  
	FROM exam_schedule a INNER JOIN exam_group b ON a.exam_group = b.exam_code 
	WHERE a.status != 'finish'";
  $res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	$set = date('H:i:s' , strtotime($tn));
  while ($row = mysqli_fetch_array($res)) {
		$addtime = date('H:i:s',strtotime('+15 minutes',strtotime($row[1])));
    if ($addtime <= $set) {
			$ids = $row[2]; //id schedule
			$idg = $row[3]; //id group
			$stn = $row[5]; //jumlah student alocated
			$idv = $row[6]; //id voucher
			$n = equSession($idg); //menghitung peserta yang ikut start ujian
			$cst = $stn - $n; //sisa alocated
			$qin = "INSERT INTO `exam_group_log` (`id_group`, `use_val`, `unuse_val`) VALUES ('$idg','$n','$cst')";
			$run = mysqli_query($GLOBALS['link'],$qin);
			if ($run == true) {
				cntDeff($idv,$cst,$stn,$n);
				reducePlan($idv,$cst); //function untuk mereset alokasi voucher exam
			}
			// reducePlan($idv,$cst);
    }
	}
  // die();
}

function upVoucher($idv,$a){
	$sql = "UPDATE tmp_voucher SET available_val = '$a' WHERE id_voucher ='$idv'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
}

function reducePlan($idv,$cst){
	$sql = "SELECT a.plan_val FROM tmp_voucher a WHERE a.id_voucher = '$idv'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	$row = mysqli_fetch_array($res);
	$un = $row[0] - $cst; //harus 0
	$s = "UPDATE tmp_voucher SET plan_val = '$un' WHERE id_voucher ='$idv'";
	$r = mysqli_query($GLOBALS['link'],$s) or die(mysqli_error($GLOBALS['link']));
	return $r;
}

function cntDeff($idv,$cst,$stn,$n){
	$sql = "SELECT a.available_val FROM tmp_voucher a WHERE a.id_voucher = '$idv'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	$row = mysqli_fetch_array($res);
	$a = $cst + $row[0]; //jumlah sisa voucher + alocated sisa
	$r = upVoucher($idv,$a);
}

function showDuration($id_v){
	$sql = "SELECT b.duration,a.id_voucher FROM transact_voucher a INNER JOIN programs b ON a.id_program=b.id_program WHERE a.id_voucher='$id_v'";
	$res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
	$row = mysqli_fetch_array($res);
	return $row[0];
}
function editExamSchedule($id){
	$sql = "SELECT a.*, SUBSTRING_INDEX(SUBSTRING_INDEX(b.group_name,'.',2),'.',-1),b.count_stu,b.id_voucher,b.group_name from exam_schedule a INNER JOIN exam_group b ON a.exam_group=b.exam_code WHERE id_schedule = '$id'";
	$result = mysqli_query($GLOBALS['link'],$sql) or die (mysqli_error($GLOBALS['link']));
	return $result;
}
function createExamGroup ($id_voucher,$group_name,$quota){
	$sql = "SELECT max(exam_code) from exam_group";
	$carikode = mysqli_query($GLOBALS['link'],$sql) or die (mysqli_error($GLOBALS['link']));
		// menjadikannya array
		$datakode = mysqli_fetch_array($carikode);
		// jika $datakode
	if ($datakode) {
	    $new_id = $datakode[0]+1;
	} else {
	    $new_id = 1;
	}
	// $qup = "INSERT INTO ";
	$sql = "INSERT INTO `exam_group`(`exam_code`, `group_name`, `id_voucher`, `count_stu`) 
		VALUES ($new_id,'".sqlValue($group_name)."','".sqlValue($id_voucher)."',$quota)";
	$res['a'] = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	$res['b'] = $new_id;
	planVoucher($id_voucher,$quota);
	return $res;
}

function createSchedule($date,$time,$duration,$proctor,$exam_id,$class){
	$sql = "INSERT INTO `exam_schedule`( `exam_group`, `date`, `start_time`, `duration`, `proctor`, `status`,`classroom`) 
		VALUES ($exam_id,'".sqlValue($date)."','".sqlValue($time)."','".sqlValue($duration)."','".sqlValue($proctor)."','init','".sqlValue($class)."')";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}
function updateExamGroup ($id_voucher,$group_name,$quota,$old_alocated){
	$sql = "UPDATE exam_group SET count_stu=$quota where group_name = '".sqlValue($group_name)."'";
	$res['a'] = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	$res['b'] = $new_id;
	$quotaUpdate = $quota-$old_alocated;
	planVoucher($id_voucher,$quotaUpdate);
	return $res;
}

function updateSchedule($date,$time,$duration,$proctor,$exam_id,$class,$id_schedule){
	$sql = "UPDATE exam_schedule SET 
		`date` =  '".sqlValue($date)."',
		`start_time` = '".sqlValue($time)."',
		 `proctor` = '".sqlValue($proctor)."',
		 `classroom` = '".sqlValue($class)."'
		 WHERE id_schedule = ' $id_schedule'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

// editvoucher
function editExamVoucher($id_g){
	$sql = "SELECT a.id_voucher,b.cust_name,c.program_name,d.available_val,c.duration,a.type_voucher
	FROM transact_voucher a INNER JOIN customer b ON a.id_customer=b.id_customer 
	INNER JOIN programs c ON a.id_program=c.id_program
	INNER JOIN tmp_voucher d ON a.id_voucher=d.id_voucher where b.id_customer='".sqlValue($id_g)."'";
	$res = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']));
	return $res;
}

// Voucher
function useVoucher($voucher_id,$exam_code,$student_id){
	//show quota
	$qwr = "SELECT b.plan_val,b.available_val FROM transact_voucher a INNER JOIN tmp_voucher b ON a.id_voucher=b.id_voucher 
		WHERE a.id_voucher ='".$voucher_id."'";
	$rsqwr=mysqli_query($GLOBALS['link'],$qwr) or die(mysqli_error($GLOBALS['link']));
	$row=mysqli_fetch_row($rsqwr);
	$new_quota = $row[0] - 1;
	$saldo = $row[1] + $new_quota;
	$sql = "INSERT INTO `voucher_history`(`id_voucher`, `exam_code`, `status`, `date`, `user_id`,saldo) 
		VALUES ('".$voucher_id."', $exam_code,'Usage', ".sqlFormatDate(date("m/d/Y H:i")).",'".$student_id."',$saldo)";
	$rs=mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error('Voucher Error : '.$GLOBALS['link']));
	//update quota voucher
	$sql = "UPDATE tmp_voucher SET plan_val=$new_quota WHERE `id_voucher`= '$voucher_id'";
	mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
}
// ================================================================================================================
// Exam Schedule
function showExam($id,$where){
	if (isset($where)) { 
		$sql = "SELECT a.id_schedule,a.date,a.start_time,c.fname, a.token,a.status, b.count_stu ,a.exam_group
		FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
		INNER JOIN users c on a.proctor=c.id_user where c.cust_group ='".sqlValue($id)."' $where ORDER BY a.id_schedule DESC";	
	}else{
		$sql = "SELECT a.id_schedule,a.date,a.start_time,c.fname, a.token,a.status, b.count_stu ,a.exam_group
		FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
		INNER JOIN users c on a.proctor=c.id_user where c.cust_group ='".sqlValue($id)."' ORDER BY a.id_schedule DESC";
	}
	//die($sql);
	$res = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']));
	return $res;
}

function showExamProctor($id,$where){
	$idp =	$_SESSION["admin_id"];
	if (isset($where)) {
		$sql = "SELECT a.id_schedule,a.date,a.start_time,c.fname, a.token,a.status, b.count_stu 
		FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
		INNER JOIN users c on a.proctor=c.id_user 
		WHERE c.cust_group ='".sqlValue($id)."' AND a.proctor = '$idp' $where  ORDER BY a.id_schedule DESC";
	}else{
		$sql = "SELECT a.id_schedule,a.date,a.start_time,c.fname, a.token,a.status, b.count_stu 
		FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
		INNER JOIN users c on a.proctor=c.id_user 
		WHERE c.cust_group ='".sqlValue($id)."' AND a.proctor = '$idp' ORDER BY a.id_schedule DESC";
	}
	$res = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']));
	return $res;
}

function finishExam($id){
	$sql = "SELECT a.id_schedule,a.date,a.start_time,c.fname, a.token,a.status, b.count_stu 
		FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
		INNER JOIN users c on a.proctor=c.id_user where c.cust_group ='".sqlValue($id)."' AND a.status='finish' ORDER BY a.id_schedule DESC";
	$res = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']));
	return $res;
}

function finishExamCount(){
	$id = $_SESSION['admin_group'];
	$sql = "SELECT Count(a.id_schedule) cnt
		FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
		INNER JOIN users c on a.proctor=c.id_user where c.cust_group ='".sqlValue($id)."' AND a.status='finish' ORDER BY a.id_schedule DESC";
	$res = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']));
	$dat = mysqli_fetch_array($res);
	return $dat[0];
}

function reportCount(){
$sql = "SELECT count(id_report) FROM customer_log_report a WHERE SUBSTRING_INDEX(a.id_report,'.',1) = '{$_SESSION['admin_group']}'";
	$res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
	$dat = mysqli_fetch_array($res);
	return $dat[0];
}

function showExamDash($id){
	$sql = "SELECT a.id_schedule,a.date,a.start_time,c.fname, a.token,a.status, b.count_stu 
		FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
		INNER JOIN users c on a.proctor=c.id_user where c.cust_group ='".sqlValue($id)."' AND a.status = 'run' ORDER BY a.id_schedule DESC";
	$res = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']));
	return $res;
}

function showExamDashProc($id){
	$idu =	$_SESSION["admin_id"];
	$sql = "SELECT a.id_schedule,a.date,a.start_time,c.fname, a.token,a.status, b.count_stu 
		FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
		INNER JOIN users c on a.proctor=c.id_user where c.cust_group ='".sqlValue($id)."' 
		AND a.proctor ='$idu' ORDER BY a.id_schedule DESC";
	$res = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']));
	return $res;
}

function generatePassword(){
	$kar="ABCDEFGHJKLMNPQRSTUVWZYZ23456789";
	$a="";
	for ($i=0; $i<6; $i++){
		$n=rand(0,strlen($kar)-1);
		$a.=$kar[$n];
	}
	return $a;
}

function updateExamToken($token,$id,$time){
	$sql = "UPDATE exam_schedule SET start_time='$time', status='run', token='".sqlValue($token)."' where id_schedule=$id";
	$res = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

function updateStatus($id){
	$sql = "UPDATE exam_schedule SET status='run' where id_schedule=$id";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

function autoFinish(){
	$sql = "UPDATE exam_schedule SET status = 'finish' WHERE status='run' AND date < CURDATE()";
	$res =  mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
}

function autoFinishSess(){
	$sql = "SELECT a.id_session,a.start_time,a.exam_code,SUBSTRING_INDEX(SUBSTRING_INDEX(b.group_name,'.',2),'.',-1) idpro, c.duration 
	FROM exam_session a LEFT JOIN exam_group b ON a.exam_code = b.exam_code 
	INNER JOIN programs c ON SUBSTRING_INDEX(SUBSTRING_INDEX(b.group_name,'.',2),'.',-1) = c.id_program 
	WHERE a.end_time IS NULL";
	$res =  mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	while ($dat = mysqli_fetch_array($res)) {
		$id_ses = $dat[0];
		$date = date('Y-m-d',strtotime($dat[1]));
		$curdate = date('Y-m-d');
		$cdexam = $dat[2];
		$cdpro = $dat[3];
		$duration = $dat[4];
		echo($id_ses);
		if ($curdate > $date) {
			$endtime = date('H:i:s',strtotime('+'.$duration.' minutes',strtotime($dat[1])));
			$blend = $date." ".$endtime ;
			$que = "UPDATE exam_session SET end_time = '$blend' WHERE id_session = '$id_ses'";
			$r = mysqli_query($GLOBALS['link'],$que) or die(mysqli_error($GLOBALS['link']).'<br>'.$que);
		} 
	}
}

function expired(){
	$id = $_SESSION["admin_group"];
	$sql = "SELECT a.date,a.start_time, a.id_schedule,a.exam_group,a.token,b.count_stu, b.id_voucher
	FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code INNER JOIN users c on a.proctor=c.id_user 
	WHERE a.status='init' AND c.cust_group ='$id' AND a.date <= CURRENT_DATE AND a.start_time <= CURRENT_TIME ORDER BY a.id_schedule DESC";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	while ($row = mysqli_fetch_array($res)) {
		$ids = $row[2];
		$idg = $row[3];
		$stn = $row[5];
		$idv = $row[6];
		$qin = "INSERT INTO `exam_group_log` (`id_group`, `use_val`, `unuse_val`) VALUES ('$idg','$n','$cst')";
		$run = mysqli_query($GLOBALS['link'],$qin);
	}
	if ($run == true) {
		cntDeff($idv,$cst,$stn,$n);
		reducePlan($idv,$cst); //function untuk mereset alokasi voucher exam
	}
	// echo($sql);
	// die();
}

function showStueExam($id){
	$sql = "SELECT SUBSTRING_INDEX(b.id_student,'.','1') n, b.start_time, b.end_time FROM exam_schedule a 
	INNER JOIN exam_session b ON a.exam_group=b.exam_code WHERE a.exam_group='$id'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$table .=
	'<table class="table table-xs" id="tbStudents"><thead><tr>
		<th scope = "col" > No </th>
		<th scope = "col" > Students ID </th>
		<th scope = "col" > Name </th>
		<th scope = "col" > Start Exam </th>
		<th scope = "col" > End Exam </th>
		<th scope = "col" > Release </th>
	</tr></thead><tbody>';
	$no = 1 ;
	while ($row = mysqli_fetch_array($res)) {
		$idx = $row[0];
		$stime = $row[1];
		$etime = $row[2];
		$qry = "SELECT * FROM students a WHERE a.idstudents='$idx'";
		$r = mysqli_query($GLOBALS['link'],$qry) or die(mysqli_error($GLOBALS['link']).'<br>'.$qry);
		$rows = mysqli_fetch_array($r);
		$table .= '<tr><td>' . $no . '</td><td>' . $rows[0] . '</td><td>' . $rows[1] . '</td><td>' . $stime . '</td><td>' . $etime . '</td>';
		if ($etime!=null) {
			$table.='<td>-</td>';
		}else{
			$table.='<td><button class="btn btn-xs btn-warning" onclick="release('.$rows[0].')"><i class="fa fa-sign-out" aria-hidden="true" ></i> Release</button></td>';
		}
		// &nbsp <button class="btn btn-xs btn-danger" onclick="restart('.$rows[0].')"><i class="fa fa-refresh" aria-hidden="true" ></i> Restart</button>
		$table .='</tr>';
		$no++;
	}
	$table .='</tbody></table>';
	return $table;
}

function infoExam($id){
	$sql = "SELECT b.exam_code, a.token, b.count_stu, d.program_name, e.fname 
	FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 
	INNER JOIN users c on a.proctor=c.id_user
	LEFT JOIN (SELECT exam_group.exam_code,programs.program_name FROM exam_group INNER JOIN programs
	ON SUBSTRING_INDEX(SUBSTRING_INDEX(exam_group.group_name,'.',2),'.',-1) = programs.id_program) d
	ON b.exam_code = d.exam_code
	LEFT JOIN users e ON a.proctor = e.id_user
	WHERE a.id_schedule = $id";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$dat = mysqli_fetch_array($res);
	return $dat;
}

function releaseUser($id){
	$sql = "UPDATE online_student set logout_time = NOW() where id_students = '".sqlValue($id)."' AND logout_time is null";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

// ================================================================================================================
// Report
function lastRreport($id){
	$sql = "SELECT a.* FROM exam_report a WHERE a.id_report = '$id' ";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$dat[] = mysqli_fetch_array($res);
	return $dat;
}
function showSchedule($id){
	$sql = "SELECT a.date,a.start_time FROM exam_schedule a WHERE a.exam_group = $id ";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$row = mysqli_fetch_array($res);
	$date = $row[0].' '.$row[1];
	$new_date = date('l, d-M-Y H:i:s', strtotime($date));
	return $new_date;
}

function filterSubject($id){
	$sql = "SELECT a.subject_name FROM subject_ls a INNER JOIN 
	(SELECT aa.id_subject FROM exam_percentage aa WHERE aa.exam_code=$id GROUP BY aa.id_subject) b ON a.id_subject = b.id_subject";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

function groupStudentReport($idg){
	$sql = "SELECT a.exam_code, SUBSTRING_INDEX(a.id_student,'.',1) id_student, b.fname 
	FROM exam_percentage a INNER JOIN (SELECT aa.idstudents, aa.fname FROM students aa) b 
	ON SUBSTRING_INDEX(a.id_student,'.',1) = b.idstudents
	WHERE a.exam_code=$idg GROUP BY a.id_student";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

function filterValue($ids,$idg){
	$sql = "SELECT percentage_true FROM exam_percentage a
	WHERE SUBSTRING_INDEX(a.id_student,'.',1) = '$ids' AND a.exam_code='$idg'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

function gPro($id,$a,$b){
	$id = $id;
	if($id == 1 ){
		$sql = "SELECT bb.prog, bb.program_name  FROM (SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1) prog 
		FROM exam_group a INNER JOIN exam_schedule b ON a.exam_code = b.exam_group 
		WHERE b.date BETWEEN '$a' AND '$b' 
		GROUP BY SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1)) aa 
		INNER JOIN programs bb ON aa.prog = bb.id_program";
	}else {
		$sql = "SELECT bb.prog, bb.program_name  FROM (SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1) prog 
		FROM exam_group a INNER JOIN exam_schedule b ON a.exam_code = b.exam_group 
		WHERE 
		b.date BETWEEN '$a' AND '$b' 
		GROUP BY SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1)) aa 
		INNER JOIN programs bb ON aa.prog = bb.id_program WHERE aa.prog ='$id'";
	}	
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

function subjectls($id){
	$sql = "SELECT a.id_program, b.subject_name 
	FROM program_detail a INNER JOIN subject_ls b ON a.id_subject= b.id_subject WHERE a.id_program = '$id'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

function repStue($id,$a,$b){
	$sql = "SELECT bb.exam_code,SUBSTRING_INDEX(bb.id_student,'.',1) ids, DATE_FORMAT(bb.start_time,'%Y-%m-%d') date_id  FROM exam_session bb 
	WHERE bb.exam_code IN 
	(SELECT a.exam_group FROM exam_schedule a INNER JOIN exam_group b ON a.exam_group = b.exam_code 
	WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(b.group_name,'.','2'),'.','-1') ='$id' 
	AND a.date BETWEEN '$a' AND '$b' AND `status`!='init')";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}

function neStu($id){
	$sql = "SELECT a.fname FROM students a WHERE a.idstudents = '$id'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$dat = mysqli_fetch_array($res);
	return $dat[0];
}

function progName($id){
	$sql = "SELECT a.program_name FROM programs a WHERE a.id_program ='$id'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$dat = mysqli_fetch_array($res);
	return $dat[0];
}

function groupReport($id_g,$name,$date_1,$date_2,$program){
	$id = $_SESSION['admin_group'];
	$date_1 = $date_1;
	$date_2 = $date_2;
	$program = $program;
	$name = $name;
	if ($name == false) {
		$name = "Report tanggal ".$date_1." sampai tanggal ".$date_2;
	}
	if ($program != 1) {
    $sql1 ="SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.','2'),'.','-1') id_program 
     FROM exam_group a INNER JOIN exam_schedule b ON a.exam_code = b.exam_group INNER JOIN
     (SELECT aa.exam_code FROM exam_session aa GROUP BY aa.exam_code) c ON a.exam_code=c.exam_code
     WHERE 
     SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.','2'),'.','-1') = '$program' AND 
     b.date BETWEEN '$date_1' AND '$date_2' GROUP BY SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.','2'),'.','-1')";   
  }else{
    $sql1= "SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.','2'),'.','-1') id_program 
     FROM exam_group a INNER JOIN exam_schedule b ON a.exam_code = b.exam_group INNER JOIN
     (SELECT aa.exam_code FROM exam_session aa GROUP BY aa.exam_code) c ON a.exam_code=c.exam_code
     WHERE 
     b.date BETWEEN '$date_1' AND '$date_2' GROUP BY SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.','2'),'.','-1')";
	}
	$res1 = mysqli_query($GLOBALS['link'],$sql1) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql1);
	$views = cekViewResult($id);
	$view = mysqli_fetch_array($views);
	if ($view[2] == 'true') {
		while ($rp = mysqli_fetch_array($res1)) {
			$program = progName($rp[0]);
			$n = subjectls($rp[0]);
			$c = mysqli_num_rows($n);
			$table .= '<table id="tb1">';
			$table .= '<caption>Program: ' . $program . '';
			$table .= '
		<tr>
			<th rowspan="2"> No </th>
			<th rowspan="2"> Group </th>
			<th rowspan="2">Date Exam</th>
			<th rowspan="2"> ID </th>
			<th rowspan="2"> Name </th>
      <th colspan="' . $c . '" >Subject</th>
			<th rowspan="2"> Nilai </th>
			<th rowspan="2"> Status </th>
    </tr>
		<tr>';
			while ($row = mysqli_fetch_array($n)) {
				$table .= '<th>' . $row[1] . '</th>';
			}
			$table .= '</tr>';
			$idstu = repStue($rp[0], $date_1, $date_2);
			$nom = 1;
			while ($r = mysqli_fetch_array($idstu)) {
				$name = neStu($r[1]);
				$nilai = filterValue($r[1], $r[0]);
				$table .= '<tr>';
				$table .= '<td>' . $nom . '</td>';
				$table .= '<td>Group ' . $r[0] . '</td>';
				$table .= '<td>' . $r[2] . '</td>';
				$table .= '<td>' . $r[1] . '</td>';
				$table .= '<td>' . $name . '</td>';
				while ($var = mysqli_fetch_array($nilai)) {
					$table .= '
				<td>' . $var[0] . '</td>';
				}
				$points = array();
				$trues = array();
				foreach (resultExam($r[1], $r[0]) as $ar) {
					$quest = $ar[2] + $ar[3] + $ar[4]; // jumlah soal
					$points[] = $quest; //dibuat array
					$trues[] = $ar[2]; //dibuat array
				}
				$point = array_sum($points);
				$true = array_sum($trues);
				$val = ($true / $point) * 100;
				$n = number_format((float)$val, 2, '.', '') . ' %';
				$conc = concResult($r[0], $val);
				$table .= '
				<td>' . $n . '</td>
				<td>' . $conc . '</td>';
				$table .= '</tr>';
				$nom++;
			}
			$table .= '</table>';
			return $table;
		}
	}else {
		while ($rp = mysqli_fetch_array($res1)) {
			$program = progName($rp[0]);
			$n = subjectls($rp[0]);
			$c = mysqli_num_rows($n);
			$table .= '<table id="tb1">';
			$table .= '<caption>Program: ' . $program . '';
			$table .= '
		<tr>
			<th > No </th>
			<th > Group </th>
			<th > Date Exam</th>
			<th > ID </th>
			<th > Name </th>
    </tr>';
			$idstu = repStue($rp[0], $date_1, $date_2);
			$nom = 1;
			while ($r = mysqli_fetch_array($idstu)) {
				$name = neStu($r[1]);
				$nilai = filterValue($r[1], $r[0]);
				$table .= '<tr>';
				$table .= '<td>' . $nom . '</td>';
				$table .= '<td>Group ' . $r[0] . '</td>';
				$table .= '<td>' . $r[2] . '</td>';
				$table .= '<td>' . $r[1] . '</td>';
				$table .= '<td>' . $name . '</td>';
				$table .= '</tr>';
				$nom++;
			}
			$table .= '</table>';
			return $table;
		}
	}
	// //////////////////////////////////////////////////////////////
	// echo $table;die();
	$acc = mysqli_num_rows($res1);
	if ($acc > 0) {
		return $table;
		mysqli_close($GLOBALS['link']);
	}else {
		return '0' ;
		mysqli_close($GLOBALS['link']);
	}
	// return $sql;
}

function savingLog($note,$dt1,$dt2,$prog,$par){
	$id_customer = $_SESSION['admin_group'];
	$id_user = $_SESSION["admin_id"];
	$date = date("Ymd");
	$sel_id = "SELECT MAX(CONVERT(SUBSTRING_INDEX(a.id_report,'.',-1),UNSIGNED INTEGER)) as val FROM customer_log_report a 
	WHERE SUBSTRING_INDEX(a.id_report,'.',1) = '$id_customer'";
	$run_id = mysqli_query($GLOBALS['link'],$sel_id) or die(mysqli_error($GLOBALS['link']).'<br>'.$sel_id);
	$val_id = mysqli_fetch_array($run_id);
	$id_report =  $val_id[0];
	$new_id = $id_report+1;
	$id = $id_customer.'.'.$date.'.'.	$new_id; 
	$sql = "INSERT INTO `customer_log_report`(`id_report`,`exefile`,`id_creator`,`note`,`dt_1`,`dt_2`,`program`) 
	VALUES ('$id','$par','$id_user','$note','$dt1','$dt2','$prog')";;
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
}

function exeFile($title){
	if ($title == 'program_exam' ) {
		$par = '4_pic_export.php';
	} elseif ($title == 'program_statistik') {
		$par = '4_pic_export_sta.php';
	} else {
		$par = null;
	}
	return $par;
}

function lastReport($amn){
	// $amn = 10;
	$id = $_SESSION['admin_group'];
	$sql = "SELECT a.id_report,SUBSTRING_INDEX(SUBSTRING_INDEX(a.id_report,'.',2),'.',-1) date,a.note,b.fname,a.dt_1,a.dt_2,a.program,a.exefile 
	FROM customer_log_report a LEFT JOIN users b ON a.id_creator=b.id_user 
	WHERE SUBSTRING_INDEX(a.id_report,'.',1) = '$id' ORDER BY CONVERT(SUBSTRING_INDEX(a.id_report,'.',-1),UNSIGNED INTEGER) ASC 
	LIMIT $amn ";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	return $res;
}
// 
function groupSub($id){
	$sql="SELECT GROUP_CONCAT(aa.subject_name) result FROM 
	(SELECT c.subject_name FROM programs a 
	INNER JOIN program_detail b ON a.id_program = b.id_program 
	LEFT JOIN subject_ls c ON b.id_subject = c.id_subject WHERE a.id_program ='$id') aa";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$row = mysqli_fetch_array($res);
	return $row[0];
}

function sumPar($id){
	$sql = "SELECT COUNT(b.id_student) FROM exam_group a INNER JOIN exam_session b ON a.exam_code = b.exam_code 
	WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1) = '$id'";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$dat = mysqli_fetch_array($res);
	$num = $dat[0];
	return $num;
} 

function sumGrad($id,$num,$min,$a,$b){
	$sql = "SELECT COUNT(aa.id_student) FROM (SELECT a.exam_code, b.id_student, ROUND((SUM(b.percentage_true)/$num)*100,2) val 
	FROM exam_session a INNER JOIN  exam_percentage b ON a.id_student=b.id_student WHERE a.start_time BETWEEN '$a' AND '$b' GROUP BY b.id_student ORDER BY a.exam_code ASC) aa WHERE aa.val >=$min";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$dat = mysqli_fetch_array($res);
	return $dat[0];
}

function sumUngrad($id,$num,$min,$a,$b){
	$sql ="SELECT COUNT(aa.id_student) FROM (SELECT a.exam_code, b.id_student, ROUND((SUM(b.percentage_true)/$num)*100,2) val 
	FROM exam_session a INNER JOIN  exam_percentage b ON a.id_student=b.id_student WHERE a.start_time BETWEEN '$a' AND '$b' GROUP BY b.id_student ORDER BY a.exam_code ASC) aa WHERE aa.val <$min";
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	$dat = mysqli_fetch_array($res);
	return $dat[0];
}

function progReport($id,$a,$b){
	$idses = $_SESSION['admin_group'];
	$id_prog = $id ;
	$data_a = $a ;
	$data_b = $b ;
	$period = date('d/M/Y',strtotime($data_a)).'-'.date('d/M/Y', strtotime($data_b));
	if ($id_prog != 1 ) {
		$sql = "SELECT a.exam_code,c.program_name,c.sum_question,c.pass_grade,c.id_program 
		FROM exam_group a INNER JOIN exam_schedule b ON a.exam_code=b.exam_group LEFT JOIN programs c ON SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1)=c.id_program  
		WHERE b.`status` != 'init' AND c.id_program='$id_prog' AND b.date BETWEEN '$data_a' AND '$data_b' GROUP BY c.id_program";
	}else {
		$sql = "SELECT a.exam_code,c.program_name,c.sum_question,c.pass_grade,c.id_program 
		FROM exam_group a INNER JOIN exam_schedule b ON a.exam_code=b.exam_group LEFT JOIN programs c ON SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1)=c.id_program  
		WHERE b.`status` != 'init' AND b.date BETWEEN '$data_a' AND '$data_b' GROUP BY c.id_program";
	}
	$res = mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);
	if ($res == true) {
		$views = cekViewResult($idses);
		$view = mysqli_fetch_array($views);
		if ($view[2] == 'true') {
			$progrep =
				'<table class="tb1">';
			$progrep .= '<caption>Report Programs Statistic</caption>';
			$progrep .=
				'<thead>
			<tr>
				<th>No</th>
				<th>Programs</th>
				<th>Periode</th>
				<th>Subjects</th>
				<th>Participants</th>
				<th>Graduation</th>
				<th>Ungraduation</th>
				<th>Percent of graduation</th>
			<tr>
		</thead><tbody>';
			$no = 1;
			while ($row = mysqli_fetch_array($res)) {
				$subs = groupSub($row[4]);
				$par = sumPar($row[4]);
				$n_grad = sumGrad($row[0], $row[2], $row[3], $data_a, $data_b);
				$n_ungrad = sumUngrad($row[0], $row[2], $row[3], $data_a, $data_b);
				$pgrad = ($n_grad / $par) * 100 . ' %';
				$n = number_format((float)$pgrad, 2, '.', '') . ' %';
				$progrep .= '<tr>';
				$progrep .=
					'<td>' . $row[0] . '</td>
					<td>' . $row[1] . '</td>
					<td>' . $period . '</td>
					<td>' . $subs . '</td>
					<td>' . $par . '</td>
					<td>' . $n_grad . '</td>
					<td>' . $n_ungrad . '</td>
					<td>' . $n . '</td>';
				$progrep .= '</tr>';
				$no++;
			}
			$progrep .=
				'</tbody></table>';
		}else {
			$progrep =
				'<table class="tb1">';
			$progrep .= '<caption>Report Programs Statistic</caption>';
			$progrep .=
				'<thead>
			<tr>
				<th>No</th>
				<th>Programs</th>
				<th>Periode</th>
				<th>Subjects</th>
				<th>Participants</th>
			<tr>
		</thead><tbody>';
			$no = 1;
			while ($row = mysqli_fetch_array($res)) {
				$subs = groupSub($row[4]);
				$par = sumPar($row[4]);
				$n_grad = sumGrad($row[0], $row[2], $row[3], $data_a, $data_b);
				$n_ungrad = sumUngrad($row[0], $row[2], $row[3], $data_a, $data_b);
				$pgrad = ($n_grad / $par) * 100 . ' %';
				$n = number_format((float)$pgrad, 2, '.', '') . ' %';
				$progrep .= '<tr>';
				$progrep .=
					'<td>' . $row[0] . '</td>
					<td>' . $row[1] . '</td>
					<td>' . $period . '</td>
					<td>' . $subs . '</td>
					<td>' . $par . '</td>';
				$progrep .= '</tr>';
				$no++;
			}
			$progrep .=
				'</tbody></table>';
		}
		return $progrep;
	}
}
// ================================================================================================================
function deleteSiswa($user_id){
	$sql="delete from users where id='".$user_id."'";
	mysqli_query($GLOBALS['link'],$sql);
	$sql="delete from peserta_ujian where id_peserta in (
	select id_peserta from user_test where user_id='".$user_id."')";
	mysqli_query($GLOBALS['link'],$sql);
	$sql="delete from session_ujian where id_peserta in (
	select id_peserta from user_test where user_id='".$user_id."')";
	mysqli_query($GLOBALS['link'],$sql);
	$sql="delete from soal_ujian where id_peserta in (
	select id_peserta from user_test where user_id='".$user_id."')";
	mysqli_query($GLOBALS['link'],$sql);
	$sql="delete from user_test where user_id='".$user_id."'";
	mysqli_query($GLOBALS['link'],$sql);
}

function sendPassword($user_id,$subject="User & Password Anda",$passw=""){
	$sql="select id,fname,passw,email,grp from users where id='".$user_id."'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	if ($row=mysqli_fetch_row($rs)){
		switch (strtolower($row[4])){
			case "student":
				$tpl_file="siswa_tpl.txt";
				//$subject="User & Password Anda";
			break;
			case "partner admin":
				$tpl_file="pa_tpl.txt";
				//$subject="User & Password Anda";
			break;
			case "test admin":
				$tpl_file="ta_tpl.txt";
				//$subject="User & Password Anda";
			break;
		}
		$tpl_file=gotoAppPath()."mailbuff/".$tpl_file;
		$handle = fopen($tpl_file, "r");
		$contents = fread($handle, filesize($tpl_file));
		fclose($handle);
		$contents=str_replace("[id]",$row[0],$contents);
		$contents=str_replace("[name]",$row[1],$contents);
		if ($passw!=""){
			$contents=str_replace("[passw]",$passw,$contents);
		}else{
			$contents=str_replace("[passw]",$row[2],$contents);	
		}
		$html="<html><head></head><body><div><img src=\"header.jpg\"></div>";
		$html.=$contents;
		$html.="</body></html>";
		$fNameHTML="";
		$email=$row[3];
		$r=kirimEmail($email,$subject,$html,$fNameHTML);
		return $r;
	}else{
		return false;
	}
}

function readSMTPcfg(){
	$sql="select `host`,`secure_connection`,`port`,`auth`,`account`,`passw`,`account_name` from `smtp`";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	if ($row=mysqli_fetch_row($rs)){
		//read config
		$GLOBALS["smtp_host"]=$row[0];
		$GLOBALS["smtp_connection_type"]=$row[1];
		$GLOBALS["smtp_port"]=$row[2];
		if ($row[3]=='1'){
			$GLOBALS["smtp_auth"]=true;
		}else{
			$GLOBALS["smtp_auth"]=false;
		}
		$GLOBALS["mail_from"]=$row[4];
		$x=explode('@',$GLOBALS["mail_from"]);
		$GLOBALS["smtp_user"]=$x[0];
		$GLOBALS["smtp_pass"]=$row[5];
		if ($row[6]!=""){
			$GLOBALS["mail_from_name"]=$row[6];
		}else{
			$GLOBALS["mail_from_name"]=$GLOBALS["mail_from"];
		}
		return true;
	}else{
		//read config
		$GLOBALS["smtp_host"]="";
		$GLOBALS["smtp_connection_type"]="";
		$GLOBALS["smtp_port"]="";
		$GLOBALS["smtp_auth"]=true;
		$GLOBALS["mail_from"]="";
		$GLOBALS["smtp_user"]="";
		$GLOBALS["smtp_pass"]="";
		$GLOBALS["mail_from_name"]="";
		return false;
	}
}
function modulname(){
	$scriptname=$_SERVER["SCRIPT_NAME"];
	$modulnames=explode("/",$scriptname);
	$modulname=array_pop($modulnames);
	return $modulname;
}

function checkpriviledge($grp,$modulname){
	$sql = "select * from grp_module where (grp='".$grp."' or grp='*' ) and upper(modul_name)=upper('".$modulname."')";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	if ($row=mysqli_fetch_row($rs)){
		return true;
	}else{
		return true;
	}
}

function generateKodeUjian($prefix){
		$sql="select ifnull(max(ctr),0)+1 from kode_counter where prefix='".$prefix."'";
		$rs=mysqli_query($GLOBALS['link'],$sql);
		$ctr=mysqli_fetch_row($rs);
		$sql="insert into kode_counter(prefix,ctr) values('".$prefix."','".$ctr[0]."')";
		mysqli_query($GLOBALS['link'],$sql);
		return $prefix.".".substr("000".$ctr[0],-3);
}

function generateToken(){
	$kar="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWZYZ1234567890";
	$a="";
	for ($i=0; $i<=27; $i++){
		$n=rand(0,strlen($kar)-1);
		$a.=$kar[$n];
	}
	return $a;
}

function generateUserId($prefix){
		$sql="select ifnull(max(ctr),0)+1 from kode_counter where prefix='".$prefix."'";
		$rs=mysqli_query($GLOBALS['link'],$sql);
		$ctr=mysqli_fetch_row($rs);
		$sql="insert into kode_counter(prefix,ctr) values('".$prefix."','".$ctr[0]."')";
		mysqli_query($GLOBALS['link'],$sql);
		return $prefix.substr("000".$ctr[0],-3);
}

function generateUserTest($prefix){
		$sql="select ifnull(max(ctr),0)+1 from kode_counter where prefix='".$prefix."'";
		$rs=mysqli_query($GLOBALS['link'],$sql);
		$ctr=mysqli_fetch_row($rs);
		$sql="insert into kode_counter(prefix,ctr) values('".$prefix."','".$ctr[0]."')";
		mysqli_query($GLOBALS['link'],$sql);
		return $prefix.$ctr[0];
}

function generateCodeByPrefix($prefix){
		$sql="select ifnull(max(ctr),0)+1 from kode_counter where prefix='".$prefix."'";
		$rs=mysqli_query($GLOBALS['link'],$sql);
		$ctr=mysqli_fetch_row($rs);
		$sql="insert into kode_counter(prefix,ctr) values('".$prefix."','".$ctr[0]."')";
		mysqli_query($GLOBALS['link'],$sql);
		return $prefix.$ctr[0];
}

function gotoAppPath(){
	//SCRIPT_NAME 	/myweb/exam/admin/info.php 
	$s="";
	$v=$_SERVER["SCRIPT_NAME"];
	$v = str_replace($GLOBALS["app-path"],"",$v);
	for ($x=1 ; $x <= substr_count($v,"/"); $x++){
		$s.="../";
	}
	return $s;
}

function cekStudentLogin(){
	if (isset($_SESSION["user_group"])){
		if ($_SESSION["user_group"]=="student"){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}


function cekStudentOnline($user_id){
	$sql="select * from online_user where user_id='".$user_id."' and logout_time is null";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	if ($row=mysqli_fetch_row($rs)){
		return true;
	}else{
		return false;
	}
}

function studentLogin($id_peserta,$password){
	/*
	$sql="select a.id,a.fname,a.grp from users a,user_test b
	 where b.user_id = a.id and a.grp = 'student' 
				and a.passw='".sqlValue($password)."' 
				and b.id_peserta='".sqlValue($id_peserta)."' 
				and b.status=1";
				//echo $sql;
	$rs=mysqli_query($GLOBALS['link'],$sql);
	if ($row=mysqli_fetch_row($rs)){
		if (cekStudentOnline($id_peserta)){
			return 2;
		}else{
			$_SESSION["user_id"]=$row[0];
			$_SESSION["id_peserta"]=$id_peserta;
			$_SESSION["user_name"]=$row[1];
			$_SESSION["user_group"]=$row[2];
			loginLog($id_peserta);
			return 1;
		}
	}else{
		return 0;
	}
	*/
	
	$sql="select id,fname,grp from users where id='".sqlValue($id_peserta)."'
	and grp='student'";
	$qwr="select kode_ujian from jadwal_ujian where token='".sqlValue($password)."'";
	//echo $sql
	//echo $id_peserta.'===='.$password;

	$rs=mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	$rs_qwr=mysqli_query($GLOBALS['link'],$qwr) or die(mysqli_error($GLOBALS['link']));
//	print_r(mysqli_num_rows($rs));
//	print_r($rs);
//	die();
	if ($rw=mysqli_fetch_row($rs_qwr)) {
		$GLOBALS['kode_ujian_token'] = $rw[0];
	if ($row=mysqli_fetch_row($rs)){
		$user_name=$row[1];
		$user_group=$row[2];
		//find id_peserta
		$sql="select id_peserta from user_test 
			where user_id='".sqlValue($id_peserta)."' and status=1
			and prioritas in (
				select max(prioritas) from user_test where user_id='".sqlValue($id_peserta)."' and status=1
				and ifnull(eff_date,".sqlFormatDate(date("m/d/Y 23:59")).") <= ".sqlFormatDate(date("m/d/Y H:i"))."
			)
		";
		$rs=mysqli_query($GLOBALS['link'],$sql);
		if ($row=mysqli_fetch_row($rs)){
			$test_id=$row[0];
			if (cekStudentOnline($id_peserta)){  //login id is online?
				return 2;
			}else{
				$_SESSION["user_id"]=$id_peserta; //login id
				$_SESSION["id_peserta"]=$test_id; //test id
				$_SESSION["user_name"]=$user_name;
				$_SESSION["user_group"]=$user_group;
				loginLog($id_peserta); //log login id
				return 1;
			}
		}else{
			return 0;	
		}
	}else{
		return 0; //invalid id/pass
	}

	}else{
		return 0; //invalid id/pass
	}
	
}

function loginLog($id_peserta){
	$skrg=date("m/d/Y H:i");
	$ip=$_SERVER["REMOTE_ADDR"];
	$sql="insert into online_user(user_id,login_time,login_from,logout_time) values (
				'".$id_peserta."',".sqlFormatDate($skrg).",'".$ip."',null)";
	mysqli_query($GLOBALS['link'],$sql);
}

function logoutLog($id_peserta){
	$skrg=date("m/d/Y H:i");
	$sql="update online_user set logout_time = ".sqlFormatDate($skrg)." where user_id='".$id_peserta."'
				 and logout_time is null";
	mysqli_query($GLOBALS['link'],$sql);
}

function sqlValue($val){
	$v=str_replace("'","''",$val);
	return $v;
}

function sqlFormatDate($val,$format="%m/%d/%Y %H:%i:%s"){
	return "STR_TO_DATE( '".$val."', '".$format."' )";
}

function updateStatusJadwalUjian($kode,$status){
	$sql="update jadwal_ujian set status='".$status."'";
	switch ($status){
		case "started":
			$cgrp = "select count(*) from group_ujian where kode_ujian='".$kode."'";
			$rs=mysqli_query($GLOBALS['link'],$cgrp);
			$row=mysqli_fetch_row($rs);
			if ($row[0]==0){
				//create group
				$grp=generateCodeByPrefix("Group ");
				$cgrp="insert into group_ujian(kode_ujian,nama_group) values('".$kode."','".$grp."')";
				mysqli_query($GLOBALS['link'],$cgrp);
			}
			$sql.=",start_time=".sqlFormatDate(date("d/m/Y H:i:s"),"%d/%m/%Y %H:%i:%s");
		break;
		case "init":
			$sql.=",start_time=null";
		break;
	}
	
	$sql.=" where kode_ujian='".$kode."'";
	mysqli_query($GLOBALS['link'],$sql);
}

function getKomposisiSoal($kode_ujian){
	$sql="select persen_lulus from jadwal_ujian where kode_ujian='".$kode_ujian."'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	$persen_lulus="1";
	if ($row=mysqli_fetch_row($rs)){
		$persen_lulus=$row[0];
	}
	$sql="select easy,medium,hard,jumlah_soal,jumlah_soal_w,jumlah_soal_e,jumlah_soal_p from komposisi_soal where kode_ujian='".$kode_ujian."'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	$easy="";$medium="";$hard="";$jumlah_soal="";
	$jumlah_soal_w="";$jumlah_soal_e="";$jumlah_soal_p="";
	if ($row=mysqli_fetch_row($rs)){
		$easy=$row[0];
		$medium=$row[1];
		$hard=$row[2];
		$jumlah_soal=$row[3];
		$jumlah_soal_w=$row[4];
		$jumlah_soal_e=$row[5];
		$jumlah_soal_p=$row[6];
	}
	echo "<h1>Jumlah soal:</h1>";
	echo "<input type=\"hidden\" name=\"jumlah_soal\" id=\"jumlah_soal\" value=\"".$jumlah_soal."\" size=\"2\">";
	echo "<table><tbody>";
	echo "<tr><td>Word : <input style=\"border:1px solid;\" type=\"text\" name=\"jumlah_soal_w\" id=\"jumlah_soal_w\" value=\"".$jumlah_soal_w."\" size=\"2\">        </td></tr>";
	echo "<tr><td>Excel : <input style=\"border:1px solid;\" type=\"text\" name=\"jumlah_soal_e\" id=\"jumlah_soal_e\" value=\"".$jumlah_soal_e."\" size=\"2\">       </td></tr>";
	echo "<tr><td>Power Point : <input style=\"border:1px solid;\" type=\"text\" name=\"jumlah_soal_p\" id=\"jumlah_soal_p\" value=\"".$jumlah_soal_p."\" size=\"2\"> </td></tr>";
	echo "</tbody></table>";
	echo "<h1>komposisi soal:</h1>";
	/*
	echo "<div id=\"slider\" style=\"width:300;\"></div><br>";
	echo "<div id=\"komposisi\">";
	if ($easy!=""){
		echo "easy:".$easy."% | medium:".$medium."% | hard:".$hard."%";
	}else{
		echo "komposisi soal belum di-set!";
	}
	echo "</div>";
	*/
	echo "<table><tbody>";
	echo "<tr><td>Easy : <input style=\"border:1px solid;\" type=\"text\" name=\"easy\" id=\"easy\" value=\"".$easy."\" size=\"2\">%        </td></tr>";
	echo "<tr><td>Hard : <input style=\"border:1px solid;\" type=\"text\" name=\"hard\" id=\"hard\" value=\"".$hard."\" size=\"2\">%       </td></tr>";
	echo "</tbody></table>";
	
	echo "<h1>Minimal Grade Kelulusan:</h1>";
	echo "<input style=\"border:1px solid;\" type=\"text\" name=\"persen_lulus\" id=\"persen_lulus\" value=\"".$persen_lulus."\" size=\"2\">%<br><br>";
	echo "<div id=\"btkomposisi\">Save</div>";
	//echo "<input type=\"hidden\" name=\"easy\" id=\"easy\" value=\"".$easy."\">";
	echo "<input type=\"hidden\" name=\"medium\" id=\"medium\" value=\"".$medium."\">";
	//echo "<input type=\"hidden\" name=\"hard\" id=\"hard\" value=\"".$hard."\">";
}

function listPesertaUjian($kode_ujian,$panel=false){
	$sql="select aa.id,aa.fname,ifnull(bb.user_id,'offline'),aa.id_peserta olstat from 
			(
			select a.id_peserta,c.fname,c.id from peserta_ujian a,user_test b,users c
			where a.kode_ujian='".$kode_ujian."'
			and a.id_peserta=b.id_peserta
			and b.user_id = c.id
			) aa left join online_user bb
			on aa.id = bb.user_id
			and bb.logout_time is null";

	//echo $sql;
	$rs=mysqli_query($GLOBALS['link'],$sql);
	//echo "<div class=\"datagrid\" >";
	echo "<table id=\"tabel_peserta\">";
	echo "<thead>";
	echo "<tr>";
	echo "<th>ID PESERTA</th>";
	echo "<th>NAMA</th>";
	echo "<th>STATUS</th>";
	if ($panel){
		echo "<th>Option</th>";
	}
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	$i=0;$JQui="";
	while ($row=mysqli_fetch_row($rs)){
		$i++;
		if ($i % 2){
			$tclass="";
		}else{
			$tclass="class=\"alt\"";
		}
		if ($row[2]!="offline"){
			$status="<font color=\"green\"><b>online</b></font>";
		}else{
			$status="<font color=\"grey\"><i>offline</i></font>";
		}
		echo "<tr ".$tclass.">";
		echo "<td>".$row[0]."</td>";  
		echo "<td>".$row[1]."</td>";
		echo "<td>".$status."</td>";
		if ($panel){
			//reset
			/*echo "<td><input type=\"button\" id=\"btreset".$i."\" name=\"btreset".$i."\" value=\"Reschedule\" onclick=\"javascript:reschedule('".$row[0]."')\"></td>";
			$JQui.="$(\"#btreset".$i."\").button();
							";
			*/
			//release only
			echo "<td><input type=\"button\" id=\"btrelease".$i."\" name=\"btrelease".$i."\" value=\"Release\" onclick=\"javascript:release('".$row[0]."')\"></td>";
			$JQui.="$(\"#btrelease".$i."\").button();
							";
		}
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	//echo "</div>";
	if ($JQui!=""){
		echo "
		<script> 
			$(function(){\r\n ".$JQui."}); 
		</script> \r\n ";
	}
}

function updateKomposisi($kode_ujian,$easy,$medium,$hard,$jumlah_soal,$jumlah_soal_w,$jumlah_soal_e,$jumlah_soal_p,$id_peserta=""){
	if ($id_peserta==""){
		$sql="select * from komposisi_soal where kode_ujian='".$kode_ujian."'";
		$rs=mysqli_query($GLOBALS['link'],$sql);
		$rows=mysqli_num_rows($rs);
		if ($rows>0){
			$sql="update komposisi_soal set easy='".$easy."',medium='".$medium."',hard='".$hard."'
			,jumlah_soal='".$jumlah_soal."' 
			,jumlah_soal_w='".$jumlah_soal_w."' 
			,jumlah_soal_e='".$jumlah_soal_e."' 
			,jumlah_soal_p='".$jumlah_soal_p."' 
			where kode_ujian='".$kode_ujian."'";
		}else{
			$sql="insert into komposisi_soal(kode_ujian,easy,medium,hard,jumlah_soal,jumlah_soal_w,jumlah_soal_e,jumlah_soal_p) values (
						'".$kode_ujian."','".$easy."','".$medium."','".$hard."'
						,'".$jumlah_soal."'
						,'".$jumlah_soal_w."'
						,'".$jumlah_soal_e."'
						,'".$jumlah_soal_p."'
						)";
		}
		mysqli_query($GLOBALS['link'],$sql);
	}else{
		$sql="select * from komposisi_soal_peserta where kode_ujian='".$kode_ujian."' 
					and id_peserta='".$id_peserta."'";
		$rs=mysqli_query($GLOBALS['link'],$sql);
		$rows=mysqli_num_rows($rs);
		if (rows>0){
			$sql="update komposisi_soal_peserta set easy='".$easy."',medium='".$medium."',hard='".$hard."' where kode_ujian='".$kode_ujian."' 
					and id_peserta='".$id_peserta."'";
		}else{
			$sql="insert into komposisi_soal_peserta(kode_ujian,easy,medium,hard,id_peserta) values (
						'".$kode_ujian."','".$easy."','".$medium."','".$hard."','".$id_peserta."')";
		}
		mysqli_query($GLOBALS['link'],$sql);
	}
}

function getInfoJadwal($kode_ujian,$format=0){
	$sql="SELECT a.kode_ujian, 
								a.tanggal, 
								c.lname mapel, 
								a.durasi, 
								a.kelas, 
								ifnull(b.nama_group,'not created') nama_group,
								a.test_admin,
								a.status
				FROM jadwal_ujian a LEFT JOIN group_ujian b ON a.kode_ujian = b.kode_ujian
				,mapel c
				where a.kode_mapel = c.kode
				and a.kode_ujian='".$kode_ujian."'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	
	if ($row=mysqli_fetch_row($rs)){
		switch ($format){
			case 0: //format lama (setting ujian)
				echo "<div class=\"datagrid\" style=\"padding-left:5px;\">";
				echo "<ul>";
				echo "<li><b>KODE UJIAN : </b>";
				echo $row[0]."</li>";
				echo "<li><b>Tanggal Ujian : </b>";
				echo $row[1]."</li>";
				echo "<li><b>Mata Pelajaran : </b>";
				echo $row[2]."</li>";
				echo "<li><b>Durasi : </b>";
				echo $row[3]." menit</li>";
				echo "<li><b>Kelas : </b>";
				echo $row[4]."</li>";
				echo "<li><b>Group : </b>";
				echo $row[5]."</li>";
				echo "<li><b>Komposisi Soal:</b>";
				$sql="select easy,medium,hard,jumlah_soal from komposisi_soal where kode_ujian='".$kode_ujian."'";
				$rs=mysqli_query($GLOBALS['link'],$sql);
				if ($row=mysqli_fetch_row($rs)){
					echo "<ul>";
					echo "<li><b>easy:</b>".$row[0]."%</li>";
					echo "<li><b>medium:</b>".$row[1]."%</li>";
					echo "<li><b>hard:</b>".$row[2]."%</li>";
					echo "<li><b>jumlah soal:</b>".$row[3]."</li>";
					echo "</ul>";
				}else{
					echo "<span style=\"color:grey;\"> not set</span>";
				}
				echo "</li>";
				$sql="select count(*) from peserta_ujian where kode_ujian='".$kode_ujian."'";
				$rs=mysqli_query($GLOBALS['link'],$sql);
				$row=mysqli_fetch_row($rs);
				echo "<li><b>Jumlah Peserta : </b>";
				echo $row[0]."</li>";
				echo "</ul>";
				echo "</div>";
			break;
			case 1: //dashboard test admin
				echo "<div class=\"datagrid\">";
				echo "<table>";
				echo "<thead>";
				echo "<tr>";
				echo "<th>Kode Ujian</th>";
				echo "<th>Tanggal</th>";
				echo "<th>Mata Pelajaran</th>";
				echo "<th>Durasi</th>";
				echo "<th>Kelas</th>";
				echo "<th>Group</th>";
				echo "<th>Status</th>";
				echo "</tr>";
				echo "</thead>";
				echo "<tbody>";
				echo "<tr>";
				echo "<td>".$row[0]."</td>";
				echo "<td>".$row[1]."</td>";
				echo "<td>".$row[2]."</td>";
				echo "<td>".$row[3]." menit</td>";
				echo "<td>".$row[4]."</td>";
				echo "<td>".$row[5]."</td>";
				echo "<td>".$row[7]."</td>";
				echo "</tr>";
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
			break;
		}
		
	}
	
}

function listJadwalUjian($panelopt=1){
	//$yesterday=date("m/d/Y",strtotime("-1 days"))." 00:00";
	$thismorning=date("m/d/Y")." 00:00";
	/*$_SESSION["user_id"]="";
	$_SESSION["user_name"]="";
	$_SESSION["user_group"]="";
	*/
	$admin_type=$_SESSION["admin_group"];
	$admin_id=$_SESSION["admin_id"];
	$sql="SELECT a.kode_ujian, 
								a.tanggal, 
								c.lname mapel, 
								a.durasi, 
								a.kelas, 
								ifnull(b.nama_group,'not created') nama_group,
								d.fname,
								a.status,
								a.test_admin
								
				FROM jadwal_ujian a LEFT JOIN group_ujian b ON a.kode_ujian = b.kode_ujian
				,mapel c,users d
				where a.kode_mapel = c.kode 
				and a.test_admin = d.id
				and a.tanggal>=".sqlFormatDate($thismorning);
	
	/* show all admin, filter di panel_option
	if ($admin_type=="test admin"){
		$sql.=" and a.test_admin='".$admin_id."' ";
	}
	*/
	$sql.=" order by a.tanggal";
	//echo $sql;
	$rs=mysqli_query($GLOBALS['link'],$sql);
	echo "<div class=\"datagrid\">";
	echo "<table>";
	echo "<thead>";
	echo "<tr>";
	echo "<th>KODE UJIAN</th>";
	echo "<th>TANGGAL/JAM</th>";
	echo "<th>MATA PELAJARAN</th>";
	echo "<th>DURASI</th>";
	echo "<th>KELAS</th>";
		echo "<th>GROUP</th>";
		echo "<th>TEST ADMIN</th>";
		echo "<th>STATUS</th>";
		echo "<th>Option</th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	$i=0;
	while ($row=mysqli_fetch_row($rs)){
		$i++;
		if ($i % 2){
			$tclass="";
		}else{
			$tclass="class=\"alt\"";
		}
		if ($row[5]=="not created"){
			$row[5]="<font color=\"grey\"><i>not created</i></font>";
		}
		$link_option="";
		if ($panelopt==1){ //setting ujian
			if ($row[7]=="init"){
				$link_option.="<a href=\"javascript:paneloption('".$row[0]."','setting');\">setting</a>";
				//$link_option.=" | ";
				//$link_option.="<a href=\"javascript:paneloption('".$row[0]."','start');\">start</a>";
			}
			if ($row[7]=="started"){
				//$link_option.="<a href=\"javascript:paneloption('".$row[0]."','stop');\">stop</a>";
			}
			if ($row[7]=="finished"){
				$link_option.="<a href=\"javascript:paneloption('".$row[0]."','reset');\">reset</a>";
				//$link_option.=" | ";
				//$link_option.="<a href=\"javascript:paneloption('".$row[0]."','result');\">View Result</a>";
			}
		}elseif($panelopt==0){ //add/edit/delete jadwal
			$link_option="";
			if ($row[7]=="init"){
				$link_option.="<a href=\"javascript:paneloption('".$row[0]."','edit');\">edit</a>";
				$link_option.=" | ";
				$link_option.="<a href=\"javascript:paneloption('".$row[0]."','delete');\">delete</a>";
			}
		}elseif($panelopt==2){
			//cant start/stop only
			$link_option.="<a href=\"javascript:paneloption('".$row[0]."','select');\">select</a>";
			if ($row[7]=="finished"){
				$link_option="&nbsp;";
			}
		}
		
		//override link option
		if (strtoupper($admin_type)=="TEST ADMIN"){ //admin pengawas tidak bisa edit jadwal pengawas lain
			if ($row[8]!=$admin_id){
				$link_option="&nbsp;";
			}
		}
		echo "<tr ".$tclass.">";
		echo "<td>".$row[0]."</td>";
		echo "<td>".$row[1]."</td>";
		echo "<td>".$row[2]."</td>";
		echo "<td>".$row[3]." menit</td>";
		echo "<td>".$row[4]."</td>";
		echo "<td>".$row[5]."</td>";
		echo "<td>".$row[6]."</td>";
		echo "<td>".$row[7]."</td>";
		echo "<td>".$link_option."</td>";
		echo "</tr>";
		
	}
	echo "</tbody>";
	echo "</table>";
	echo "</div>";
	echo "<form name=\"fpaneloption\" method=\"post\" action=\"\">";
	echo "<input type=\"hidden\" name=\"kode_ujian\">";
	echo "<input type=\"hidden\" name=\"cmd\">";
	echo "</form>";
	echo "<script language=\"javascript\"> \r\n
				function paneloption(kode_ujian,cmd){ \r\n
					if (cmd=='delete'){
						if (!confirm('Are you sure?!')){
							return;
						}
					}
					document.fpaneloption.kode_ujian.value=kode_ujian; \r\n
					document.fpaneloption.cmd.value=cmd; \r\n
					document.fpaneloption.submit(); \r\n
				} \r\n
				</script> \r\n
			";
}

function getGroupName($kode_ujian){
	$sql="select nama_group from group_ujian where kode_ujian='".$kode_ujian."'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	$s="<input type=\"text\" name=\"nama_group\" value=\"\" style=\"border:1px solid;\"> ";
	$s.="<input type=\"hidden\" name=\"old_nama_group\" value=\"\"> ";
	$s.="<input type=\"button\" id=\"bt_create_group\" name=\"bt_create_group\" value=\"Create Group\" onclick=\"javascript:settingCmd('create_group');\"> ";
	if ($row=mysqli_fetch_row($rs)){
		if (trim($row[0])!=""){
			$s="<input type=\"text\" name=\"nama_group\" value=\"".$row[0]."\" style=\"border:1px solid;\"> ";
			$s.="<input type=\"hidden\" name=\"old_nama_group\" value=\"".$row[0]."\"> ";
			$s.="<input type=\"button\" id=\"bt_rename_group\" name=\"bt_rename_group\" value=\"Rename Group\" onclick=\"javascript:settingCmd('create_group');\"> ";
		}
	}
	echo $s;
}

function groupExist($nama_group){
	$sql="select * from group_ujian where upper(nama_group)=upper('".$nama_group."')";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	$rows=mysqli_num_rows($rs);
	if ($rows==0){
		return false;
	}else{
		return true;
	}
}

function createGroup($kode_ujian,$nama_group){
	if (groupExist($nama_group)){
		return 1;
	}else{
		$sql="select * from group_ujian where kode_ujian='".$kode_ujian."'";
		$rs=mysqli_query($GLOBALS['link'],$sql);
		$rows=mysqli_num_rows($rs);
		if ($rows==0){
			$sql="insert into group_ujian(kode_ujian,nama_group) values ('".$kode_ujian."','".$nama_group."')";
		}else{
			$sql="update group_ujian set nama_group='".$nama_group."' where kode_ujian='".$kode_ujian."'";
		}
		mysqli_query($GLOBALS['link'],$sql);
		return 0;
	}
}

function selectAdminUjian_frm() {
	global $admin_id;
	$sql="select id,fname from users where grp='test admin'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	echo "<select id=\"admin_id\" name=\"admin_id\">";
	while ($row = mysqli_fetch_row($rs)){
		echo "<option value=\"".$row[0]."\"";
		if ($row[0]==$admin_id){
			echo " selected ";
		}
		echo ">".$row[1]."</option>";
	}
	echo "</select>";
}

function selectKelas_frm(){
	global $kelas;	
	$sql="select kelas from kelas order by kelas";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	echo "<select id=\"kelas\" name=\"kelas\">";
	while ($row = mysqli_fetch_row($rs)){
		echo "<option value=\"".$row[0]."\"";
		if ($row[0]==$kelas){
			echo " selected ";
		}
		echo ">".$row[0]."</option>";
	}
	echo "</select>";
}
function print_form_reload($act=""){
	echo "<form name=\"f_reload\" method=\"post\" action=\"".$act."\">";
	echo "<input type=\"hidden\" name=\"cmd\" value=\"reload\">";
	echo "</form>";	
}
function kode_soal_exist($kode_soal){
	$sql="select * from kode_soal where kode_soal='".$kode_soal."'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	if (mysqli_num_rows($rs)==0){
		return false;
	}else{
		return true;
	}
}

function uploaded(){
	$errmsg="";
	$file1=$_FILES["file1"]["name"];
	$outfilename="";
	if ($file1!=""){
		$temp = explode(".", $file1);
		for ($ofn=1;$ofn < count($temp); $ofn++){
			$outfilename.=$temp[$ofn-1].".";
		}
		//$outfilename.=gmdate('YmdHis') . "." . end($temp);
		$outfilename.=date('YmdHis') . "." . end($temp);
	}
	
	if ($outfilename!=""){
		$isuploaded=0;
		//$apppath=gotoAppPath();
		//$destdir=$apppath.$GLOBALS["tmp-dir"];
		$destdir='../'.$GLOBALS["tmp-dir"];
		if (!is_dir($destdir)){
			$d=mkdir($destdir);
			if (!$d){
				$errmsg.="Upload Failed! Unable to create directory!";
			}
		}
		
		if ($errmsg==""){
			$filetmp=$_FILES["file1"]["tmp_name"];
			$server_path=$destdir.$outfilename;
			$s=move_uploaded_file($filetmp,$server_path);
			if ($s){
				$isuploaded=1;
			}else{
				$errmsg.="Upload Failed!";
			}
		}
	}else{
		$errmsg.="file belum dipilih!";
	}
	if ($errmsg!=""){
		echo $errmsg;
		return "";
	}else{
		return $outfilename;
	}
}

// //tidak di pakai
// function selectMapel_frm($tampil="lname",$attr=""){
// 	global $selitem,$selkode_mapel;
// 	if (isset($_POST["kode_mapel"])){
// 		$selkode_mapel=$_POST["kode_mapel"];
// 	}
// 	$sql="select kode,".$tampil." from mapel order by ".$tampil;
// 	$rs=mysqli_query($GLOBALS['link'],$sql);
// 	$i=0;
// 	$selitem="";
// 	echo "<select id=\"kode_mapel\" name=\"kode_mapel\" ".$attr.">";
// 	while ($row = mysqli_fetch_row($rs)){
// 		$i++;
// 		if ($i==1){
// 			$selitem=$row[0];
// 		}
// 		echo "<option value=\"".$row[0]."\" ";
// 		if ($row[0]==$selkode_mapel){
// 			echo "selected";
// 			$selitem=$row[0];
// 		}
// 		echo ">".$row[1]."</option>";
// 	}
// 	echo "</select>";
// }
// //
function is_kode_mapel_exist($kode){
	$sql="select * from mapel where kode='".$kode."'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	if (mysqli_num_rows($rs)==0){
		return false;
	}else{
		return true;
	}
}

function generateKodeMapel($lname){
	$arl=explode(" ",strtoupper($lname));
	$kode="";
	for ($x=0; $x<count($arl); $x++){
		$kode.=$arl[$x][0];
		//echo $arl[$x]."-";
	}
	$y=0;
	while (is_kode_mapel_exist($kode) and $y< strlen($arl[$x-1])){
		$y++;
		$kode.=$arl[$x-1][$y];
	}
	if (is_kode_mapel_exist($kode)){
		$kode = generateCodeByPrefix($kode);
	}
	return $kode;
}

function hapusSoal($idsoal){
	$sql="delete from bank_soal where id='".$idsoal."'";
	mysqli_query($GLOBALS['link'],$sql);
}

function insert_mapel($kode,$lname,$keterangan){
	if (is_kode_mapel_exist($kode)){
		return false;
	}else{
		$sql="insert into mapel(kode,lname,keterangan) values('".sqlValue($kode)."','".sqlValue($lname)."','".sqlValue($keterangan)."')";
		mysqli_query($GLOBALS['link'],$sql);
		return true;
	}
}

function hapus_mapel($kode){
	$sql="delete from mapel where kode='".$kode."'";
	mysqli_query($GLOBALS['link'],$sql);
	$sql="delete from bank_soal where kode_mapel='".$kode."'";
	mysqli_query($GLOBALS['link'],$sql);
}

function update_mapel($kode,$lname,$keterangan){
	$sql="update mapel set lname='".sqlValue($lname)."',keterangan='".sqlValue($keterangan)."' where kode='".$kode."'";
	mysqli_query($GLOBALS['link'],$sql);
}
function list_mapel($admin_opt=true){

	if (isset($_POST["cmd"])){
		if ($_POST["cmd"]=="hapus_mapel"){
			hapus_mapel($_POST["kode_mapel"]);
		}
	}

	$sql="select kode,lname,keterangan from mapel ";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	echo "<div class=\"datagrid\">";
	echo "<table>";
	echo "<thead>";
	echo "<tr>";
	echo "<th width=\"5\">No</th>";
	echo "<th width=\"25\">Kode</th>";
	echo "<th>Mata Pelajaran</th>";
	echo "<th>Keterangan</th>";
	if ($admin_opt){
		echo "<th width=\"100\">Opsi</th>";
	}
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	$i=0;
	while ($row = mysqli_fetch_row($rs)){
		$i++;
		if ($i % 2){
			$tclass="";
		}else{
			$tclass="class=\"alt\"";
		}
		echo "<tr ".$tclass.">";
		echo "<td>".$i."</td>";
		echo "<td>".$row[0]."</td>";
		echo "<td>".$row[1]."</td>";
		echo "<td>".$row[2]."</td>";
		if ($admin_opt){
			echo "<td>";
			echo "<a href=\"javascript:ngepop('pop_mapel_edit_frm.php?kode=".$row[0]."',100,100,500,200,1);\">Edit</a>";
			echo " | ";
			echo "<a href=\"javascript:hapus_mapel('".$row[0]."')\">Hapus</a>";
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo "</div>";
	if ($admin_opt){
		//form untuk hapus mapel
		echo "<form name=\"f_l_m\" action=\"\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"cmd\" value=\"hapus_mapel\">";
		echo "<input type=\"hidden\" name=\"kode_mapel\" value=\"\">";
		echo "</form>";
		
		//untuk reload page setelah edit mapel
		print_form_reload();
		
		$js="<script language=\"javascript\"> \r\n 
				function hapus_mapel(x){\r\n
					if (confirm('Anda yakin?')){ \r\n
						document.f_l_m.kode_mapel.value=x; \r\n
						document.f_l_m.submit();
					}
				}\r\n
				</script> \r\n";
		echo $js;
	}
}

function kirimEmail($to,$subject,$text="",$fNameHTML=""){
	switch ($GLOBALS["phpmail-class"]) {
		case "PHPMailer":
			require_once(gotoAppPath().$GLOBALS["phpmail-dir"].'PHPMailerAutoload.php');
			$mail = new PHPMailer;
			$mail->isSMTP(); //pake smtp
			$mail->Host = $GLOBALS["smtp_host"];
			$mail->SMTPAuth = $GLOBALS["smtp_auth"];
			if ($GLOBALS["smtp_auth"]){
				$mail->Username = $GLOBALS["smtp_user"];
				$mail->Password = $GLOBALS["smtp_pass"];
			}
			if ($GLOBALS["smtp_connection_type"]!=""){
				$mail->SMTPSecure = $GLOBALS["smtp_connection_type"];
			}
			$mail->Port = $GLOBALS["smtp_port"];
			if (isset($GLOBALS["PHPMailer_SMTPDebug"])){
				$mail->SMTPDebug = $GLOBALS["PHPMailer_SMTPDebug"];
			}
			$mail->From = $GLOBALS["mail_from"];
			if (isset($GLOBALS["mail_from_name"])) {
				$mail->FromName = $GLOBALS["mail_from_name"];
			}
			/*
			$mail->addReplyTo('info@example.com', 'Information');
			$mail->addCC('cc@example.com');
			$mail->addBCC('bcc@example.com');
  	
			$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
			*/
			$arrTo = explode(",",$to);
			for ($i=0; $i < count($arrTo) ; $i++){
				$mail->addAddress($arrTo[$i]);
			}
			/*
			$mail->isHTML(true);
			$mail->Body    = $text;
			$mail->AltBody = $text;
			*/
			//this msgHTML($html,$basedir) method override $mail->Body, $mail->AltBody value and automatically set $mail->isHTML(true);
			$mail->msgHTML($text,gotoAppPath().$GLOBALS["mail_html_dir"]); 
			
			$mail->Subject = $subject;
			
			$r=$mail->send();
			if(!$r) {
			    echo 'Mailer Error: ' . $mail->ErrorInfo;
			}
			return $r;
		break;
		case "htmlMimeMail":
			require_once(gotoAppPath().$GLOBALS["phpmail-dir"].'htmlMimeMail.php');
			$mail = new htmlMimeMail();
			if ($GLOBALS["smtp_connection_type"]!=""){
  			$mail->smtp_params['host']=$GLOBALS["connection_type"]."://".$GLOBALS["smtp_host"];
  		}else{
  			$mail->smtp_params['host']=$GLOBALS["smtp_host"];
  		}
  		$mail->smtp_params['port']=$GLOBALS["smtp_port"];
  		$mail->smtp_params['auth']=$GLOBALS["smtp_auth"];
  		if ($GLOBALS["smtp_auth"]){
  			$mail->set_params['user']=$GLOBALS["smtp_user"];
  			$mail->set_params['pass']=$GLOBALS["smtp_pass"];
  		}
  		$mail->setFrom($GLOBALS["mail_from"]);
  		$mail->setSubject($subject);
  		if ($fNameHTML!=""){
  			$fNameHTML=gotoAppPath().$GLOBALS["mail_html_dir"].$fNameHTML;
  			$html = $mail->getFile($fNameHTML);
  			$mail->setHtml($html,'','./');
  		}else{
  			$mail->setText($text);
  		}
  		
  		$result = $mail->send(explode(",",$to),'smtp');
  		return $result;
  	break;
  }
}

function getStatusUjian(){
	global $kodeUjian,$remainingtime;
	$statusUjian="init";
	$skrg=sqlFormatDate(date("d/m/Y H:i:s"),"%d/%m/%Y %H:%i:%s");
	$sql="select status,durasi,start_time,
		timediff(".$skrg.", start_time) elasped_time
		 from jadwal_ujian where kode_ujian='".$kodeUjian."'";
	$rs=mysqli_query($GLOBALS['link'],$sql);
	if ($row=mysqli_fetch_row($rs)){
		$statusUjian=$row[0];
		$remainingtime = $row[1] * 60;
		/* if counter is server based
		if ($statusUjian=="started"){
			$art=explode(":",$row[3]);
			$elapsed=intVal($art[0]) * 60 * 60;
			$elapsed+=intVal($art[1]) * 60;
			$elapsed+=intVal($art[2]);
			$remainingtime = ($row[1]*60) - $elapsed;
			//echo $remainingtime;
		}
		*/
	}
	return $statusUjian;
}

function updatePersenLulus($kode_ujian,$persen_lulus){
	$sql="update jadwal_ujian set persen_lulus='".$persen_lulus."' where kode_ujian='".$kode_ujian."'";
	mysqli_query($GLOBALS['link'],$sql);
}
?>
