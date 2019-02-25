<?php 
include "../../cfg/general.php";
include "../../control/inc_function.php";
include "../../control/inc_function2.php";
connectdb();
$id = $_GET['id'];
$group = $_GET['group'];
$ids = implode('.', array_slice(explode('.', $id), 0, 1));
$n = datStudent($ids);
$image = getLogo($_SESSION['admin_group']);
$logo = $image[0];
$result = 
'<table class="table table-bordered table-result">
  <tr>
    <td rowspan=2 width=270px> <img align="middle" style="max-height: 100px;" src= "../assets/img/logo/'.$logo.'"></td>
    <th>ID</th>
    <th colspan=3> '. $ids.'</th>
  </tr>
  <tr>
    <th>NAME </th>
    <th colspan=3> '.$n[1].'</th>
  </tr>
  <tr>
		<td colspan=5 style=\"height: 45px;\"><h3 style=\"margin: 0px;\">RESULT</h3></td>	
	</tr>
  <tr>
    <th rowspan="2">Subject</th>
    <th rowspan="2">Question</th>
    <th colspan="3">Answer</th>
  </tr>
  <tr>
    <th>True </th>
    <th>False </th>
    <th>Not Answer </th>
  </tr>';
foreach(resultExam($ids,$group) as $r) {
  $quest = $r[2] + $r[3] + $r[4];
  $point[] = $quest;
  $true[] = $r[2];
  $result .='
  <tr><td>'.$r[1].'</td><td>'.$quest.'</td><td>'.$r[2].'</td><td>'.$r[3].'</td><td>'.$r[4].'</td></tr>';
}
$point = array_sum ($point);
$true = array_sum ($true);
$val = ($true/$point)*100;
$n = number_format((float)$val, 2, '.', '').' %';
$conc = concResult($group,$val);
$result .='
  <tr>
    <td colspan=5 height=70px><h3 style=\"margin: 0px;\"> Test Score : '.$n.'&emsp;'.$conc.'  </h3></td>
  </tr>
</table>';
?>

<div class="modal-header">
	<h4 class="modal-title">Exam Result</h4>
</div>
<form action="" method="post">
<div class="modal-body">
  <div class="row">
    <div class="col-lg-12">
    <?php
    echo($result); 
    ?>
    </div>
  </div>
</div>	
<div class="modal-footer">
	<input type="submit" name="cmd" class="btn btn-primary btn-sm" value="Ok">
</div>
</form>
