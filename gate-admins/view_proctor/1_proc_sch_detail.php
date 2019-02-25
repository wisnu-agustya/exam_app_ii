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
			<div class="panel-heading">Schedule</div>
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
        <!-- <button type="button" class="btn btn-outline btn-info btn-sm" data-toggle="modal" data-target="#addclass">Generate Token</button><br><br> -->
        <div id="output"></div>
				<?php
        // $result=showStueExam($id);
        //  echo $result;
			?>
			</div>
		</div>
	</div>
</div>
<!--======================-->
<script src='../assets/js/jquery-1.12.0.min.js'></script>
<script>
	$(document).ready(function(){
		var id = "<?php echo ($dat[0])?>";
		function data() {
      $.ajax({
        type : 'GET',
        url  : 'view_proctor/1_proc_datastarted.php', //Here you will fetch records 
        data : {id:id},   //Pass $id
        success : function(data){
          document.getElementById("data_in").innerHTML=data;
        }
      });
    }
		data();
    setInterval(function() {
      data(); 
    },5000);
	});
</script>
<script>
	$(document).ready(function(){
		var idg = "<?php echo ($dat[0])?>";
		function datapar() {
      $.ajax({
        type : 'GET',
        url  : 'view_proctor/1_proc_dataparticipant.php', //Here you will fetch records 
        data : {id:idg},   //Pass $id
        success : function(data){
          $('#output').html(data);
        }
      });
    }
		datapar();
    setInterval(function() {
      datapar(); 
    },5000);
	});
</script>
<script>
	$(document).ready(function() {
		$('#tbStudents').DataTable({});
	});
</script>

<!--/.row-->