<?php 
include "../../cfg/general.php";
include "../../control/inc_function.php";
include "../../control/inc_function2.php";
connectdb();

switch ($_GET['variable']) {
	case 'by_voucher':
		$id = $_GET['id'];
		$result=showVoucher($id,'');
			while($row = mysqli_fetch_array($result)){
				if ($row[0]!='C0000') {
				echo "
					<div class=\"checkbox\">
					<label>
					<input  type=\"checkbox\" id=\"voucher\" name=\"voucher[]\" value=\"$row[0]\">$row[0] - $row[2]
					</label>
					</div>
				";
			}
		}
		break;
	case 'by_exam':
		$id = $_GET['id'];
		$start = $_GET['start'];
		$end = $_GET['end'];
		if ($id=='null') {
			$sql = "SELECT a.id_schedule,b.group_name FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 	INNER JOIN users c on a.proctor=c.id_user where a.date>='$start' and a.date<='$end' ORDER BY a.id_schedule DESC";
		}else {
			$sql = "SELECT a.id_schedule,b.group_name FROM exam_schedule a INNER JOIN exam_group b on a.exam_group=b.exam_code 	INNER JOIN users c on a.proctor=c.id_user where c.cust_group ='$id' AND a.date>='$start' and a.date<='$end' ORDER BY a.id_schedule DESC";
		}
		$result = mysqli_query($GLOBALS['link'],$sql)or die(mysqli_error($GLOBALS['link']));
			while($row = mysqli_fetch_array($result)){
				if ($row[0]!='C0000') {
				echo "
					<div class=\"checkbox\">
					<label>
					<input  type=\"checkbox\" id=\"exam\" name=exam[] value=\"$row[1]\">$row[0] - $row[1]
					</label>
					</div>
				";
			}
		}
		break;
	
	default:
		# code...
		break;
}
	
?>