<?php 
	$id_voucher=$_GET['id'];
	if (isset($_POST['cmd'])) {
		switch ($_POST['cmd']) {
			case 'top-up':
				$id_v = $_POST['id_voucher'];
				$topup = $_POST['top-up'];
				$inv_num = $_POST['invoice_num'];
				$inv_date = $_POST['invoice_date'];
				topUpVoucher($id_v,$topup,$inv_num,$inv_date);
			break;
		}
	}
?>
<div class="row">
	<div class="col-lg-12">
		<h1></h1>
	</div>
	<div class="col-md-12">
		<div class="panel panel-info">
			<div class="panel-heading">Detail Voucher <?=$id_voucher?></div>
			<div class="panel-body">
			<div class="row">
				<div class="col-md-6">
					<a href="?pg=admin_man_cust" class="btn btn-xs btn-danger"><i class="fa fa-arrow-left" ></i> Back</a>
					<button class="btn btn-xs btn-primary" data-id="<?=$id_voucher?>" data-toggle="modal" data-target="#topup_voucher"><i class="fa fa-shopping-cart"></i> Transaksi</button>
					<button class="btn btn-xs btn-warning" data-id="<?=$id_voucher?>" data-toggle="modal" data-target="#report"><i class="fa fa-flag-o"></i> Report</button>
		<?php
			$result=editVoucher($id_voucher);
			$row = mysqli_fetch_array($result);
				echo " <table style=\"height: 100px;margin-top:10px;\">
						<tr>
							<td style=\" width: 150px;font-weight: 700;\">Id Voucher</td>
							<td style=\" width: 20px;\">:</td>
							<td>".$row[0]."</td>
						</tr>
						<tr>
							<td style=\" width: 150px;font-weight: 700;\">Customer Name</td>
							<td >:</td>
							<td>".$row[1]."</td>
						</tr>
						<tr>
							<td style=\" width: 150px;font-weight: 700;\">Program Name</td>
							<td>:</td>
							<td>".$row[2]."</td>
						</tr>
						<tr>
							<td style=\" width: 150px;font-weight: 700;\">Saldo Voucher</td>
							<td>:</td>
							<td>".$row[3]."</td>
						</tr>
					   </table>";

			
		?>	</div>
			<div class="col-md-6" >
				<form method="POST" autocomplete="off" role='form'>
					<div class="form-group">
						<label>Filter by Date</label>
						<div class="controls input-append date date_start"  data-date-format="yyyy-mm-dd" data-link-field="dtp_input1">
						<input class="form-control" name="start" placeholder="yyyy-mm-dd" required>
						<span class="add-on"><i class="icon-th"></i></span>
						</div>
					</div>
					<div class="form-group">
						<label>To</label>
						<div class="controls input-append date date_end"  data-date-format="yyyy-mm-dd" data-link-field="dtp_input1">
						<input class="form-control" name="end" placeholder="yyyy-mm-dd" required>
						<span class="add-on"><i class="icon-th"></i></span>
						</div>
					</div>
					<div class="form-group" style="text-align: center;">
						<input type="hidden" name="cmd" value="filter"></ins>
				<input class="btn btn-sm btn-primary" style="padding: 2px 10px;" type="submit" name="" value="Submit">
					</div>
			</form>
				
			</div>
		
		<div class="col-lg-12" >
		<hr style="    border: 0;   height: 1px;    background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgb(48, 165, 255), rgba(0, 0, 0, 0));">
			<h4>Stock Card</h4>
	</div>
