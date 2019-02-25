<?php
	$program_id  = $_GET['CC'];
	$amn = 10;
if (isset($_POST['cmd'])) {
  switch ($_POST['cmd']) {
		case 'Create Report':
	  	$id_g  = $_SESSION['admin_group'];
      $_SESSION['xn'] = $_POST['name'];
			$_SESSION['xd1'] = $_POST['date_1'];
			$_SESSION['xd2'] = $_POST['date_2'];
			$_SESSION['prog'] = $_POST['program'];
			$_SESSION['report'] = "report";
			$_SESSION['par'] = "true";
			savingLog($_POST['name'],$_POST['date_1'],$_POST['date_2'],$_POST['program']);
			unset($_POST['cmd']);
			header('location:dashboard.php?pg=pic_report');
			break;
		case 'Export to Spreadsheet':
			header('location:view_pic/4_pic_export.php?idg='.$_SESSION['admin_group'].'&name='.$_SESSION['xn'].'&d1='.$_SESSION['xd1'].'&d2='.$_SESSION['xd2'].'&pro='.$_SESSION['prog'].'');
			break;
		case 'Reset':
			$_SESSION['report'] = "";
			$_SESSION['xn'] = "";
			$_SESSION['xd1'] = "";
			$_SESSION['xd2'] = "";
			$_SESSION['prog'] = "";
			echo "<script>window.location.href = window.location.href;</script>";
			break;
			case 'Go Last Report':
			$_SESSION['report'] ='';
				break;
			case 'amount':
				$_SESSION['amount'] = $_POST['amn'];
 				break;
    default:
			# code...
      break;
	}
}
$datareport ='
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-info-costum">
			<div class="panel-body">
				<form role="form" method="post" >
					<div class="col-md-5">
						<div class="row">
							<div class="input-group">
								<span class="input-group-addon">View Entries</span>
								<input type="text" class="form-control" placeholder="Amount" name="amn" value="'.$_SESSION['amount'].'">
								<span class="input-group-btn">
									<button class="btn btn-warning btn-sm" type="submit" name="cmd" value="amount">Submit</button>
								</span>
							</div>
						</div>
					</div>
				</form>
				<br><br>
				<table class="table table-xs"  id="tblastreport">
					<thead>
					<tr>
						<th scope="col">No</th>
						<th scope="col">Date</th>
						<th scope="col">Note</th>
						<th scope="col">Creator</th>
						<th scope="col">Option</th>
					</tr>
					</thead>
					<tbody>';
					if (isset($_SESSION['amount'])) {
						$amn = $_SESSION['amount'];
					}
					$data = lastReport($amn);
					$no = 1 ;
					while ($row = mysqli_fetch_array($data)) {
					$datareport .= '
					<tr>
						<td>'.$no.'</td>
						<td>'.date("d-M-Y",strtotime($row[1])).'</td>
						<td>'.$row[2].'</td>
						<td>'.$row[3].'</td>
						<td><a href="view_pic/4_pic_export.php?idg='.$_SESSION['admin_group'].'&name='.$row[2].'&d1='.$row[4].'&d2='.$row[5].'&pro='.$row[6].'"><button class="btn btn-success btn-xs">Download</button></a></td>
					</tr>';
					$no++;
					}
$datareport .='
					</tbody>
				<table>
				';
$datareport .='							
			</div>
		</div>
	</div>
