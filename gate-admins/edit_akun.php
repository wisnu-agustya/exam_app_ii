<?php 
include "../cfg/general.php";
include "../control/inc_function.php";
include "../control/inc_function2.php";
connectdb();
$id = $_GET['id'];
$res = editUser($id);
$result = mysqli_fetch_array($res);
$id = $_SESSION["admin_group"];
?>
<div class="modal-header">
	<h4 class="modal-title">Edit Akun</h4>
</div>
<form action="" method="post">
<div class="modal-body">
	<div class="row">
		<div class="col-md-6">
				<div class="form-group">
					<label>Name</label>
					<input name="name" type="text" class="form-control" value="<?= $result[1];?>" placeholder="name lastname" id="name">
					<input name="id" type="hidden" value="<?= $result[0]; ?>">
				</div>
				<div class="form-group">
					<label>Place of Birt</label>
					<input name="pob" type="text" class="form-control" value="<?= $result[2]; ?>" placeholder="City">
				</div>
				<div class="form-group">
					<label>Date of Birth</label>
					<input name="dob" type="text" class="form-control" value="<?= $result[3]; ?>" placeholder="dd-mm-yyyy">
				</div>
				<div class="form-group">
					<label>Address</label>
					<input name="address" type="text" class="form-control" value="<?= $result[4]; ?>" placeholder="Jalan no.1">
				</div>
				<div class="form-group">
					<label>Phone</label>
					<input name="phone" type="text" class="form-control" value="<?= $result[5]; ?>" placeholder="+62852xx">
				</div>
		</div>
		<div class="col-md-6">
				<div class="form-group">
				<label>Email</label>
					<input name="email" type="text" class="form-control" value="<?= $result[6]; ?>" placeholder="someone@example.com">
				</div>
				<div class="form-group">
					<input type="hidden" name="role" value="role" >
				</div>
				<div class="form-group">
					<label>Username Login</label>
					<input name="username" type="text" class="form-control" value="<?= $result[7]; ?>" placeholder="username">
				</div>
				<div class="form-group">
					<label>Set New Password</label>
					<input name="pword" type="text" class="form-control" value="" placeholder="***">
				</div>
				<div class="form-group">
					<label>Confirm Password</label>
					<input name="pword_check" type="text" class="form-control" value="" placeholder="***">
				</div>
		</div>
	</div>
</div>	
<div class="modal-footer">
 	<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	<input type="submit" name="cmd4" class="btn btn-primary btn-sm" value="Update">
</div>
			</form>


