<?php //die('Sorry Not Ready to use');
include "../../cfg/general.php";
include "../../control/inc_function.php";
include "../../control/inc_function2.php";
connectdb();
$id = $_GET['id'];
$rs = editProgram($id);
$no = 1;
while ($row = mysqli_fetch_array($rs)) {
	$result[$no] = $row;
	$no++;
}
?>				
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h4 class="modal-title">Edit Program</h4>
</div>
<form role="form" id="edit_program" action="" method="POST">
	<div class="modal-body">
		<div class="row">
			<div class="col-lg-12">
				<div class="form-group">
					<label>Name</label>
					<input class="form-control"  name="name" value="<?= $result[1][1] ?>">
					<input class="form-control" type="hidden" name="id" value="<?= $result[1][0] ?>">
				</div>
				<div class="form-group">
					<label>Sum of Questions</label>
					<input class="form-control" type="number" name="tot" value="<?= $result[1][6] ?>">
				</div>
				<div class="form-group">
					<label>Duration</label>
					<input class="form-control" type="number"  name="duration" value="<?= $result[1][7] ?>" placeholder='On Minutes' required>
				</div>
				<div class="form-group">
					<label>Exam Opportunity</label>
					<select class="form-control" name="margin">
					<?php
						for ($i = 1; $i <= 5; $i++) {
							if ($result[1][2] == $i) {
								echo "<option value='" . $i . "' selected>" . $i . "</option>";
							} else {
								echo "<option value='" . $i . "'>" . $i . "</option>";
							}
						}
					?> 
					</select>
				</div>
			</div>
			<?php
			//loop subject
			$cnr = showSubjectList($id);
			$c = mysqli_num_rows($cnr);
			for ($i = 1; $i <= $c; $i++) {
			?>
				<div class="col-lg-10">
					<div class="form-group">
						<label>Subject <?= $i ?></label>
						<select class="form-control" name="subject[]">
						<?php
						$rs = showSubjectList($id);
						while ($row = mysqli_fetch_array($rs)) {
							if ($result[$i][3] == $row[0]) {
								echo "<option value='" . $row[0] . "'selected>" . $row[1] . "</option>";
							} else {
								echo "<option value='" . $row[0] . "'>" . $row[1] . "</option>";
							}
						}
						?> 
						</select>
					</div>
				</div>
				<div class="col-lg-2">
					<div class="form-group">
						<label>Percent</label>
						<input class="form-control"  name="percent[]" value="<?= $result[$i][4] ?>">
						<input class="form-control" type="hidden"  name="no_id[]" value="<?= $result[$i][5] ?>">
					</div>
				</div>
				<?php
				}
				?>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
		<input type="submit" name="cmd" class="btn btn-sm btn-success" value="Update">
	</div>
</form>

