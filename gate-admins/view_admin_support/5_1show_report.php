<style type="text/css">
#tbl th {
  background-color: #1769aa;
  color: #fff;
}
table, td, th {  
  border: 1px solid #565656;
	text-align: center;
	font-size: 12px;
}
table {
  border-collapse: collapse;
  width: 100%;
}
th,td {
  padding: 2px;
}
</style>
<?php
if (isset($_POST['cmd'])) {
	switch ($_POST['cmd']) {
		case 'customer':
			$start = $_POST['date_start'];
			$end = $_POST['date_end'];
			$by = $_POST['by'];
			$customer = $_POST['customer'];
			$result = admRpt_Cust($start,$end,$by,$customer);
			echo "
			<div class=\"row\">
				<div class=\"col-lg-12\">
					<h1></h1>
				</div>
				<div class=\"col-lg-12\">

			";
/*By Vouc*/	if ($by=='Voucher') {
			$header = array('No','Customer Name','Id Voucher','Id Program','Latest Top Up','Available','Use','Top Up' );
				echo "
					<table id=\"tbl\" class=\"table table-bordered\">
						<tr>
							<th>No</th>
							<th>Customer Name</th>
							<th>Id Voucher</th>
							<th>Id Program</th>
							<th>Latest Top Up</th>
							<th>Available</th>
							<th>Use</th>
							<th>Top Up</th>
						</tr>
				";
				$grup = 0;
				$no = 1;
				$param = 'Report Customer By Voucher';
				while ($row = mysqli_fetch_row($result)) {
					$field[0][$no] = array($row[1],$row[2],$row[3],$row[5],$row[4],$row[6],$row[7]);
					echo "
					<tr>
						<td>$no</td>
						<td>".$row[1]."</td>
						<td>".$row[2]."</td>
						<td>".$row[3]."</td>
						<td>".$row[5]."</td>
						<td>".$row[4]."</td>
						<td>".$row[6]."</td>
						<td>".$row[7]."</td>
					</tr>
					";
					$no++;
				}	
				echo "</table>";	  
/*By exam*/ } else if ($by=='Exam') {
				$header = array('No','Customer Name','Id Exam Group','Date','Id Classroom','Proctor','Alocated','Participant','Voucher Cancel' );
				 echo "
					<table id=\"tbl\">
						<tr>
							<th>No</th>
							<th>Customer Name</th>
							<th>Id Exam Group</th>
							<th>Date</th>
							<th>Id Classroom</th>
							<th>Proctor</th>
							<th>Alocated </th>
							<th>Participant</th>
							<th>Voucher Cancel</th>
						</tr>
				";
				$grup = 0;
				$no = 1;
				$param = 'Report Customer By Exam';
				while ($row = mysqli_fetch_row($result)) {
					$field[$grup][$no] = array($row[1],$row[2],$row[3],$row[5],$row[4],$row[6],$row[7],$row[8],$row[9] );
					echo "
					<tr>
						<td>$no</td>
						<td>".$row[1]."</td>
						<td>".$row[2]."</td>
						<td>".$row[3]."</td>
						<td>".$row[5]."</td>
						<td>".$row[4]."</td>
						<td>".$row[6]."</td>
						<td>".$row[7]."</td>
						<td>".$row[8]."</td>
						<td>".$row[9]."</td>
					</tr>
					";
					$no++;
				}		  
				echo "</table>";
/*By Prog*/	} else if ($by=='Program') {
			$header = array('No','Customer Name','Program Name','Exams','Pass Rates' );
		 	echo "
				<table id=\"tbl\">
					<tr>
						<th>No</th>
						<th>Customer Name</th>
						<th>Program Name</th>
						<th>Exams</th>
						<th>Pass Rates </th>
					</tr>
			";
			$grup = 0;
			$no = 1;
			$param = 'Report Customer By Program';
			while ($row = mysqli_fetch_row($result)) {
			$sql1 = 
			"select (sum(AA.kelulusan)/count(*)*100)kelulusan from (
				select if(sum(a.percentage_true)/sum(a.percentage_true+a.percentage_false+a.percentage_null)*100>=c.pass_grade,1,0) kelulusan 
				from exam_percentage a 
				inner join exam_group b on a.exam_code=b.exam_code 
				inner join programs c on SUBSTRING_INDEX(SUBSTRING_INDEX(group_name,'.',2),'.',-1) = c.id_program 
				where b.exam_code in (".$row[0].") 
				group by a.id_student,a.exam_code 
			)AA";
			$res = mysqli_query($GLOBALS['link'],$sql1) or die(mysqli_error($GLOBALS['link']));
				while ($row1 = mysqli_fetch_row($res)) {
					$field[$grup][$no] = array($row[2],$row[1],$row[3],number_format($row1[0],2)."%" );
					echo "
					<tr>
						<td>$no</td>
						<td>".$row[2]."</td>
						<td>".$row[1]."</td>
						<td>".$row[3]."</td>
						<td>".number_format($row1[0],2)."%</td>
					</tr>
					";
					$no++;
				}	
			}	
			echo "</table>";  
		  }
				
			
			break;
		case 'voucher':
			$start = $_POST['date_start'];
			$end = $_POST['date_end'];	
			$voucher = $_POST['voucher'];	
			//print_r($start.'<br>'.$end.'<br>'.$voucher);
			$result = admRpt_Voucher($start,$end,$voucher);
			echo "
			<div class=\"row\">
				<div class=\"col-lg-12\">
					<h1></h1>
				</div>
				<div class=\"col-lg-12\">

			";
			$header = array('No','Id Customer','Customer Name','Program Name','Exam Group','Date Time','Alocated','Use','Cancel','Pass Rates' );
			echo "
				<table id=\"tbl\" >
					<tr>
						<th>No</th>
						<th>Id Voucher</th>
						<th>Customer Name</th>
						<th>Program Name</th>
						<th>Exam Group</th>
						<th>Date Time</th>
						<th>Alocated</th>
						<th>Use</th>
						<th>Cancel</th>
						<th>Pass Rates </th>
					</tr>
			";
			$grup = 0;
			$no = 1;
			$param = 'Report Voucher Customer';
			while ($row = mysqli_fetch_row($result)) {
			$sql1 = 
			"select (sum(AA.kelulusan)/count(*)*100)kelulusan from (
				select if(sum(a.percentage_true)/sum(a.percentage_true+a.percentage_false+a.percentage_null)*100>=c.pass_grade,1,0) kelulusan 
				from exam_percentage a 
				inner join exam_group b on a.exam_code=b.exam_code 
				inner join programs c on SUBSTRING_INDEX(SUBSTRING_INDEX(group_name,'.',2),'.',-1) = c.id_program 
				where b.exam_code = '".$row[7]."' 
				group by a.id_student,a.exam_code 
			)AA";
			$res = mysqli_query($GLOBALS['link'],$sql1) or die(mysqli_error($GLOBALS['link']));
				while ($row1 = mysqli_fetch_row($res)) {
					$field[$grup][$no] = array($row[0],$row[1],$row[2],$row[3],$row[8],$row[4],$row[5],$row[6],number_format($row1[0],2)."%" );
					echo "
					<tr>
						<td>$no</td>
						<td>".$row[0]."</td>
						<td>".$row[1]."</td>
						<td>".$row[2]."</td>
						<td>".$row[3]."</td>
						<td>".$row[8]." ".$row[9]."</td>
						<td>".$row[4]."</td>
						<td>".$row[5]."</td>
						<td>".$row[6]."</td>
						<td>".number_format($row1[0],2)."%</td>
					</tr>
					";
					$no++;
				}	
			}	 
				echo "</table>";

			break;
		case 'exam':
			$start = $_POST['date_start'];
			$end = $_POST['date_end'];
			$exam = $_POST['exam'];
			$param = 'Report Voucher Exam';
			echo "
			<div class=\"row\">
				<div class=\"col-lg-12\">
					<h1></h1>
				</div>
				<div class=\"col-lg-12\">

			";
				$field = array();
			foreach ($exam as $key => $value) {
				$result = admRpt_Exam($start,$end,$value);
				$header=array('No','Student Name','Start Exam','End Exam','True Answer','Percentage','Status');
				echo "
				
				<table id=\"tbl\">
					<h4> Exam Group : ".$value."</h4>
					<tr>
						<th>No</th>
						<th style='width:38%;'>Student Name</th>
						<th style='width:170px;'>Start Exam</th>
						<th style='width:170px;'>End Exam</th>
						<th>True Answer</th>
						<th style='width:105px;'>Percentage</th>
						<th style='width:70px;'>Status</th>
					</tr>
				";
				$no = 1;
				while ($row = mysqli_fetch_row($result)) {
					if ($row[6]>=$row[7]) {
						$status = "<b style='color:green;'>Lulus</b>";
						$status2 = "Lulus";
					}else{
						$status = "<b style='color:red;'>Tidak Lulus</b>";
						$status2 = "Tidak Lulus";
					}
					$field[$value][$no] = array($row[1],$row[2],$row[3],$row[4],number_format($row[6],2).'%',$status2);
					echo "
						<tr>
							<td>$no</td>
							<td>".$row[1]."</td>
							<td>".$row[2]."</td>
							<td>".$row[3]."</td>
							<td>".$row[4]."</td>
							<td>".number_format($row[6],2)."%</td>
							<td>".$status."</td>
						</tr>
						";
						$no++;
				}
				echo "</table>";
			}
			break;
		case 'program':
			$start = $_POST['date_start'];
			$end = $_POST['date_end'];
			$program = $_POST['program'];
			print_r($start.'<br>'.$end.'<br>'.$program);
			break;
		case 'voucher_history':
			$start = $_POST['date_start'];
			$end = $_POST['date_end'];	
			$voucher = $_POST['voucher'];	
			//print_r($start.'<br>'.$end.'<br>'.$voucher);
			echo "
				<div class=\"row\">
					<div class=\"col-lg-12\">
						<h1></h1>
					</div>
					<div class=\"col-lg-12\">

				";
			foreach ($voucher as $key => $value) {
				$result = historyVoucher($value,$start,$end);
				$header = array('No','Tanggal','Description','Kredit','Debit','Saldo' );
				$no=1;
				while($row = mysqli_fetch_array($result)){
					if ($row[1]==0) {$kredit=$row[5]; $debit='';}else{$kredit=''; $debit=$row[9];}
					if ($row[1]==0) {
						$desc="(".$row[11].")  Invoice Number : ".$row[7]." / Date : ".tanggal_indo($row[8]);
					}else{
						$desc="Exam Group : ".$row[10];
					}
					$field[$value][$no] = array(date("d-M-Y H:i",strtotime($row[3])),$desc,$kredit,$debit,$row[12]);
					$no++;
				}
			}
			$param = 'Report History Voucher Customer';
			//tabel
			foreach ($field as $kode_voucher => $field_desc) {
				echo $kode_voucher;
				echo "<br>
					 <table id=\"tbl\">";
				//isinya
				foreach ($field_desc as $no_key => $isi) {
					echo "<tr>";
					//membuat header
					if ($no_key==1) {
						echo "<tr>";
						foreach ($header as $header_key => $header_value) {
							echo "<th>".$header_value."</th>";
						}
						echo "</tr>";
					}
						echo "<td>".$no_key."</td>";
					//isi komolomya
					foreach ($isi as $key => $value) {
						echo "<td>".$value."</td>";
					}

					echo "</tr>";
				}
				echo "</table>";

			}
			break;
			default:
			print_r($_POST['cmd']);
			break;
	}
}
// //tabel
// foreach ($field as $kode_ujian => $field_desc) {
// 	echo $kode_ujian;
// 	echo "<br>
// 		 <table border='1'>";
// 	//isinya
// 	foreach ($field_desc as $no_key => $isi) {
// 		echo "<tr>";
// 		//membuat header
// 		if ($no_key==1) {
// 			echo "<tr>";
// 			foreach ($header as $header_key => $header_value) {
// 				echo "<th>".$header_value."</th>";
// 			}
// 			echo "</tr>";
// 		}
// 			echo "<td>".$no_key."</td>";
// 		//isi komolomya
// 		foreach ($isi as $key => $value) {
// 			echo "<td>".$value."</td>";
// 		}

// 		echo "</tr>";
// 	}
// 	echo "</table>";

// }

$nama = json_encode($header);
$isi = json_encode($field);
?>
<br>
<a href="?pg=admin_report" class="btn btn-sm btn-danger"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Report</a>
<a class="btn btn-sm btn-primary" href='<?php echo "view_admin/help-export.php?param=".$param."&nama=".$nama."&isi=".$isi?>'><i class="fa fa-download" aria-hidden="true"></i> Download</a>
