<?php
if (isset($_GET['CC'])) {
	$id = $_GET['CC'];
	$dat = infoExam($id);
}
if (isset($_POST['cmd'])) {
	switch ($_POST['cmd']) {
		case 'Release':
			break;
		default:
			# code...
			break;
	}
}
?>
<input type="hidden" id="id" value="<?= $dat[0] ?>">
<div class="row">
	<div class="col-lg-12">
		<h4></h4>
	</div>
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading"><a href="?pg=pic_exam_result" class="btn btn-xs btn-danger"><i class="fa fa-arrow-left"></i> Back</a> &nbsp Exam Result</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-info-costum">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-3">
										<b>Token</b>
									</div>
									<div class="col-md-3">
										: <?= $dat[1] ?>
									</div>
									<div class="col-md-3">
										<b>Alocate</b>
									</div>
									<div class="col-md-3">
										: <?= $dat[2] ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<b>Program</b>
									</div>
									<div class="col-md-3">
										: <?= $dat[3] ?>
									</div>
									<div class="col-md-3">
										<b>Started</b>
									</div>
									<div class="col-md-3">
										: <span id="data_in"></span>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<b>Proctor</b>
									</div>
									<div class="col-md-3">
										: <?= $dat[4] ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
		        <span id="output"></span>
		        <table class="table table-xs" id="tbStudents">
		          <thead>
			        <tr>
						<th scope = "col" > No </th>
						<th scope = "col" > Students ID </th>
						<th scope = "col" > Name </th>
						<th scope = "col" > Start Exam </th>
						<th scope = "col" > End Exam </th>
						<th scope = "col" > Percentage </th>
					</tr>
				  </thead>
				  	<?php
				  	$no=1;
					$q = listExamParticipants($dat[0]);
					while ($dataSession = mysqli_fetch_array($q)) {
						$qry = "SELECT * FROM students a WHERE a.idstudents=".$dataSession[0]."";
						$r = mysqli_query($GLOBALS['link'],$qry) or die(mysqli_error($GLOBALS['link']).'<br>'.$qry);
						$dataStudents = mysqli_fetch_array($r);
						$dataExam = resultExamByid($dataStudents[0],$dat[0]);
						echo "
						<tr>
							<td>$no</td>
							<td>".$dataStudents[0]."</td>
							<td>".$dataStudents[1]."</td>
							<td>".$dataSession[1]."</td>
							<td>".$dataSession[2]."</td>
							<td>".number_format($dataExam[1],2)."%  (".$dataExam[0].")</td>
						</tr>
						";
					}
					?>
				</table>
			</div>
		</div>
	</div>
</div>
<!--======================-->
<script src='../assets/js/jquery-1.12.0.min.js'></script>
<script>
	$(document).ready(function() {
		$('#tbStudents').DataTable({});
	});
</script>

<!--/.row-->