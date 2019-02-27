<?php

function limit_words($string, $word_limit)
{
    $words = explode(' ', $string);

    return implode(' ', array_splice($words, 0, $word_limit));
}
function getLogo($id)
{
    $sql = "SELECT logo,cust_name FROM customer where id_customer = '$id'";
    $res = mysqli_query($GLOBALS['link'], $sql);
    $result = mysqli_fetch_array($res);

    return $result;
}
function getDataStudent($id)
{
    $sql = " select a.fname,a.idstudents, b.logo from students a join customer b on SUBSTRING_INDEX(a.our_id,'.','1')=b.id_customer where a.idstudents='$id'";
    $res = mysqli_query($GLOBALS['link'], $sql);
    $result = mysqli_fetch_array($res);

    return $result;
}

/*==========================CUSTOMER========================*/
function editCustomer($id)
{
    $sql = "SELECT * FROM customer where id_customer = '".sqlValue($id)."'";
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function viewSetCust($id)
{
    $sql = "SELECT * FROM customer_set where id_customer = '".sqlValue($id)."'";
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function updateCustomer($id, $name, $address, $phone, $email, $logo, $result)
{
    $sql = "UPDATE `customer` SET `cust_name`='".sqlValue($name)."',`address`='".sqlValue($address)."',`phone_off`='".sqlValue($phone)."',`email_off`='".sqlValue($email)."',`logo`='".sqlValue($logo)."' WHERE id_customer='$id'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die('astafirrullah');
    $sql_set = "UPDATE `customer_set` SET `set_view_result`='$result' WHERE id_customer = '$id'";
    $res_set = mysqli_query($GLOBALS['link'], $sql_set) or die('astafirrullah');
    $cek = "SELECT a.id_customer FROM customer_set a WHERE a.id_customer = '$id'";
    $res_cek = mysqli_query($GLOBALS['link'], $cek) or die('astafirrullah');
    if (mysqli_num_rows($res_cek) == 0) {
        $sql_setting = "INSERT IGNORE INTO `customer_set`(id_customer,`set_view_result`) VALUES ('$id','$result')";
        $res_setting = mysqli_query($GLOBALS['link'], $sql_setting) or die($sql_setting);
    }
}
function deleteCustomer($id)
{
    $sql = "DELETE FROM customer where id_customer = '$id'";
    $res = mysqli_query($GLOBALS['link'], $sql);
}
function cekUser($id)
{
    $sql = "SELECT a.id_user,a.fname,a.alamat,a.phone,a.email,b.level_auth FROM users a inner join auth b on a.id_user = b.user_auth where a.cust_group = '".sqlValue($id)."'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}

////////////////////// PIC /////////////////////

/*=============================================================================================*/

/*==========================PROGRAM========================*/
function detailPrograms($id)
{
    $sql = "SELECT a.*,c.subject_name,b.percent FROM programs a inner join program_detail b on a.id_program=b.id_program inner join subject_ls c on b.id_subject=c.id_subject WHERE a.id_program = '$id'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}
/*=============================================================================================*/
/*==========================SOAL========================*/
function showQuestion($subject_id)
{
    if (isset($subject_id)) {
        $sql = "select a.*,b.subject_name from exam_source a inner join subject_ls b on a.id_subject= b.id_subject where b.id_subject = '$subject_id'";
    } else {
        $sql = 'select a.*,b.subject_name from exam_source a inner join subject_ls b on a.id_subject=b.id_subject';
    }
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function editQuestion($id)
{
    $sql = "SELECT * FROM  exam_source where id=$id";
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function deleteQuestion($id)
{
    $sql = "DELETE FROM  exam_source where id = $id";
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
//////////////////////////////// Import Soal Excel //////////////////////////////////////////

function print_upload_form()
{
    if (isset($_GET['CC'])) {
        $idSubject = $_GET['CC'];
    }
    echo '<div class="row">
			<div class="col-lg-12">
			<h1></h1>
		  </div>
		  <div class="col-lg-12">
		    <div class="panel panel-info">
			<div class="panel-heading">Upload Soal</div>
			<div class="panel-body">
		  <div class="col-md-6">
		  
			';
    echo "<form role='form' name=\"fupload\" method=\"post\" enctype=\"multipart/form-data\" action=\"\">";
    echo '	<input type="hidden" name="cmd" value="upload">
					<input type="hidden" name="kode_soal" value="">
	<div class="datagrid">
		';
    echo '<div class="form-group">
			<label>Subject</label>
				<select class="form-control" name="subject">
					';
    $result = showSubject();
    while ($row = mysqli_fetch_array($result)) {
        if ($idSubject == $row[0]) {
            echo "<option value='".$row[0]."' selected>".$row[1].'</option>';
        } else {
            echo "<option value='".$row[0]."'>".$row[1].'</option>';
        }
    }
    echo'	</select>
		</div>';
    echo '	<div class="form-group">
				<label>Data File</label>
				<input class="form-control" type="file" name="file1">
			</div>
			<button class="btn btn-sm btn-danger" type="button" onclick="javascript:history.back()"><i class="fa fa-arrow-left" ></i> Back</button>
				<input class="btn btn-sm btn-primary" type="submit" id="btsubmit" name="btsubmit" value="Upload">';
    echo "</form>
		</div>
		</div>
		<div class='col-md-6'>
		<h3>Harap diperhatikan</h3>
			<table>
				<tr>
					<td>1. </td>
					<td>Format File Upload bisa di download <a href=\"../assets/file/Example File Upload.xls\" style:'color:blue;'> Disini</a></td>
				</tr>
				<tr>
					<td>2. </td>
					<td>Format File Upload harus .xls</td>
				</tr>
				<tr>
					<td>3. </td>
					<td>Jika terdapat gambar pada soal, posisi gambar harus di dalam kolom (Seperti pada contoh)</td>
				</tr>
			</table>
		</div>
		
		</div>
	</div>
	</div>
	";
}

function print_info($data)
{
    $row_count = $data->rowcount(0);
    $col_count = $data->colcount(0);
    echo 'Col Count:'.$col_count;
    echo '<br>';
    echo 'Row Count:'.$row_count;
    echo '<br>';
    if ($col_count != $GLOBALS['xl_col_count']) {
        return false;
    } else {
        return true;
    }
}

function import_excel($filename)
{
    global $kode_soal;
    global $kode_mapel;
    $filename_path = '../'.$GLOBALS['tmp-dir'].$filename;
    $ext = strtolower(array_pop(explode('.', $filename)));
    if ($ext == 'xls') {
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
    } else {
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    }
    $objPHPExcel = $objReader->load($filename_path);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $into_fields = 'row_reff,kode_mapel,import_id,pertanyaan,
	jawab_a,jawab_b,jawab_c,jawab_d,jawab_e,kunci';
    $rows = $objWorksheet->getHighestRow();
    for ($row = 2; $row <= $rows; ++$row) {
        $data = "'".$row."','".$kode_mapel."','".$_SESSION['import_id']."'";
        //kolom index start dari 0 (A=0,B=1 dst)
        //kolom A skip
        //kolom B,C,D,E,F,G,H,I,J ->tanya,a,b,c,d,e,kunci,bobot,tipe_soal
        for ($col = 1; $col <= 7; ++$col) {
            $data .= ",'";
            $data .= sqlValue($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
            $data .= "'";
        }
        $sql = 'insert into exam_source_tmp('.$into_fields.') values ('.$data.')';
        //echo $sql."<br>";
        mysqli_query($GLOBALS['link'], $sql);
    }
    //kolom K,L,M,N,O -> [img1],[img2],[img3],[img4],[img5]
    //get images
    //$GLOBALS["app-path"].$GLOBALS["img-soal-dir"]
    $imgdir = '../'.$GLOBALS['img-soal-dir'];

    foreach ($objWorksheet->getDrawingCollection() as $drawing) {
        $coordinate = $drawing->getCoordinates();
        preg_match('/[z-zA-Z]+/i', $coordinate, $capt);
        $col = $capt[0];
        unset($capt);
        preg_match('/[0-9]+/i', $coordinate, $capt);
        $row = $capt[0];
        unset($capt);
        ob_start();
        call_user_func(
        $drawing->getRenderingFunction(),
        $drawing->getImageResource()
    );
        $imageContents = ob_get_contents();
        ob_end_clean();

        $imgfiletype = $drawing->getMimeType();
        $imgfilename = $_SESSION['import_id'].'-'.$row.'-'.$col; //md5(microtime());
        $new_file = '';
        switch ($imgfiletype) {
        case 'image/gif':
            $image = imagecreatefromstring($imageContents);
            imagegif($image, $imgdir.$imgfilename.'.gif', 100);
            $new_file = $imgfilename.'.gif';
            break;

        case 'image/jpeg':
            $image = imagecreatefromstring($imageContents);
            imagejpeg($image, $imgdir.$imgfilename.'.jpg', 100);
            $new_file = $imgfilename.'.jpg';
            break;

        case 'image/png':
            $image = imagecreatefromstring($imageContents);
            imagepng($image, $imgdir.$imgfilename.'.png');
            $new_file = $imgfilename.'.png';
            break;

        default:
            echo $imgsiletype.'<br>';
    }
        if ($new_file != '') {
            $sql = "select id,pertanyaan,jawab_a,jawab_b,jawab_c,jawab_d,jawab_e,kunci from exam_source_tmp
    			where import_id='".$_SESSION['import_id']."' and row_reff='".$row."'";
            $rs = mysqli_query($GLOBALS['link'], $sql);
            if ($row = mysqli_fetch_row($rs)) {
                $id = $row[0];
                $tanya = $row[1];
                $a = $row[2];
                $b = $row[3];
                $c = $row[4];
                $d = $row[5];
                $e = $row[6];
                switch ($col) {
                case 'I':
                    $imgtag = '[img1]';
                    break;
                case 'J':
                    $imgtag = '[img2]';
                    break;
                case 'K':
                    $imgtag = '[img3]';
                    break;
                case 'L':
                    $imgtag = '[img4]';
                    break;
                case 'M':
                    $imgtag = '[img5]';
                    break;
                default:
                    $imgtag = '';
            }
                //echo $imgtag;
                if ($imgtag != '') {
                    $rplwith = "<img src=$path\"".$new_file.'"><br>';

                    if ((!strpos($tanya, $imgtag)) &&
                            (!strpos($jawab_a, $imgtag)) &&
                            (!strpos($jawab_b, $imgtag)) &&
                            (!strpos($jawab_c, $imgtag)) &&
                            (!strpos($jawab_d, $imgtag)) &&
                            (!strpos($jawab_e, $imgtag))) {
                        //not find [img] tag,, put img in $tanya by default
                        $tanya .= '<br>'.$rplwith;
                    } else {
                        $tanya = str_replace($imgtag, $rplwith, $tanya);
                        $a = str_replace($imgtag, $rplwith, $a);
                        $b = str_replace($imgtag, $rplwith, $b);
                        $c = str_replace($imgtag, $rplwith, $c);
                        $d = str_replace($imgtag, $rplwith, $d);
                        $e = str_replace($imgtag, $rplwith, $e);
                    }
                    $sql = "update exam_source_tmp set pertanyaan='".$tanya."',
    				jawab_a='".$a."',
    				jawab_b='".$b."',
    				jawab_c='".$c."',
    				jawab_d='".$d."',
    				jawab_e='".$e."'
    				where id='".$id."'";
                    mysqli_query($GLOBALS['link'], $sql);
                }
            }
        }
    }
}

function preview_soal()
{
    echo '
		<style type="text/css">
			table {
				font-size: 11px;
				border: 2px solid #3b719a;
			}
			td{
				border-right: 1px solid #3b719a;
				padding: 3px 5px;
			}
		</style>
	';
    $sql = 'select id,pertanyaan,jawab_a,jawab_b,jawab_c,jawab_d,jawab_e,kunci,bobot from exam_source_tmp';
    $sql .= " where import_id='".$_SESSION['import_id']."' order by id";
    $rs = mysqli_query($GLOBALS['link'], $sql);
    $i = 1;
    while ($row = mysqli_fetch_row($rs)) {
        $tr_class = ' class="alt" ';
        //$tr_class=" class=\"alt\" ";
        if ($i % 2) {
            $tr_class = '';
        }
        if ($i == 1) {
            echo '<div class="row">
					<div class="col-lg-12">
					<h1></h1>
				  </div>
				  <div class="col-lg-12">
				    <div class="panel panel-info">
					<div class="panel-heading">Preview Soal</div>
					<div class="panel-body">
					';
            echo "<table style='width:100%'>";
        }
        echo '<tr '.$tr_class.'>';
        echo '<td valign="top" style="width: 25px;"><b>'.$i.'.</b></td>';
        echo '<td colspan="2">'.str_replace('<img src="', '<br><img style="max-width:500px;" src="../'.$GLOBALS['img-soal-dir'], $row[1]).'</td>';
        //echo "<td valign=\"top\" width=\"80\" align=\"center\"></td>";
        //edit saat review soal dimatikan
        // echo "<td valign=\"top\" width=\"80\" align=\"center\"><a href=\"javascript:edit_soal_tmp(".$row[0].");\">Edit</a></td>";
        echo '</tr>';
        echo '<tr '.$tr_class.'>';
        echo '<td>&nbsp;</td>';
        echo '<td valign="top" style="border-right: 0px;">';
        echo '<b>A.</b> '.str_replace('<img src="', '<img style="max-width:500px;" src="../'.$GLOBALS['img-soal-dir'], $row[2]);
        echo '</td>';
        echo '<td valign="top" >';
        echo '<b>C.</b> '.str_replace('<img src="', '<img style="max-width:500px;" src="../'.$GLOBALS['img-soal-dir'], $row[4]);
        echo '</td>';
        //echo "<td>&nbsp;</td>";
        echo '</tr>';
        echo '<tr '.$tr_class.'>';
        echo '<td>&nbsp;</td>';
        echo '<td valign="top" style="border-right: 0px;">';
        echo '<b>B.</b> '.str_replace('<img src="', '<img style="max-width:500px;" src="../'.$GLOBALS['img-soal-dir'], $row[3]);
        echo '</td>';
        echo '<td valign="top">';
        echo '<b>D.</b> '.str_replace('<img src="', '<img style="max-width:500px;" src="../'.$GLOBALS['img-soal-dir'], $row[5]);
        echo '</td>';
        //echo "<td>&nbsp;</td>";
        echo '</tr>';

        echo '<tr '.$tr_class.'>';
        echo '<td>&nbsp;</td>';
        echo '<td valign="top" style="border-right: 0px;">';
        echo '<b>E.</b> '.str_replace('<img src="', '<img style="max-width:500px;" src="../'.$GLOBALS['img-soal-dir'], $row[6]);
        echo '</td>';
        echo '<td>&nbsp;</td>';
        //echo "<td>&nbsp;</td>";
        echo '</tr>';

        echo '<tr '.$tr_class.'>';
        echo '<td style="padding-bottom:10px;border-bottom: 1px solid #3b719a;">&nbsp;</td>';
        echo '<td style="border-right: 0px; padding-bottom:10px;border-bottom: 1px solid #3b719a;">';
        echo '<b>Jawaban: '.$row[7].'</b><br>';
        //echo "<b>Bobot: ".$GLOBALS["bobot_soal"][$row[8]]." </b>";
        echo '</td>';
        echo '<td style="padding-bottom:10px;border-bottom: 1px solid #3b719a;">';
        echo '&nbsp;';
        echo '</td style="padding-bottom:10px;border-bottom: 1px solid #3b719a;">';
        //echo "<td>&nbsp;</td>";
        echo '</tr>';
        ++$i;
    }
    if ($i > 1) {
        echo '</table>';
        echo '<br>';
    }
    //untuk reload preview setelah edit soal
    print_form_reload();
}

function finish_import()
{
    $sql = "INSERT INTO `exam_source`(`id_subject`, `question`, `val_a`, `val_b`, `val_c`, `val_d`, `val_e`, `val_key`)select kode_mapel,pertanyaan,jawab_a,jawab_b,jawab_c,jawab_d,jawab_e,kunci from exam_source_tmp	where import_id='".$_SESSION['import_id']."'";

    mysqli_query($GLOBALS['link'], $sql) or die('error import question');

    //echo $sql;
    $sql = "delete from exam_source_tmp where import_id='".$_SESSION['import_id']."'";
    mysqli_query($GLOBALS['link'], $sql);
    echo "<script LANGUAGE='JavaScript'>
		    window.alert('Upload berhasil, klik ok!');
		    window.location.href='dashboard.php?pg=admin_soal';
		    </script>";
}

function batal_import()
{
    if (isset($_SESSION['import_id'])) {
        $sql = "delete from exam_source_tmp where import_id='".$_SESSION['import_id']."'";
        mysqli_query($GLOBALS['link'], $sql);
        $_SESSION['import_id'] = '';
    }
}

function print_finish_button()
{
    echo '<form name="f_finish" method="post" action="">';
    echo '<input type="hidden" name="cmd" value="finish">';
    echo '<input type="button" class="btn btn-sm btn-primary" id="bt_selesai" name="bt_selesai" value="Selesai" onclick="javascript:finish_import();">&nbsp';
    echo '<input type="button" class="btn btn-sm btn-danger" id="bt_batal" name="bt_batal" value="Batal" onclick="javascript:cancel_import();">';
    echo '</form>';
    echo "<script language=\"javascript\"> \r\n
				function cancel_import(){\r\n
				document.f_finish.cmd.value=\"batal\"; \r\n
				document.f_finish.submit(); \r\n
				}\r\n
				function finish_import(){\r\n
				document.f_finish.cmd.value=\"finish\"; \r\n
				document.f_finish.submit(); \r\n
				}\r\n
				</script>\r\n
				</div>
				</div>
				</div>
				</div>
				<br>
				<br>
				";
}

function preview_soal_old()
{
    $sql = 'select id,pertanyaan,jawab_a,jawab_b,jawab_c,jawab_d,kunci from '.$GLOBALS['tmp_table_name'];
    $sql .= " where import_id='".$_SESSION['import_id']."' order by id";
    $rs = mysqli_query($GLOBALS['link'], $sql);
    $i = 1;
    while ($row = mysqli_fetch_row($rs)) {
        if ($i == 1) {
            echo '<div class="datagrid">';
            echo '<table>';
            echo '<thead>';
            echo '<tr><th colspan="4" align="center">PREVIEW SOAL</th></tr>';
            echo '</thead>';
        }
        echo '<tr>';
        echo '<td rowspan="3" valign="top">'.$i.'</td>';
        echo '<td colspan="2">'.$row[1].'</td>';
        echo '<td rowspan="3" valign="middle"><a href="javascript:edit_soal_tmp('.$row[0].');">Edit</a> | delete</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>';
        if (($row[6] == 'a') || ($row[6] == 'A')) {
            echo '<b>A.'.$row[2].'</b>';
        } else {
            echo 'A.'.$row[2];
        }
        echo '</td>';
        echo '<td>';
        if (($row[6] == 'c') || ($row[6] == 'C')) {
            echo '<b>C.'.$row[4].'</b>';
        } else {
            echo 'C.'.$row[4];
        }
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>';
        if (($row[6] == 'b') || ($row[6] == 'B')) {
            echo '<b>B.'.$row[3].'</b>';
        } else {
            echo 'B.'.$row[3];
        }
        echo '</td>';
        echo '<td>';
        if (($row[6] == 'd') || ($row[6] == 'D')) {
            echo '<b>D.'.$row[5].'</b>';
        } else {
            echo 'D.'.$row[5];
        }
        echo '</td>';
        echo '</tr>';
        ++$i;
    }
    if ($i > 1) {
        echo '</table>';
        echo '</div>';
    }
    //untuk reload preview setelah edit soal
    print_form_reload();
}

/*=============================================================================================*/
/*==========================SUBJECT========================*/
function showSubjectList($id)
{
    $sql = "SELECT b.id_subject,b.subject_name,b.level 
	FROM program_detail a LEFT JOIN subject_ls b ON a.id_subject = b.id_subject 
	WHERE a.id_program = '$id' ORDER BY b.id_subject;";
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function showSubject($id)
{
    $sql = 'SELECT a.id_subject,a.subject_name,a.level FROM  subject_ls a group by a.id_subject';
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function addSubject($id, $name, $level)
{
    $sql = 'select max(id_subject) from subject_ls';
    $carikode = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($sql));
    // menjadikannya array
    $datakode = mysqli_fetch_array($carikode);
    // jika $datakode
    if ($datakode) {
        $nilaikode = substr($datakode[0], 2);
        // menjadikan $nilaikode ( int )
        $kode = (int) $nilaikode;
        // setiap $kode di tambah 1
        $kode = $kode + 1;
        $new_id = 'SJ'.str_pad($kode, 4, '0', STR_PAD_LEFT);
    } else {
        $new_id = 'SJ0001';
    }
    $sql = "INSERT INTO `subject_ls`(`id_subject`, `subject_name`, `level`) VALUES ('".sqlValue($new_id)."','".sqlValue($name)."',".sqlValue($level).')';
    $res = mysqli_query($GLOBALS['link'], $sql) or die('Process Error please input again <a href=dashboard.php?pg=admin_soal> Here </a> <br> error : '.mysqli_error($GLOBALS['link']));

    return $sql;
}
function editSubject($id)
{
    $sql = "SELECT * FROM subject_ls where id_subject = '$id'";
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function updateSubject($id, $name, $level)
{
    $sql = "UPDATE `subject_ls` SET `subject_name`='".sqlValue($name)."',`level`='".sqlValue($level)."' WHERE `id_subject`='".sqlValue($id)."'";
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function deleteSubject($id, $id_subject)
{
    $sql = "DELETE from subject_ls where id=$id and id_subject='$id_subject'";
    $res = mysqli_query($GLOBALS['link'], $sql);
    $sql = "DELETE FROM  exam_source where id_subject = '$id_subject'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}
/*=============================================================================================*/
/*==========================VOUCHER========================*/
function checkExistingVoucher($cust, $program, $type)
{
    $sql = "SELECT * FROM transact_voucher WHERE id_customer = '$cust' AND id_program = '$program' AND type_voucher = '$type'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    if ($row = mysqli_num_rows($res) == 0) {
        return true;
    } else {
        return false;
    }
}

function showVoucher($id, $extra)
{
    if (isset($id) and $id != 'null') {
        $sql = "SELECT a.id_voucher,b.cust_name,c.program_name,d.available_val,a.type_voucher, a.id_program from transact_voucher a inner join customer b on a.id_customer=b.id_customer inner join programs c on a.id_program=c.id_program inner join tmp_voucher d on a.id_voucher=d.id_voucher
			where b.id_customer='$id' $extra";
    } else {
        $sql = 'SELECT a.id_voucher,b.cust_name,c.program_name,d.available_val,a.type_voucher from transact_voucher a inner join customer b on a.id_customer=b.id_customer inner join programs c on a.id_program=c.id_program inner join tmp_voucher d on a.id_voucher=d.id_voucher';
    }
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}

function showAllVoucher($id, $extra)
{
    if (isset($id) and $id != 'null') {
        $sql = 'SELECT a.id_voucher,b.cust_name,c.program_name,d.available_val,a.type_voucher, a.id_program 
		from transact_voucher a inner join customer b on a.id_customer=b.id_customer 
		inner join programs c on a.id_program=c.id_program inner join tmp_voucher d on a.id_voucher=d.id_voucher';
    } else {
        $sql = 'SELECT a.id_voucher,b.cust_name,c.program_name,d.available_val,a.type_voucher 
		from transact_voucher a inner join customer b on a.id_customer=b.id_customer 
		inner join programs c on a.id_program=c.id_program inner join tmp_voucher d on a.id_voucher=d.id_voucher';
    }
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}

function addVoucher($customer, $program, $amount, $today, $type, $inv_num, $inv_date)
{
    $user_admin = $_SESSION['admin_id'];
    if ($amount == null) {
        $amount = 0;
    }
    $sql = 'select max(id_voucher) from transact_voucher';
    $carikode = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error());
    // menjadikannya array
    $datakode = mysqli_fetch_array($carikode);
    // jika $datakode
    if ($datakode) {
        $nilaikode = substr($datakode[0], 2);
        // menjadikan $nilaikode ( int )
        $kode = (int) $nilaikode;
        // setiap $kode di tambah 1
        $kode = $kode + 1;
        $new_id = 'VC'.str_pad($kode, 4, '0', STR_PAD_LEFT);
    } else {
        $new_id = 'VC0001';
    }
    $sql = "INSERT INTO `transact_voucher`(`id_voucher`, `id_customer`, `id_program`, `date_create`,type_voucher) VALUES ('$new_id','".sqlValue($customer)."','".sqlValue($program)."','".sqlValue($today)."','$type')";
    mysqli_query($GLOBALS['link'], $sql) or die($sql);
    $sql = "INSERT INTO tmp_voucher (`id_voucher`,`available_val`) VALUES ('$new_id','$amount')";
    mysqli_query($GLOBALS['link'], $sql) or die($sql);
    //$sql = "INSERT INTO `voucher_history`(`id_voucher`, `status`, `top_up`, `date`,saldo) VALUES  ('$new_id','Topup',$amount,'" . sqlValue($today) . "',$amount)";
    $sql = "INSERT INTO `voucher_history`(`id_voucher`, `status`, `date`, `top_up`,saldo,inv_num,inv_date,user_id) VALUES ('$new_id','Topup','$today',$amount,$amount,'$inv_num','$inv_date','$user_admin')";

    mysqli_query($GLOBALS['link'], $sql) or die($sql);
}
function editVoucher($id)
{
    $sql = "select a.id_voucher,b.cust_name,c.program_name,d.available_val,c.margin from transact_voucher a inner join customer b on a.id_customer=b.id_customer
	inner join programs c on a.id_program=c.id_program 
	inner join tmp_voucher d on a.id_voucher=d.id_voucher
	where a.id_voucher='".sqlValue($id)."'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die($sql);

    return $res;
}
function historyVoucher($id, $start, $end)
{
    if (isset($start) and isset($end)) {
        $sql = "SELECT a.*, if(a.status = 'Topup',top_up,count(*)) total,b.group_name,c.fname,min(a.saldo)
		FROM voucher_history a
		left join exam_group b on a.exam_code = b.exam_code
		left join users c on a.user_id = c.id_user
		where a.id_voucher='".sqlValue($id)."' and a.date >='$start 00:00:00' and a.date<='$end 23:59:59'
		group by if(a.status = 'Topup',a.date,a.exam_code) 
		order by a.date desc";
    } else {
        $sql = "SELECT a.*, if(a.status = 'Topup',top_up,count(*)) total,b.group_name,c.fname,min(a.saldo)
		FROM voucher_history a
		left join exam_group b on a.exam_code = b.exam_code
		left join users c on a.user_id = c.id_user
		where a.id_voucher='".sqlValue($id)."' 
		group by if(a.status = 'Topup',a.date,a.exam_code) 
		order by a.date desc";
    }
    $res = mysqli_query($GLOBALS['link'], $sql);

    return $res;
}
function historyDetail($id, $exam_code)
{
    $sql = "SELECT * FROM voucher_history where id_voucher='".sqlValue($id)."'AND exam_code= $exam_code";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}
function topUpVoucher($id, $topup, $inv_num, $inv_date)
{
    $today = date('Y-m-d H:i:s');
    $user_admin = $_SESSION['admin_id'];
    $sql = "UPDATE transact_voucher SET lastest_topup = '$today' where id_voucher='$id'";
    mysqli_query($GLOBALS['link'], $sql) or die($sql);
    $sql = "UPDATE  tmp_voucher SET available_val = available_val+'$topup' where id_voucher='$id'";
    mysqli_query($GLOBALS['link'], $sql) or die($sql);
    $sql1 = "select available_val from tmp_voucher  where id_voucher='$id'";
    $a = mysqli_query($GLOBALS['link'], $sql1) or die($sql1);
    $val_sql = mysqli_fetch_array($a);
    $sql = "INSERT INTO `voucher_history`(`id_voucher`, `status`, `date`, `top_up`,saldo,inv_num,inv_date,user_id) VALUES ('$id','Topup','$today',$topup,$val_sql[0],'$inv_num','$inv_date','$user_admin')";
    mysqli_query($GLOBALS['link'], $sql) or die($sql);
    mysqli_close($sql);
}
function deleteVoucher($id)
{
    $sql = "DELETE FROM tmp_voucher WHERE id_voucher='$id'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    $sql = "DELETE FROM transact_voucher WHERE id_voucher='$id'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
}
function cekVoucherAlocated($voucher_id)
{
    $qwr = "SELECT b.plan_val FROM transact_voucher a INNER JOIN tmp_voucher b ON a.id_voucher=b.id_voucher 
		WHERE a.id_voucher ='".$voucher_id."'";
    $rsqwr = mysqli_query($GLOBALS['link'], $qwr) or die(mysqli_error($GLOBALS['link']));
    $row = mysqli_fetch_row($rsqwr);
    $new_quota = $row[0] - 1;
}

/*=============================================================================================*/
/*==========================Exam========================*/

/*=============================================================================================*/
/*==========================Student========================*/
function listExamParticipants($group)
{
    $sql = "SELECT SUBSTRING_INDEX(b.id_student,'.','1') n, b.start_time, b.end_time FROM exam_schedule a 
	INNER JOIN exam_session b ON a.exam_group=b.exam_code WHERE a.exam_group='$group'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql);

    return $res;
}
function resultExamByid($ids, $group)
{
    foreach (resultExam($ids, $group) as $r) {
        $quest = $r[2] + $r[3] + $r[4];
        $point[] = $quest;
        $true[] = $r[2];
    }
    $point = array_sum($point);
    $true = array_sum($true);
    $val = ($true / $point) * 100;
    $n = number_format((float) $val, 2, '.', '').' %';
    $sql = "SELECT a.exam_code, c.pass_grade
		FROM exam_group a JOIN transact_voucher b ON a.id_voucher = b.id_voucher
		JOIN programs c ON b.id_program = c.id_program
		WHERE a.exam_code = $group";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    $row = mysqli_fetch_array($res);
    $equ = $row[1];
    if ($val >= $equ) {
        $grad[0] = 'L';
        $grad[1] = $val;
    } else {
        $grad[0] = 'TL';
        $grad[1] = $val;
    }

    return $grad;
}
function userRemidial($prog, $cust_group, $where)
{
    if (isset($where)) {
        $sql = "select * from students_remidial where program ='$prog' and SUBSTRING_INDEX(our_id,'.',1) = '$cust_group' and $where";
    } else {
        $sql = "select * from students_remidial where program ='$prog' and SUBSTRING_INDEX(our_id,'.',1) = '$cust_group'";
    }
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $rs;
}
function sendUserRemidial($user, $prog, $cust_group)
{
    foreach ($user as $key => $value) {
        $sql = "update students_remidial set submited = 'Y' where nim ='$value' and program='$prog' and SUBSTRING_INDEX(our_id,'.',1)='$cust_group'";
        $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    }
}
function approveUserRemidial($nim, $user, $prog, $cust_group, $margin)
{
    foreach ($user as $key => $value) {
        //max notif max prioritas
        $qwer = "select max(prioritas),max(notif) from user_test where user_id = '$value' and id_program='$prog'";
        $exe = mysqli_query($GLOBALS['link'], $qwer) or die(mysqli_error($GLOBALS['link']));
        $dataUser = mysqli_fetch_array($exe);
        for ($i = 1; $i <= $margin[$key]; ++$i) {
            if ($i == 1) {
                $userTest = $value.'.'.$i;
                $skrg = sqlFormatDate(date('d/m/Y H:i:s', mktime(date(H), date(i) - 1, date(s), date(m), date(d), date(Y))), '%d/%m/%Y %H:%i:%s');
                $sql = "insert into user_test(user_id,id_peserta,status,prioritas,notif,eff_date,id_program) values ('".$nim[$key]."','".$userTest."',1,".($i + $dataUser[0]).','.($dataUser[1] + 1).','.$skrg.",'".sqlValue($prog)."')";
            } else {
                $userTest = $value.'.'.$i;
                $sql = "insert into user_test(user_id,id_peserta,status,prioritas,notif,eff_date,id_program) values ('".$nim[$key]."','".$userTest."',0,".($i + $dataUser[0]).','.($dataUser[1] + 1).",null,'".sqlValue($prog)."')";
            }
            mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
        }
        $qwer = "delete from students_remidial where nim = '$nim[$key]' and program='$prog'";
        $exe = mysqli_query($GLOBALS['link'], $qwer) or die(mysqli_error($GLOBALS['link']));
    }
}

function examStudentLogin($id_peserta, $password)
{
    //mencocokan id peserta dan password apa 1 group
    //tambahkan where jadwal ujian masih belum kadaluarsa
    //ketentuan : start_time + 15 menit > date now
    $sql = "SELECT b.idstudents,b.fname,SUBSTRING_INDEX(b.our_id,'.','1') ,a.date,a.start_time, a.exam_group, a.id_schedule , c.id_voucher 
	FROM exam_schedule a INNER JOIN students b ON SUBSTRING_INDEX(b.our_id,'.','1') = SUBSTRING_INDEX(a.proctor,'.','1') INNER JOIN exam_group c ON a.exam_group = c.exam_code 
	WHERE b.idstudents='".sqlValue($id_peserta)."' AND a.status='run' AND a.start_time <= CURRENT_TIME() AND a.token ='".sqlValue($password)."'";
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    if ($row = mysqli_fetch_row($rs)) {
        $GLOBALS['exam_group'] = $row[5];
        $user_name = $row[1];
        $user_group = 'student';
        $cust_group = $row[2];
        $schedule_id = $row[6];
        $voucher_id = $row[7];
        //find role programs and margin ujian
        $qwr = "SELECT a.id_voucher,a.id_customer,a.id_program,b.margin 
		FROM transact_voucher a INNER JOIN programs b ON a.id_program=b.id_program 
		WHERE a.id_voucher='".sqlValue($voucher_id)."'";
        $rsqwr = mysqli_query($GLOBALS['link'], $qwr) or die(mysqli_error($link));
        if ($rw = mysqli_fetch_row($rsqwr)) {
            //find id_peserta
            // $GLOBALS['id_voucher'] = $rw[0];
            // $GLOBALS['id_customer'] = $rw[1];
            $GLOBALS['id_program'] = $rw[2];
            global $id_program;
            $sql = "SELECT a.id_peserta FROM user_test a	
			WHERE a.user_id='".sqlValue($id_peserta)."' AND a.status=1 and a.id_program = '".sqlValue($id_program)."' 
			AND a.prioritas IN (
				SELECT max(prioritas) FROM user_test WHERE user_id='".sqlValue($id_peserta)."' AND status=1
				AND ifnull(eff_date,".sqlFormatDate(date('m/d/Y 23:59')).') <= '.sqlFormatDate(date('m/d/Y H:i'))." 
				AND id_program ='".sqlValue($id_program)."')";
            $rs = mysqli_query($GLOBALS['link'], $sql);
            //cek user test existing
            if ($row = mysqli_fetch_row($rs)) {
                $test_id = $row[0];
                if (studentOnlineCek($id_peserta)) {  //login id is online?
                    return 2;
                } else {
                    $_SESSION['user_id'] = $id_peserta; //login id
                    $_SESSION['id_peserta'] = $test_id; //test id
                    $_SESSION['user_name'] = $user_name;
                    $_SESSION['user_group'] = $user_group;
                    $_SESSION['id_program'] = $id_program;
                    $_SESSION['cust_group'] = $cust_group;
                    $_SESSION['exam_group'] = $GLOBALS['exam_group'];
                    $GLOBALS['id_participant'] = $_SESSION['id_peserta'];
                    logLogin($id_peserta); //log login id
                    return 1;
                }
            } else {
                //loop user_test
                for ($i = 1; $i <= $rw[3]; ++$i) {
                    if ($i == 1) {
                        $userTest = $id_peserta.'.'.$i;
                        $skrg = sqlFormatDate(date('d/m/Y H:i:s', mktime(date(H), date(i) - 1, date(s), date(m), date(d), date(Y))), '%d/%m/%Y %H:%i:%s');
                        $sql = "insert into user_test(user_id,id_peserta,status,prioritas,notif,eff_date,id_program) values ('".$id_peserta."','".$userTest."',1,$i,0,".$skrg.",'".sqlValue($id_program)."')";
                    } else {
                        $userTest = $id_peserta.'.'.$i;
                        $sql = "insert into user_test(user_id,id_peserta,status,prioritas,notif,eff_date,id_program) values ('".$id_peserta."','".$userTest."',0,$i,0,null,'".sqlValue($id_program)."')";
                    }
                    mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
                }
                //tidak menemukan hasil user_test
                $_SESSION['user_id'] = $id_peserta; //login id
                $_SESSION['id_peserta'] = $id_peserta.'.1'; //test id
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_group'] = $user_group;
                $_SESSION['id_program'] = $id_program;
                $_SESSION['cust_group'] = $cust_group;
                $_SESSION['exam_group'] = $GLOBALS['exam_group'];
                $GLOBALS['id_participant'] = $_SESSION['id_peserta'];
                logLogin($id_peserta); //log login id
                return 1;
            }
        } else {	//tidak menemukan user & pass
            return 0; //invalid id/pass
        }
    } else {
        return 0; //invalid id/pass
    }
}
function studentLoginCek()
{
    if (isset($_SESSION['user_group'])) {
        if ($_SESSION['user_group'] == 'student') {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function studentOnlineCek($user_id)
{
    $sql = "select * from online_student where id_students='".$user_id."' and logout_time is null";
    $rs = mysqli_query($GLOBALS['link'], $sql);
    if ($row = mysqli_fetch_row($rs)) {
        return true;
    } else {
        return false;
    }
}

function logLogout($id_peserta)
{
    $skrg = date('m/d/Y H:i');
    $sql = 'update online_student set logout_time = '.sqlFormatDate($skrg)." where id_students='".$id_peserta."'and logout_time is null";
    mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
}

function logLogin($id_peserta)
{
    $skrg = date('m/d/Y H:i');
    $ip = $_SERVER['REMOTE_ADDR'];
    $sql = "INSERT INTO `online_student`( `id_students`, `login_time`, `logout_time`, `ip_address`) VALUES ('".$id_peserta."',".sqlFormatDate($skrg).",null,'".$ip."')";
    mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
}

function cekSession()
{
    global $id_peserta;
    $sql = "select id_session,exam_code from exam_session where id_student = '".sqlValue($id_peserta)."' and end_time is null";
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    if (mysqli_fetch_row($rs)) {
        return true;
    } else {
        return false;
    }
}
function cekSessionFront($id_peserta, $exam_group)
{
    $sql = "select id_session,exam_code from exam_session where id_student = '".sqlValue($id_peserta)."' and exam_code ='".sqlValue($exam_group)."' and end_time is null";
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    if (mysqli_fetch_row($rs)) {
        return true;
    } else {
        return false;
    }
}
function cekSessionExist()
{
    global $id_peserta,$exam_group;
    $sql = "select a.id_session,a.exam_code from exam_session a inner join exam_group b on a.exam_code=b.exam_code where a.id_student = '".sqlValue($id_peserta)."' AND b.id_voucher=(select id_voucher from exam_group where exam_code = $exam_group)";
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    if ($row = mysqli_fetch_row($rs)) {
        return $row[1];
    } else {
        return false;
    }
}
function cekOpportunity()
{
    global $id_peserta,$exam_group;
    $sql = "select max(notif) from user_test where id_peserta = '".sqlValue($id_peserta)."'";
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    $dataUserTest = mysqli_fetch_row($rs);
    $sql1 = "select count(a.id_student) from exam_participants a inner join exam_group b on a.exam_group=b.exam_code where a.id_student = '".sqlValue($id_peserta)."' AND b.id_voucher=(select id_voucher from exam_group where exam_code = $exam_group) and b.exam_code !=$exam_group";
    $rs1 = mysqli_query($GLOBALS['link'], $sql1) or die(mysqli_error($GLOBALS['link']).'<br>'.$sql1);
    $sumParticipant = mysqli_fetch_array($rs1);
    if ($dataUserTest[0] >= $sumParticipant[0]) {
        return true;
    } else {
        return false;
    }
}
function getDataExam()
{
    global $id_peserta,$exam_group;
    $sql = "select a.exam_group,a.id_student,b.id_voucher,c.id_program from exam_participants a inner join exam_group b on a.exam_group=b.exam_code inner join transact_voucher c on b.id_voucher = c.id_voucher where a.id_student= '".sqlValue($id_peserta)."' AND a.exam_group=$exam_group";
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));
    $result = mysqli_fetch_array($rs);

    return $result;
}
function clearQuestion($exam_code, $student_id)
{
    $sql = "DELETE FROM exam_run_quest WHERE id_student = '$student_id' AND group_name = '$exam_code'";
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']).'$sql');
}
function generateQuestion($program_id, $exam_code, $student_id)
{
    //get komposisi
    $sql = "SELECT a.sum_question,a.duration,a.margin,b.id_subject,b.percent,c.`level` from programs a 
			inner join program_detail b on a.id_program=b.id_program 
			inner join subject_ls c on b.id_subject=c.id_subject
			where a.id_program='".sqlValue($program_id)."'";
    $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']).'$sql');
    $i = 0;
    while ($row = mysqli_fetch_row($rs)) {
        $sum_question = $row[0];
        $percent = $row[4];
        $quest[] = $sum_question;
        $hasil = $sum_question * ($percent / 100);
        $levels[] = $row[5];
        $duration[] = $row[1];
        $subs[] = $row[3];
        $r[] = floor($hasil);
        ++$i;
    }
    $sum = array_sum($r);
    $mod = $quest[0] - $sum;
    $r[0] = $r[0] + $mod;
    //random soal
    $no_quest = 1;
    foreach ($r as $key => $value) {
        $sql = "select * from exam_source a where a.id_subject ='".sqlValue($subs[$key])."' order by RAND() limit $value";
        $rs = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']).$sql);
        while ($row = mysqli_fetch_row($rs)) {
            //random jawaban
            $kunci[1] = 'A';
            $kunci[2] = 'B';
            $kunci[3] = 'C';
            $kunci[4] = 'D';
            $kunci[5] = 'E';
            $field_key[4] = 'A';
            $field_key[5] = 'B';
            $field_key[6] = 'C';
            $field_key[7] = 'D';
            $field_key[8] = 'E';
            $idxs = range(4, 8);
            shuffle($idxs);
            $i = 1;
            foreach ($idxs as $idx) {
                $jawab[$i] = $row[$idx];
                if (strtoupper($row[9]) == $field_key[$idx]) {
                    $kunci_new = $kunci[$i];
                    $true = $idx;
                }
                ++$i;
            }
            //insert soal run
            $sql = "INSERT INTO `exam_run_quest`(`group_name`, `id_student`, `no_quest`, `id_quest`, `question`, `val_a`, `val_b`, `val_c`, `val_d`, `val_e`, `val_key`, `grade`, `subject`) VALUES
				($exam_code,'".sqlValue($student_id)."',$no_quest,$row[0],'".sqlValue($row[3])."','".$jawab[1]."','".$jawab[2]."','".$jawab[3]."','".$jawab[4]."','".$jawab[5]."','".$kunci_new."',$levels[$key],'".sqlValue($subs[$key])."')";
            ++$no_quest;
            $insert_quest = mysqli_query($GLOBALS['link'], $sql) or die('insert soal gagal :'.mysqli_error($GLOBALS['link']).$sql);
        }
    }
}
function sessionUjian($exam_code, $id_peserta, $program_id)
{
    $sql = "INSERT INTO `exam_session`(`id_student`, `start_time`, `end_time`, `exam_code`) VALUES ('".$id_peserta."',".sqlFormatDate(date('m/d/Y H:i:s')).",null,'".$exam_code."')";
    mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error('session error : '.$GLOBALS['link']));
    $sql1 = "UPDATE user_test SET exam_code = '$exam_code' WHERE id_peserta = '$id_peserta' AND id_program = '$program_id' and exam_code is null ";
    mysqli_query($GLOBALS['link'], $sql1) or die(mysqli_error('session error : '.$GLOBALS['link']));

    //record session user, start time, magin exam dan kesempatan ujian
}
function cancelLogin($id_peserta, $exam_group)
{
    $sql = "DELETE from exam_participants where id_student = '$id_peserta' and exam_group =$exam_group";
    mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error('Cancel Login : '.$GLOBALS['link']));
}
/*=============================================================================================*/
/*====================================  Report  =============================================*/
function showReport($id, $extra)
{
    if (isset($extra)) {
        $sql = "select a.*,b.program_name from exam_report a inner join programs b on SUBSTRING_INDEX(SUBSTRING_INDEX(a.id_report,'.',2),'.',-1)=b.id_program where SUBSTRING_INDEX(a.id_report,'.',1)='$id' and b.id_program='$extra'";
    } else {
        $sql = "select a.*,b.program_name from exam_report a inner join programs b on SUBSTRING_INDEX(SUBSTRING_INDEX(a.id_report,'.',2),'.',-1)=b.id_program where SUBSTRING_INDEX(a.id_report,'.',1)='$id'";
    }
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}
function createReport($id_g, $name, $date1, $date2)
{
    $today = date('Y-m-d H:i:s');
    $sql = "select SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1)program_id from exam_group a inner join exam_schedule b on a.exam_code=b.exam_group where b.date >='$date1' and b.date<='$date2' and SUBSTRING_INDEX(a.group_name,'.',1)='$id_g'group by program_id";
    $result = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error());
    while ($row = mysqli_fetch_row($result)) {
        $sql = "select max(a.id_report) from exam_report a where SUBSTRING_INDEX(SUBSTRING_INDEX(a.id_report,'.',2),'.',-1)='$row[0]'";
        $carikode = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error());
        // menjadikannya array
        $datakode = mysqli_fetch_array($carikode);
        // jika $datakode
        if ($datakode) {
            $nilaikode = substr($datakode[0], 2);
            // menjadikan $nilaikode ( int )
            $kode = (int) $nilaikode;
            // setiap $kode di tambah 1
            $kode = $kode + 1;
            $new_id = $id_g.'.'.$row[0].'.'.str_pad($kode, 4, '0', STR_PAD_LEFT);
        } else {
            $new_id = $id_g.'.'.$row[0].'.1';
        }
        print_r($new_id);
        echo '<br>';
    }
    die('disini');

    if (isset($extra)) {
        $sql = "select a.id_voucher,b.cust_name,c.program_name,d.available_val,a.type_voucher from transact_voucher a inner join customer b on a.id_customer=b.id_customer inner join programs c on a.id_program=c.id_program inner join tmp_voucher d on a.id_voucher=d.id_voucher
			where b.id_customer='$id'";
    } else {
        $sql = "select a.*,b.program_name from exam_report a inner join programs b on SUBSTRING_INDEX(SUBSTRING_INDEX(a.id_report,'.',2),'.',-1)=b.id_program where SUBSTRING_INDEX(a.id_report,'.',1)='$id'";
    }
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}

function tanggal_indo($tanggal)
{
    $bulan = array(1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember',
            );
    $split = explode('-', $tanggal);

    return $split[2].' '.$bulan[(int) $split[1]].' '.$split[0];
}

function admRpt_Cust($start, $end, $by, $cust)
{
    $len = count($cust);
    foreach ($cust as $key => $value) {
        if ($key < $len - 1) {
            $list_cust .= "'".$value."',";
        } else {
            $list_cust .= "'".$value."'";
        }
        //echo $list_cust;
    }
    if ($by == 'Voucher') {
        $sql = "select a.id_customer,a.cust_name, b.id_voucher, b.id_program, d.available_val, b.lastest_topup,count(if(c.`status`='Usage',1,null))pakai, sum(c.top_up)top_up ,c.`status` 
		from customer a 
		inner join transact_voucher b on a.id_customer=b.id_customer 
		left join voucher_history c on b.id_voucher=c.id_voucher 
		left join tmp_voucher d on b.id_voucher = d.id_voucher 
		where a.id_customer in ($list_cust) 
		group by b.id_voucher
		order by a.id_customer";
    } elseif ($by == 'Exam') {
        $sql = "select c.id_customer,c.cust_name,b.group_name,a.date,a.classroom,d.fname,b.count_stu,e.use_val,e.unuse_val from exam_schedule a inner join exam_group b on a.exam_group=b.exam_code inner join customer c on SUBSTRING_INDEX(b.group_name,'.',1) = c.id_customer inner join users d on a.proctor = d.id_user inner join exam_group_log e on b.exam_code=e.id_group where c.id_customer in($list_cust) 
		";
    } elseif ($by == 'Program') {
        $sql = "select GROUP_CONCAT(a.exam_code SEPARATOR ',')exam_code,c.program_name,b.cust_name,count(*)ujian from exam_group a inner join customer b on SUBSTRING_INDEX(a.group_name,'.',1) = b.id_customer inner join programs c on SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1) = c.id_program group by SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1)";
    }
    //and c.date >='$start' and c.date <= '$end'

    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}
function admRpt_Voucher_History($start, $end, $voucher)
{
    $len = count($voucher);
    foreach ($voucher as $key => $value) {
        if ($key < $len - 1) {
            $list_voucher .= "'".$value."',";
        } else {
            $list_voucher .= "'".$value."'";
        }
        //echo $list_cust;
    }
    $sql = "select a.id_voucher, d.cust_name, c.program_name, a.group_name, a.count_stu, b.use_val, b.unuse_val,a.exam_code,e.date,e.start_time from exam_group a 
		inner join exam_group_log b on a.exam_code=b.id_group
		inner join programs c on SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1) = c.id_program
		inner join customer d on SUBSTRING_INDEX(a.group_name,'.',1)=d.id_customer
		inner join exam_schedule e on a.exam_code=e.exam_group
		where a.id_voucher in ($list_voucher) 
		and e.date>='$start' and e.date<='$end'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}
function admRpt_Voucher($start, $end, $voucher)
{
    $len = count($voucher);
    foreach ($voucher as $key => $value) {
        if ($key < $len - 1) {
            $list_voucher .= "'".$value."',";
        } else {
            $list_voucher .= "'".$value."'";
        }
        //echo $list_cust;
    }
    $sql = "select a.id_voucher, d.cust_name, c.program_name, a.group_name, a.count_stu, b.use_val, b.unuse_val,a.exam_code,e.date,e.start_time from exam_group a 
		inner join exam_group_log b on a.exam_code=b.id_group
		inner join programs c on SUBSTRING_INDEX(SUBSTRING_INDEX(a.group_name,'.',2),'.',-1) = c.id_program
		inner join customer d on SUBSTRING_INDEX(a.group_name,'.',1)=d.id_customer
		inner join exam_schedule e on a.exam_code=e.exam_group
		where a.id_voucher in ($list_voucher) 
		and e.date>='$start' and e.date<='$end'";
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}
function admRpt_Exam($start, $end, $exam)
{
    $len = count($exam);
    foreach ($exam as $key => $value) {
        if ($key < $len - 1) {
            $list_exam .= "'".$value."',";
        } else {
            $list_exam .= "'".$value."'";
        }
        //echo $list_cust;
    }
    $sql = "select b.group_name,c.fname,a.start_time,a.end_time,sum(d.percentage_true)benar,sum(d.percentage_true+d.percentage_false+d.percentage_null)soal,sum(d.percentage_true)/sum(d.percentage_true+d.percentage_false+d.percentage_null)*100 nilai,e.pass_grade, c.idstudents from exam_session a 
	inner join exam_group b on a.exam_code=b.exam_code
	inner join students c on SUBSTRING_INDEX(a.id_student,'.',1)=c.idstudents
	left join exam_percentage d on a.id_student = d.id_student
	inner join programs e on SUBSTRING_INDEX(SUBSTRING_INDEX(b.group_name,'.',2),'.',-1) = e.id_program
	where b.group_name = '$exam'
	group by a.id_student,d.exam_code order by d.exam_code";
    //and c.date >='$start' and c.date <= '$end'
    $res = mysqli_query($GLOBALS['link'], $sql) or die(mysqli_error($GLOBALS['link']));

    return $res;
}
/*=============================================================================================*/
/*======================================= Export Excel ========================================*/

/*=============================================================================================*/
