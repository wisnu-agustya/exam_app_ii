<?php
//test
//test seng bla bla bla
//njajal maneh //123156986215
//revisi 4 //kjkokoi
include "/cfg/general.php";
include "/control/inc_function.php";
include "/control/inc_function2.php";
connectdb();
// iki loo
function generateQuestion($program_id,$exam_code,$student_id){
	//get komposisi
	$sql = "select a.sum_question,a.duration,a.margin,b.id_subject,b.percent,c.`level` from programs a 
			inner join program_detail b on a.id_program=b.id_program 
			inner join subject_ls c on b.id_subject=c.id_subject
			where a.id_program='".sqlValue($program_id)."'";
	$rs=mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
		$i=0;
	while ($row=mysqli_fetch_row($rs)) {
		$sum_question=$row[0];
		$duration=$row[1];
		$margin=$row[2];
		$id_subject[$i]=$row[3];
		if ($i==0) {
			$jumlah[$i]=ceil($row[4]/100*$sum_question);
			$level[$i]=$row[5];
		} else {
			$jumlah[$i]=floor($row[4]/100*$sum_question);
			$level[$i]=$row[5];
		}
		$i++;
	}
	//random soal
		$no_quest=1;
	foreach ($id_subject as $key => $value) {
		$sql = "select * from exam_source a where a.id_subject ='".sqlValue($value)."' order by RAND() limit $jumlah[$key] ";
		$rs=mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
		while ($row=mysqli_fetch_row($rs)) {
	//random jawaban		
			$kunci[1]="A";
			$kunci[2]="B";
			$kunci[3]="C";
			$kunci[4]="D";
			$kunci[5]="E";
			$field_key[4]="A";
			$field_key[5]="B";
			$field_key[6]="C";
			$field_key[7]="D";
			$field_key[8]="E";
				$idxs = range(4, 8);
				shuffle($idxs);
				$i=1;
				foreach ($idxs as $idx) {
		  	  	$jawab[$i] = $row[$idx];
		  	  	  	if (strtoupper($row[9])==$field_key[$idx]){
			  	  		$kunci_new=$kunci[$i];
			  	  		$true=$idx;
			  	  	} 
		  	  		$i++;
				}
	//insert soal run
			$sql = "INSERT INTO `exam_run_quest`(`group_name`, `id_student`, `no_quest`, `id_quest`, `question`, `val_a`, `val_b`, `val_c`, `val_d`, `val_e`, `val_key`, `grade`, `subject`) VALUES
				($exam_code,'".sqlValue($student_id)."',$no_quest,$row[0],'".sqlValue($row[3])."','".$jawab[1]."','".$jawab[2]."','".$jawab[3]."','".$jawab[4]."','".$jawab[5]."','".$kunci_new."',$level[$key],'".sqlValue($id_subject[$key])."')";
			$no_quest++;
			$insert_quest=mysqli_query($GLOBALS['link'],$sql) or die('insert soal gagal :'.mysqli_error($GLOBALS['link']));
		}
	}
	
}

function cekSession($margin,$student_id){
	$sql="select id_session from exam_session where id_student like '".sqlValue($student_id)."%'";
	$rs=mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
	if (mysqli_num_rows($rs)>0) {
		 //select session null id urutan index
	} else {
		for ($i=1; $i < $margin ; $i++) { 
			//insert session senuai margin nim.$i
		}
		//return session id null nomer index 1
	}
	//cek session existing
	//jika sudah ada dan ada session ujian ke2 yang kosong maka tidak mengurangi voucher
}
function sessionUjian($token,$student_id){
	$start_time = date('H:i:s');

	$sql = "INSERT INTO `exam_session`(`id_session`, `id_student`, `start_time`, `end_time`, `token`) VALUES
				($exam_code,'".sqlValue($student_id)."',$no_quest,$row[0],'".sqlValue($row[3])."','".$jawab[1]."','".$jawab[2]."','".$jawab[3]."','".$jawab[4]."','".$jawab[5]."','".$kunci_new."',$level[$key],'".sqlValue($id_subject[$key])."')";
			$insert_session=mysqli_query($GLOBALS['link'],$sql) or die('insert soal gagal :'.mysqli_error($GLOBALS['link']));
	//record session user, start time, magin exam dan kesempatan ujian
}
function useVoucher($voucher_id,$exam_code,$student_id){
	//record history Voucher
}
	//sessionUjian('123','2394248232');
 	//generateQuestion('P0001',1,'2394248232');
	print (examStudentLogin('165050109111027','V9MR98'));
	echo "<br>";
	print_r($_SESSION["user_group"]);

?>
<form action="" method="POST" >
	<input name="program_id" value="<?=$row[3]?>">
	<input name="exam_group" value="<?=$row[0]?>">
	<input name="participant_id" value="<?=$row[1]?>">
	<input name="cmd" value="start">
</form>
	<a href="logout.php"><em class="fa fa-toggle-off">&nbsp;</em>Logout</a>