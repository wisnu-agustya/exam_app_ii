<?php 
$today = timeVar();
$id_g = $_SESSION['admin_group'];
if (isset($_GET['CC'])) {
  $id=$_GET['CC'];
  $tokenRandom = generatePassword();
  $update = updateExamToken($tokenRandom,$id,$today[1]);
  //$status = updateStatus($id);
  echo "
<script type='text/javascript'>
$(document).ready(function(){
document.getElementById('token').innerHTML = '$tokenRandom';
$('#Show_token').modal('show');
});
</script>";
  //header('location:?pg=pic_exam');

}
if (isset($_POST['cmd'])) {
  switch ($_POST['cmd']) {
    case 'Search':
      $valin = $_POST['valin'];
      break;
    default:
      # code...
      break;
  }
}?>
<div class="row">
  <div class="col-lg-12">
    <h4></h4>
  </div>
  <div class="col-md-12">
    <div class="panel panel-primary">
      <div class="panel-heading">Exam Manager</div>
      <div class="panel-body">
        <div class="panel-body tabs">
          <ul class="nav nav-pills">
            <li class="active"><a href="#pilltab1" data-toggle="tab">Exam Schedule</a></li>
            <li><a href="#pilltab2" data-toggle="tab">Create Exam</a></li>
          </ul>
          <div class="tab-content">             
            <div class="tab-pane fade  in active" id="pilltab1">
              <h4>Exam</h4>
              <table class="table table-xs" id="tbExam">
                <thead>
                  <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Date</th>
                    <th scope="col">Start</th>
                    <th scope="col">Proctor</th>
                    <th scope="col">Token</th>
                    <th scope="col">Status</th>
                    <th scope="col">Alocated</th>
                    <th scope="col">Option</th>
                  </tr>
                </thead>
                <tbody><?php
                $no = 1;
                $result=showExam($id_g,null);
                while($row = mysqli_fetch_array($result)){
                  if ($row[4]!=null) {
                    $token="<button type=\"button\" class=\"btn btn-xs btn-block btn-warning \" onclick=\"show_token('$row[4]')\" >$row[4]</button>";
                  } else {
                      if (cekTimeSchedule($row[0])) {
                        $token="<a href=\"?pg=pic_exam&CC=".$row[0]."\"><button type=\"button\" class=\"btn btn-xs btn-danger btn-block \"><i class=\"fa fa-key\"></i>  Generate</button></a>";
                      } else {
                        if ($row[1]==$today[0] and $row[2]>$today[1]) {
                            $token="<button type=\"button\" class=\"btn btn-xs btn-default btn-block \">Not Ready</button>";
                        }else if ($row[1]>$today[0]) {
                            $token="<button type=\"button\" class=\"btn btn-xs btn-default btn-block \">Not Ready</button>";
                        }else{
                            $token="<button type=\"button\" class=\"btn btn-xs btn-default btn-block \">Expired</button>";
                        }
                    }
                  }echo '
                  <tr>
                    <td>'.$no.'</td>
                    <td>'.date('d.M.Y', strtotime($row[1])).'</td>
                    <td>'.$row[2].'</td>
                    <td>'.$row[3].'</td>
                    <td>'.$token.'</td>
                    <td>'.$row[5].'</td>
                    <td>'.$row[6].'</td>
                    <td>';
                    if ($row[5]!='init') {
                      echo '<a href="?pg=pic_sch_detail&CC='.$row[0].'"><button type="button" class="btn btn-xs btn-primary"><i class="fa fa-info-circle"></i>  Detail</button></a> ';
                    }
                    echo '</td>
                  </tr>';
                  $no++;
                }?> 
                </tbody>
              </table>
            </div>
            <div class="tab-pane fade" id="pilltab2">
              <div class="row"><?php
                $result=editExamVoucher($id_g);
                while ($row = mysqli_fetch_array($result)) {
                echo "
                  <div class=\"col-md-3\">
                    <div class=\"panel panel-info\" style=\"border: 1px solid #30a5ff;\">
                      <div class=\"panel-heading\">".$row[2]."</div>
                      <div class=\"panel-body\" style=\"text-align:center\">
                        <b>".$row[5]."</b><br>
                        Voucher : <b>".$row[3]."</b><br><br>";
                        if($row[3]==0){
                          if ($row[5]=='Postpaid') {
                          echo "<a href=\"?pg=pic_create_exam&CC=".$row[0]."\" class=\"btn btn-block btn-sm btn-danger\">Use</a>";
                          } else {
                          echo "<button type=\"button\" class=\"btn btn-block btn-sm btn-default\">Use</button>";
                          }
                        }else {
                          echo "<a href=\"?pg=pic_create_exam&CC=".$row[0]."\" class=\"btn btn-block btn-sm btn-danger\">Use</a>";
                        }
                        echo "<a href=\"?pg=pic_detail_voucher&id=".$row[0]."\" class=\"btn btn-block btn-sm btn-success\">View</a>";
                      echo"
                      </div>
                    </div>
                  </div>";
                }?>        
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="Show_token" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Token Session</h4>
      </div>
      <div class="modal-body">
        <div class="row" style="text-align: center;text-shadow: 0px -2px 2px #000;" >
          <h1 id='token' style="font-size: 75px;"></h1>
        </div>
      </div>  
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--======================-->
  <script src='../assets/js/jquery-1.12.0.min.js'></script>
  <script>
    $(document).ready(function() {
      $('#tbExam').DataTable({
        "columnDefs":[
          {"width": "2%", "targets":0},
          {"width": "15%", "targets":1},
          {"width": "8%", "targets":2}
        ]
      });
    });
    function show_token($tokenRandom){
      document.getElementById('token').innerHTML = $tokenRandom;
      $('#Show_token').modal('show');
    }
  </script>
      