</div>';
?>
<style>
#tb1
table, td, th {  
  border: 1px solid #ddd;
	text-align: center;
	font-size: 12px;
}
table {
  border-collapse: collapse;
  width: 100%;
}
#tb1 th{
	background-color: #1769aa;
	color :#fff;
}
#tb1 th,td {
  padding: 2px;
}
</style>
<div class="row">
	<div class="col-md-12">
		<h4></h4>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-body tabs">
				<div class="panel-heading">Reports</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-4">
							<div class="panel panel-info-costum">
								<div class="panel-body">
									<label for="">Category</label>
									<ul class="nav nav-pills nav-stacked" style="padding-left: 0px;padding-right: 0px;">
										<li role="presentation" class="active"><a href="#tab1" data-toggle="tab">Programs</a></li>
										<li role="presentation"><a href="#tab2" data-toggle="tab">Group</a></li>
										<li role="presentation"><a href="#tab3" data-toggle="tab">Student</a></li>
									</ul>
									<br>
									<?php
										if($_SESSION['report']=="report"){
											echo'<form method="post">
											<input name="cmd" type="submit" class="btn btn-primary btn-xs" value="Export to Spreadsheet">
											<input name="cmd" type="submit" class="btn btn-danger btn-xs" value="Reset">
											</form>';
										}
									?>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="panel panel-info-costum">
								<div class="panel-body">
									<label for="">Filter</label>
									<div class="tab-content" style="padding-left: 0px;padding-right: 0px;padding-top: 0px;padding-bottom: 0px;">
										<!-- 0 -->
										<div class="tab-pane fade in active" id="tab1">
											<form action="">
												<h5>Date Start</h5>
												<div class="controls input-append date form_date2"  data-date-format="yyyy-mm-dd" data-link-field="dtp_input1">
													<input type="hidden" id="dtp_input1" value="" required="" />
													<input class="form-control" name="date_1" placeholder="YYYY-MM-DD" required readonly>
													<span class="add-on"><i class="icon-remove"></i></span>
													<span class="add-on"><i class="icon-th"></i></span>
												</div>
												<h5>Date End</h5>
												<div class="controls input-append date form_date2"  data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" >
													<input type="hidden" id="dtp_input2" value="" required="" />
													<input class="form-control" name="date_2" placeholder="YYYY-MM-DD" required readonly> 
													<span class="add-on"><i class="icon-remove"></i></span>
													<span class="add-on"><i class="icon-th"></i></span>
												</div>
												<h5>Programs</h5>
												<select name="program" id="" class="form-control input-sm" style="width:60%;">
													<option value=1>All</option>
													<?php $result=showAllProgram();
													while($dat = mysqli_fetch_array($result)){
														echo "<option value='".$dat[0]."'>".$dat[1]."</option>";
													}?>
												</select>
												<h5>Notes</h5>
												<input type="text" name="name" id="" class="form-control input-sm" style="width:60%;">
												<br><p>
												<input type="reset" name="reset" class="btn btn-warning btn-sm" value="Reset">
												<button type="submit" name="cmd" class="btn btn-info btn-sm" value="r_program">Create Report</button></p>
											</form>
										</div>
										<!-- 1 -->
										<div class="tab-pane fade" id="tab2">
											<h4>Tab 2</h4>
											<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec eget rutrum purus. Donec hendrerit ante ac metus sagittis elementum. Mauris feugiat nisl sit amet neque luctus, a tincidunt odio auctor.</p>
										</div>
										<!-- 2 -->
										<div class="tab-pane fade" id="tab3">
											<h4>Tab 3</h4>
											<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec eget rutrum purus. Donec hendrerit ante ac metus sagittis elementum. Mauris feugiat nisl sit amet neque luctus, a tincidunt odio auctor.</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- ============================================================================================================== -->
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">Report</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-info-costum">
							<div class="panel-body">
								<form role="form" method="post" >
									<div class="form-group">
										<div class="row">
											<div class="col-md-2">
											<label for="">Date Start</label>
											</div>
											<div class="col-md-4">
											<div class="controls input-append date form_date2"  data-date-format="yyyy-mm-dd" data-link-field="dtp_input1">
												<input type="hidden" id="dtp_input1" value="" required="" />
												<input class="form-control" name="date_1" placeholder="YYYY-MM-DD" required readonly>
												<span class="add-on"><i class="icon-remove"></i></span>
												<span class="add-on"><i class="icon-th"></i></span>
											</div>
											</div>
											<div class="col-md-2">
											<label for="">Date End</label>
											</div>
											<div class="col-md-4">
											<div class="controls input-append date form_date2"  data-date-format="yyyy-mm-dd" data-link-field="dtp_input2" >
												<input type="hidden" id="dtp_input2" value="" required="" />
												<input class="form-control" name="date_2" placeholder="YYYY-MM-DD" required readonly> 
												<span class="add-on"><i class="icon-remove"></i></span>
												<span class="add-on"><i class="icon-th"></i></span>
											</div>
											</div>
										</div><br>
										<div class="row">
											<div class="col-md-2">
												<label for="">Programs</label>
											</div>
											<div class="col-md-8">
											<select name="program" id="" class="form-control input-sm" style="width:60%;">
												<option value=1>All</option>
												<?php $result=showProgram();
												while($dat = mysqli_fetch_array($result)){
													echo "<option value='".$dat[0]."'>".$dat[1]."</option>";
												}?>
											</select>
											</div>
										</div><br>
										<div class="row">
											<div class="col-md-2">
												<label for="">Notes</label>
											</div>
											<div class="col-md-8">
												<input type="text" name="name" id="" class="form-control input-sm" style="width:60%;">
											</div>
										</div>
									</div>
									<br>
									<div class="row">
										<div class="col-md-6">
											<input name="cmd" type="submit" class="btn btn-primary btn-xs" value="Create Report">
											<button name="cmd" type="reset" value="Cancel" class="btn btn-warning btn-xs">Cancel</button>
								</form>
										</div>
										<div class="col-md-6">
											<span class="pull-right">
											<?php
												if($_SESSION['report']=="report"){
													echo'<form method="post">
													<input name="cmd" type="submit" class="btn btn-primary btn-xs" value="Export to Spreadsheet">
													<input name="cmd" type="submit" class="btn btn-danger btn-xs" value="Reset">
													</form>';
												}
											?>
											</span>
										</div>
									</div>									
							</div>
						</div>
						<?php
							if ($_SESSION['report']==true) {
								$table = tableReport($id_g,$_SESSION['xn'],$_SESSION['xd1'],$_SESSION['xd2'],$_SESSION['prog']);
								if ($table == '0') {
									echo("<b>Data not available.</b><form method='post'><input name='cmd' type='submit' class='btn btn-warning btn-xs' value='Go Last Report'></form>");
								}else {
									echo $table;
								}								
							}else{
								echo $datareport;
							}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- MODAL Create report -->

<!--======================-->
<script src='../assets/js/jquery-1.12.0.min.js'></script>
<!--======================-->
<!-- Extra JS for Time Picker -->
<script type="text/javascript" src="../assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../assets/js/bootstrap-datetimepicker.id.js" charset="UTF-8"></script>
<script>
	$(document).ready(function() {
		$('#tblastreport').DataTable({
			"scrollY":"300px",
			"scrollCollapse": true,
			"paging":false,
			"info":false,
      "columnDefs":[
        {"width": "2%", "targets":0},
        {"width": "10%", "targets":1},
        {"width": "10%", "targets":3},
        {"width": "7%", "targets":4},
      ]
    });
	});
</script>
<script type="text/javascript">
   $('.form_date1').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0,
    }); 
   $('.form_date2').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
</script>