<?php
switch ($_GET['pg']) {
	case 'pic_dashboard':
		//include 'view_pic/5_pic_classroom.php';
		include 'view_pic/pic_dashboard.php';
		break;
	case 'pic_user':
		include 'view_pic/1_pic_user.php';
		break;
	case 'pic_student':
		include 'view_pic/2_pic_student.php';
		break;
	case 'pic_add_student':
		include 'view_pic/2_pic_add_student.php';
		break;
	case 'pic_student_remidial':
		include 'view_pic/2_pic_student_remidial.php';
		break;
	case 'pic_student_det':
		include 'view_pic/2_pic_student_det.php';
		break;
	case 'pic_exam':
		include 'view_pic/3_pic_exam.php';
		break;
	case 'pic_exam_schedule':
		include 'view_pic/3_pic_exam_schedule.php';
		break;
	case 'pic_exam_edit':
		include 'view_pic/3_pic_exam_edit.php';
		break;
	case 'pic_exam_create':
		include 'view_pic/3_pic_exam_create.php';
		break;
	case 'pic_exam_result':
		include 'view_pic/3_pic_exam_result.php';
		break;
	case 'pic_create_exam':
		include 'view_pic/3_pic_create_exam.php';
		break;
	case 'pic_sch_detail':
		include 'view_pic/3_pic_sch_detail.php';
		break;
	case 'pic_result_detail':
		include 'view_pic/3_pic_result_detail.php';
		break;
	case 'pic_report':
		include 'view_pic/4_pic_report.php';
		break;
	case 'pic_program_list':
		include 'view_pic/4_pic_program_list.php';
		break;
	case 'pic_report_view':
		include 'view_pic/4_pic_report_view.php';
		break;
	case 'pic_classroom':
		include 'view_pic/5_pic_classroom.php';
		break;
	case 'pic_class_detail':
		include 'view_pic/5_pic_class_detail.php';
		break;
	case 'pic_voucher':
		include 'view_pic/6_pic_voucher.php';
		break;
	case 'pic_detail_voucher':
		include 'view_pic/6_pic_voucher_his.php';
		break;
	case 'pic_report_export':
		include 'view_pic/4_pic_report_export.php';
		break;
	case 'report_sl':
		include 'view_pic/4_report_sl.php';
		break;
	case 'pic_sch_detail_1':
		include 'view_pic/pic_sch_detail.php';
		break;
	default:
		// include 'view_pic/5_pic_classroom.php';
		include 'view_pic/pic_dashboard.php';
		break;
}
?>
