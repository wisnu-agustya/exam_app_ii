<?php
	if (isset($_POST['cmd'])) {
		switch ($_POST['cmd']) {
			case 'Add Subject':
			  if ($_POST['duplicate']=='duplicate') {
			  	echo ("	<script LANGUAGE='JavaScript'>
						    window.alert(\"Duplicate Subject Name\");
						</script>");
			  }else{
			  	$name = $_POST['name'];
				$id = $_POST['id'];
				$level = $_POST['level'];
				addSubject($id,$name,$level);
			  }
				break;
			case 'Update Subject':
				$id = $_POST['id'];
				$name = $_POST['name'];
				$level = $_POST['level'];
				updateSubject($id,$name,$level);
				break;
			case 'Delete Subject':
				$id = $_POST['id'];
				$id_subject = $_POST['id_subject'];
				deleteSubject($id,$id_subject);
				break;
			case 'Delete Question':
				$id = $_POST['id'];
				deleteQuestion($id);
				break;
			case 'Update':
			 	 $_SESSION["import_id"]=date("Ymd-His");
				 $id_source = $_POST['id'];
				 $gambar_lama = $_POST['gambar_lama'];
				 $pertanyaan = $_POST['pertanyaan'];
				 $gambar = $_POST['gambar'];
				 $opsi_a = $_POST['opsi_a'];
				 $opsi_b = $_POST['opsi_b'];
				 $opsi_c = $_POST['opsi_c'];
				 $opsi_d = $_POST['opsi_d'];
				 $opsi_e = $_POST['opsi_e'];
				 $kunci  = $_POST['kunci'];
				 //upload gambar
				 $type = explode('.',$_FILES['gambar']['name']);
				 $idx= count($type);
				 $namaFile = $type[0].'-'.$_SESSION["import_id"].'.'.$type[($idx-1)];
				 $namaSementara = $_FILES['gambar']['tmp_name'];
				 $dirUpload = "../assets/img-soal/";
				 $terupload = move_uploaded_file($namaSementara, $dirUpload.$namaFile);
				 	 if ($type[1]=='') {
					 	$img_source ='<img src="'.$gambar_lama;
					 }else {
					 	$img_source="<img src=\"".$namaFile."\"><br>";
					 }
				 $sql="UPDATE exam_source SET 
				 	question='$pertanyaan $img_source',
				 	val_a = '".sqlValue($opsi_a)."',
				 	val_b = '".sqlValue($opsi_b)."',
				 	val_c = '".sqlValue($opsi_c)."',
				 	val_d = '".sqlValue($opsi_d)."',
				 	val_e = '".sqlValue($opsi_e)."',
				 	val_key = '$kunci'
				 	WHERE id = $id_source";
				 mysqli_query($GLOBALS['link'],$sql) or die(mysqli_error($GLOBALS['link']));
				 break;
			default:
				# code...
				break;
		}
	}
?>
 	<style>
		.wrong{
			border-color:red;
		}
		.true{
			border-color:green;
		}
		.zoom {
		  margin: 0 auto;
		  z-index: 999;
		}
		.zoom:hover {
		  margin-left: 140px;
		  z-index: 999;
		  transform: scale(2.5); /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
		}
    </style>
  <div class="row">
<div class="col-lg-12">
	<h4><?php echo $_POST['cmd'] ;?></h4>
</div>
<div class="col-md-12">
  <div class="panel panel-info">
	<div class="panel-heading">Question</div>
	<div class="panel-body">
		<button class="btn btn-xs btn-danger" type="button" onclick="javascript:history.back()"><i class="fa fa-arrow-left" ></i> Back</button>
	<h4>Question </h4>
			<a href="?pg=add_question" class="btn btn-primary btn-xs"><i class="fa fa-plus-square-o"></i> Add Question</a>
			<a href="?pg=import_soal&CC=<?=$_GET['CC']?>"> <button class="btn btn-success btn-xs" type="button"><i class="fa fa-upload"></i> Upload Question</button></a>
			<a href="../assets/file/Example File Upload.xls"> <button class="btn btn-warning btn-xs" type="button"><i class="fa fa-download"></i> Example Format</button></a>
			<br><br>
			<table class="table table-bordered table-xs" id="tbQuestion" style="font-size: 11px;">
				<thead>
					<tr>
						<th scope="col">No.</th>
						<th scope="col">Subject</th>
						<th scope="col">Question</th>
						<th scope="col">A</th>
						<th scope="col">B</th>
						<th scope="col">C</th>
						<th scope="col">D</th>
						<th scope="col">E</th>
						<th scope="col">Answer Key</th>
						<th scope="col">Option</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$no = 1;
						if (isset($_GET['CC'])){
							$result=showQuestion($_GET['CC']);
						} else {
							$result=showQuestion();
						}
						while($row = mysqli_fetch_array($result)){
							echo '
							<tr>
								<td>'.$no.'</td>
								<td>'.$row[10].'</td>
								<td>'.str_replace("<img src=\"","<img style=\"max-width:230px;\" src=\"../".$GLOBALS["img-soal-dir"],$row[3]).'</td>
								<td>'.$row[4].'</td>
								<td>'.$row[5].'</td>
								<td>'.$row[6].'</td>
								<td>'.$row[7].'</td>
								<td>'.$row[8].'</td>
								<td>'.$row[9].'</td>
								<td style="width:9%">
								<button type="button" class="btn btn-sm btn-primary  "  data-id="'.$row[0].'" data-toggle="modal" data-target="#edit_question"><i class="fa fa-edit"></i> </button><button type="button" class="btn btn-sm btn-danger " data-id="'.$row[0].'"  data-toggle="modal" data-target="#delete_question"><i class="fa fa-trash"></i> </button></td>
							</tr>
							';
						$no++;
						}
					?>
				</tbody>
			</table>
	  </div>
	</div>
  </div>
</div>

<!-- MODAL add question -->
			<div class="modal fade" id="add_question" role="dialog">
		    <div class="modal-dialog">
		    <!-- Modal content-->
			<div class="modal-content">
			    <div class="modal-header">
			          <button type="button" class="close" data-dismiss="modal">&times;</button>
			          <h4 class="modal-title">Add Question</h4>
			        </div>
			        <div class="modal-body">
			          Sorry, this menu is not ready to use
			        </div>
			        <!-- <div class="modal-footer">
			        <input type="submit" name="cmd" class="btn btn-primary" value="Add Subject">
			         
			          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        </div> </form>
			      </div> -->
		        </div>
			</div>
		</div>
<!-- Modal edit question -->
			<div class="modal fade" id="edit_question" role="dialog">
		    <div class="modal-dialog modal-lg">
		        <!-- Modal content-->
			      <div class="modal-content">
			       	<div class="fetched-data"></div> 
			      </div>
			</div>
			</div>
<!-- Modal delete questions -->
			<div class="modal fade" id="delete_question" role="dialog">
		    <div class="modal-dialog">
		        <!-- Modal content-->
			      <div class="modal-content">
			       	<div class="fetched-data"></div> 
			      </div>
			</div>
			</div>
<script>				
	$(document).ready(function() {
		$('#tbQuestion').DataTable();
	} );

			  $(document).ready(function(){
			    $('#delete_question').on('show.bs.modal', function (e) {
			        var rowid = $(e.relatedTarget).data('id');
			        $.ajax({
			            type : 'get',
			            url : 'view_admin/3_delete_question.php', //Here you will fetch records 
			            data :  'id='+ rowid, //Pass $id
			            success : function(data){
			            $('.fetched-data').html(data);//Show fetched data from database
			            }
			        });
			     });
			});
			  $(document).ready(function(){
			    $('#edit_question').on('show.bs.modal', function (e) {
			        var rowid = $(e.relatedTarget).data('id');
			        $.ajax({
			            type : 'get',
			            url : 'view_admin/3_edit_question.php', //Here you will fetch records 
			            data :  'id='+ rowid, //Pass $id
			            success : function(data){
			            $('.fetched-data').html(data);//Show fetched data from database
			            }
			        });
			     });
			});
</script>