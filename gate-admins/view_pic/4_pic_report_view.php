<?php
$id = $_GET['CC'];
if (isset($id)) {
  $n = viewRreport($id);
}
if (isset($_POST['cmd'])) {
  switch ($_POST['cmd']) {
    case 'Submit':
      
      break;
    default:
			# code...
      break;
  }
}
?>
<div class="row">
	<div class="col-lg-12">
		<h4></h4>
	</div>
	<div class="col-md-12">
		<div class="panel panel-primary">
			<div class="panel-heading">Report View</div>
			<div class="panel-body">
				<table class="table table-xs" id="tbReport">
					<thead>
						<tr>
							<th scope="col">No</th>
							<th scope="col">Program</th>
							<th scope="col">Date</th>
							<th scope="col">Option</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$no = 1;
						$id_g = $_SESSION['admin_group'];
						$result = showReport($id_g);
						while ($row = mysqli_fetch_array($result)) {
							echo '
							<tr>
								<td>' . $no . '</td>
								<td>' . $row[5] . '</td>
								<td>' . tanggal_indo($row[2]).' - '.tanggal_indo($row[3]) . '</td>
								<td> 
								<a href="?pg=pic_report_view&CC='.$row[0].'" class="btn btn-xs btn-success"> <i class="fa fa-search-plus"></i> View</a>
								<a href="?pg=view_report&CC='.$row[0].'" class="btn btn-xs btn-Primary"> <i class="fa fa-file-excel-o"></i> Export</a>
								</td>
							</tr>';
						$no++;
						}
					?>
					</tbody>
				</table>


			</div>
		</div>
	</div>
</div>
<!-- MODAL Create report -->
<!--======================-->
<script src='../assets/js/jquery-1.12.0.min.js'></script>

  <script>
	$(document).ready(function() {
		$('#tbReport').DataTable({
			"columnDefs": [
    	{ "orderable": false, "targets": 2 }
  	]});
	});
</script>

<!--/.row-->
	
<!--======================-->
<!-- Extra JS for Time Picker -->
