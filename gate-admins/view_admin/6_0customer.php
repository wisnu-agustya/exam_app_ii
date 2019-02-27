<?php
	if (isset($_POST['cmd'])) {
		switch ($_POST['cmd']) {
			case 'Submit':  
				$today = date("Ymd");
				$name = $_POST['name'];
				$address = $_POST['address'];
				$phone = $_POST['phone'];
				$email = $_POST['email'];
				$result = $_POST['vires'];
				$type = explode('.',$_FILES['logo']['name']);
				$namaFile = "logo-".$name.$today.".".$type[1];
				$namaSementara = $_FILES['logo']['tmp_name'];
				$dirUpload = "../assets/img/logo/";
				$terupload = move_uploaded_file($namaSementara, $dirUpload.$namaFile);
				if ($terupload) {
						addCustomer($name,$address,$phone,$email,$namaFile,$result);
				} else {
						echo "Upload Gagal!";
				}
				break;
			case 'Update':
				$id = $_POST['id'];
				$name = $_POST['name'];
				$address = $_POST['address'];
				$phone = $_POST['phone'];
				$email = $_POST['email'];
				$result = $_POST['vires'];
				if ($_FILES['logo']['name']!=null) {
					$type = explode('.',$_FILES['logo']['name']);
					$namaFile = "logo-".$name.$today.".".$type[1];
					$namaSementara = $_FILES['logo']['tmp_name'];
					$dirUpload = "../assets/img/logo/";
					$terupload = move_uploaded_file($namaSementara, $dirUpload.$namaFile);
				} else {
					$namaFile=$_POST['logo_lama'];
				}
				updateCustomer($id,$name,$address,$phone,$email,$namaFile,$result);
				
				break;
			case 'Delete':
				$id = $_POST['id'];
				deleteCustomer($id);
				break;
			case 'Add PIC':  
				$id = $_POST['id'];
				$name = $_POST['name'];
				$address = $_POST['address'];
				$phone = $_POST['phone'];
				$email = $_POST['email'];
				  $result = addPIC($id,$name,$address,$phone,$email);
				  if ($result!=null) {
				  	echo "	<div class=\"col-lg-12\">
								<h4><br></h4>
							</div>
				  		  <div class=\"col-md-4\">
						   <div class=\"panel panel-warning\">
							<div class=\"panel-heading\">Register Success</div>
							  <div class=\"panel-body\">
								<table>
									<tr>
										<td style=\"width:100px;\"><b> User_Name  </b></td>
										<td style=\"width:10px;\"> : </td>
										<td> ".$result['id_user']."</td>
									</tr>
									<tr>
										<td><b> Password  </b></td>
										<td> : </td>
										<td> ".$result['pass']."</td>
									</tr>
								</table>
							  </div>
							</div>
						   </div>
						  </div>";
						  die();
				  }
				break;
			default:
				# code...
				break;
		}
	}
?>
<div class="row">
  <div class="col-lg-12">
    <h4><?php echo $_POST['cmd'] ;?></h4>
  </div>
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">Manage Custommer</div>
      <div class="panel-body">
        <table class="table table-bordered table-xs" id="tbCustomer">
          <thead>
            <tr>
              <th scope="col">No.</th>
              <th scope="col">Name</th>
              <th scope="col">Phone</th>
              <th scope="col">Email</th>
              <th scope="col">Option</th>
            </tr>
          </thead>
          <tbody>
          <?php
					$no = 1;
					$result = showCustomer();
					while ($row = mysqli_fetch_array($result)) {
						if ($row[0] != 'C0000') {
					echo '
					<tr>
					<td>' . $no . '</td>
					<td>' . $row[1] . '</td>
					<td>' . $row[3] . '</td>
					<td>' . $row[4] . '</td>
					<td>
						<a href="?pg=man_voucher&CC=' . $row[0] . '" class="btn btn-xs btn-success"><i class="fa fa-tasks" aria-hidden="true"></i> Programs</a>
						<a href="?pg=man_report&CC=' . $row[0] . '" class="btn btn-xs btn-primary"><i class="fa fa-flag-o" aria-hidden="true"></i> Report</a>
					</td>
					</tr>';
					$no++;}
					}?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src='../assets/js/jquery-1.12.0.min.js'></script>
<script>
$(document).ready(function() {
  $('#tbCustomer').DataTable();
});
</script>
<script>
$(document).ready(function() {
  $('#edit_cust').on('show.bs.modal', function(e) {
    var rowid = $(e.relatedTarget).data('id');
    $.ajax({
      type: 'get',
      url: 'view_admin/1_edit_customer.php', //Here you will fetch records 
      data: 'id=' + rowid, //Pass $id
      success: function(data) {
        $('.fetched-data').html(data); //Show fetched data from database
      }
    });
  });
});
$(document).ready(function() {
  $('#delete_cust').on('show.bs.modal', function(e) {
    var rowid = $(e.relatedTarget).data('id');
    $.ajax({
      type: 'get',
      url: 'view_admin/1_delete_customer.php', //Here you will fetch records 
      data: 'id=' + rowid, //Pass $id
      success: function(data) {
        $('.fetched-data').html(data); //Show fetched data from database
      }
    });
  });
});
$(document).ready(function() {
  $('#add_pic').on('show.bs.modal', function(e) {
    var rowid = $(e.relatedTarget).data('id');
    $.ajax({
      type: 'get',
      url: 'view_admin/1_add_pic.php', //Here you will fetch records 
      data: 'id=' + rowid, //Pass $id
      success: function(data) {
        $('.fetched-data').html(data); //Show fetched data from database
      }
    });
  });
});
</script>