</div>
	<div class="table-responsive">
		  <table class="table table-bordered" id="tbStockCard" style="font-size: 11pt;">
		    <thead>
		      <tr>
		    	<th>Tanggal</th>
		    	<th>Description</th>
		    	<th>Kredit</th>
		    	<th>Debit</th>
		    	<th>Saldo</th>
		      </tr>
		    </thead>
		    <?php
		    $no=1;
		    if (isset($_POST['start'])) {
		    	$view=historyVoucher($id_voucher,$_POST['start'],$_POST['end']);
			}else{
				$view=historyVoucher($id_voucher);
		    }
			while($row = mysqli_fetch_array($view)){
		   		if ($row[1]==0) {$kredit=$row[5]; $debit='';}else{$kredit=''; $debit=$row[9];}
				if ($row[1]==0) {
					$desc="<b>(".$row[11].")</b> <br>
					Invoice Number : ".$row[7]." / Date : ".tanggal_indo($row[8]);
				}else{
					$desc="<b>Exam Group : </b>".$row[10]."  <a href = '?pg=detail_exam&id=$id_voucher.".$row[1]."' class=\"btn btn-xs btn-primary \"><i class=\"fa fa-info-circle\"></i>  Detail</a>"; 
				}
				echo "
				<tr>
					<td>".date("d-M-Y H:i",strtotime($row[3]))."</td>
					<td>".$desc."</td>
					<td>".$kredit."</td>
					<td>".$debit."</td>
					<td>".$row[12]."</td>
				</tr>

				";
				$no++;
			}
		    ?>
		  </table>
		  </div>
		</div>
		</div>
	</div>
</div>
</div>
</div>
<!-- Modal Transaksi -->
			<div class="modal fade" id="topup_voucher" role="dialog">
		    	<div class="modal-dialog">
		        <!-- Modal content-->
			      <div class="modal-content">
			        	<div class="fetched-data"></div> 			          
			      </div>
		        </div>
			</div>
<!-- Modal Transaksi -->
			<div class="modal fade" id="report" role="dialog">
		    <div class="modal-dialog">
		    <!-- Modal content-->
			<div class="modal-content">
			    <div class="modal-header">
			          <button type="button" class="close" data-dismiss="modal">&times;</button>
			          <h4 class="modal-title">Report</h4>
			        </div>
			        <div class="modal-body">
			      <form role="form" id="by_voucher" name="by_voucher" action="?pg=admin_showReport" method="POST" autocomplete="off" >
				    	<div class="form-group">
							<label>Periode</label>
							<div class="row">
								<div class="col-md-2" <div class="col-md-2" style="padding-right:0;">Start Date :</div>
								<div class="col-md-4">
								<div class="controls input-append date date_start"  data-date-format="yyyy-mm-dd" data-link-field="dtp_input1">
									<input class="form-control" name="date_start"  placeholder="YYYY-MM-DD" required>
									<span class="add-on"><i class="icon-remove"></i></span>
									<span class="add-on"><i class="icon-th"></i></span>
									</div>
								</div>
								<div class="col-md-2" >End Date : </div>
								<div class="col-md-4" >
									<div class="controls input-append date date_end"  data-date-format="yyyy-mm-dd" data-link-field="dtp_input1">
									<input class="form-control" name="date_end"  placeholder="YYYY-MM-DD" required>
									<span class="add-on"><i class="icon-remove"></i></span>
									<span class="add-on"><i class="icon-th"></i></span>
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" name="cmd" value="voucher_history">
						<input type="hidden" name="voucher[]" value="<?=$id_voucher?>">
			        <div class="modal-footer">
			            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
			            <input type="submit" class="btn btn-sm btn-success" value="Submit">
			        </div> </form>
			      </div>
		        </div>
			</div>
		</div>
		<!--/.row-->
		<script src='../assets/js/jquery-1.12.0.min.js'></script>
		<script type="text/javascript" src="../assets/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
		<script type="text/javascript" src="../assets/js/bootstrap-datetimepicker.id.js" charset="UTF-8"></script>
		<script type="text/javascript">
		   $('.date_start').datetimepicker({
		        language:  'fr',
		        weekStart: 1,
		        todayBtn:  1,
				autoclose: 1,
				todayHighlight: 1,
				startView: 2,
				minView: 2,
				forceParse: 0
		    });
		   $('.date_end').datetimepicker({
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
		<script>
			$(document).ready(function() {
				$('#tbStockCard').DataTable({
			        "order": [[ 0, "desc" ]]
			    });
			} );
			
			  $(document).ready(function(){
			    $('#topup_voucher').on('show.bs.modal', function (e) {
			        var rowid = $(e.relatedTarget).data('id');
			        $.ajax({
			            type : 'POST',
			            url : 'view_admin/4_topup_voucher.php', //Here you will fetch records 
			            data :  'id='+ rowid, //Pass $id
			            success : function(data){
			            $('.fetched-data').html(data);//Show fetched data from database
			            }
			        });
			     });
			}); 
		</script>