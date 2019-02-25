<ul class="nav menu">
	<li <?php if(!isset($_GET['pg']) 
		OR $_GET['pg'] == 'pic_dashboard')  {echo 'class="active"';}?>>
		<a href="?pg=pic_dashboard"><em class="fa fa-dashboard">&nbsp;</em> Dashboard</a>
	</li>
	<li <?php if(isset($_GET['pg']) 
		AND $_GET['pg'] == 'pic_voucher' OR $_GET['pg'] == 'pic_detail_voucher'){echo 'class="active"';}?>>
		<a href="?pg=pic_voucher"><em class="fa fa-tasks">&nbsp;</em> Programs</a> 
	</li>
	<li <?php if(isset($_GET['pg']) 
		AND $_GET['pg'] == 'pic_user' ){echo 'class="active"';} ?> >
		<a href="?pg=pic_user">&nbsp;<em class="fa fa-user"></em>&nbsp;&nbsp;Users</a>
	</li>
	<li  <?php if(isset($_GET['pg']) 
		AND $_GET['pg'] == 'pic_student' 
		OR $_GET['pg'] =='pic_student_det' 
		OR $_GET['pg'] =='pic_add_student'
		OR $_GET['pg'] =='pic_student_remidial'){echo 'class="parent active"';} ?> >
		<a data-toggle="collapse" href="#sub-item-1"><em class="fa fa-graduation-cap">&nbsp;</em>Student<span data-toggle="collapse" href="#sub-item-1" class="icon pull-right"><em class="fa fa-plus" style="padding-top: 6px;"></em></span>
		</a>
		<ul class="children collapse" id="sub-item-1">
			<li <?php if (isset($_GET['pg']) AND $_GET['pg'] == 'pic_student') {echo 'class="active"';} ?>><a class="" href="?pg=pic_add_student">
				<span class="fa fa-plus">&nbsp;</span> Add Student
			</a></li>
			<li><a class="" href="?pg=pic_student">
				<span class="fa fa-address-card-o">&nbsp;</span> Student Details
			</a></li>
			<li><a class="" href="?pg=pic_student_remidial">
				<span class="fa fa-list">&nbsp;</span> Student Remidial
			</a></li>
		</ul>
	</li>
	<li  <?php if (isset($_GET['pg']) 
		AND $_GET['pg'] == 'pic_classroom' 
		OR $_GET['pg'] == 'pic_class_detail') {echo 'class="active"';} ?> >
		<a href="?pg=pic_classroom"><em class="fa fa-fort-awesome">&nbsp;&nbsp;</em>Test Class</a>
	</li>
	<li <?php if (isset($_GET['pg']) 
		AND $_GET['pg'] == 'pic_exam' 
		OR $_GET['pg'] == 'pic_create_exam'
		OR $_GET['pg'] == 'pic_exam_create'
		OR $_GET['pg'] == 'pic_exam_schedule'
		OR $_GET['pg'] == 'pic_exam_result' 
		OR $_GET['pg'] == 'pic_sch_detail' 
		OR $_GET['pg'] == 'pic_result_detail') {echo 'class="active"';} ?>>
		<a data-toggle="collapse" href="#sub-item-2">
			<em class="fa fa-navicon">&nbsp;</em> Exam 
			<span data-toggle="collapse" class="icon pull-right">
				<em class="fa fa-plus" style="padding-top: 6px;"></em>
			</span>
		</a>
		<ul class="children collapse" id="sub-item-2">
			<li <?php if (isset($_GET['pg']) AND $_GET['pg'] == 'pic_exam') {echo 'class="active"';} ?>>
			<a class="" href="?pg=pic_exam_create">
				<span class="fa fa-book">&nbsp;</span> Create Exam
			</a></li>
			<li><a class="" href="?pg=pic_exam_schedule">
				<span class="fa fa-calendar">&nbsp;</span> Exam Schedule
			</a></li>
			<li><a class="" href="?pg=pic_exam_result">
				<span class="fa fa-list-ol">&nbsp;</span> Exam Result
			</a></li>
		</ul>
	</li>
	<!-- <li <?php if (isset($_GET['pg']) AND $_GET['pg'] == 'pic_exam' OR $_GET['pg'] == 'pic_create_exam' OR $_GET['pg'] == 'pic_sch_detail') {echo 'class="active"';} ?> >
		<a href="?pg=pic_exam"><em class="fa fa-book">&nbsp;&nbsp;</em> Exam</a>
	</li> -->
	<li <?php if(isset($_GET['pg']) AND $_GET['pg'] == 'pic_report' OR $_GET['pg'] == 'pic_report_view' ){echo 'class="active"';} ?> >
		<a href="?pg=pic_report"><em class="fa fa-file-text-o">&nbsp;&nbsp;&nbsp;</em>Report</a>
	</li>
	<!-- <li <?php# if(isset($_GET['pg']) AND $_GET['pg'] == 'pic_program_list' OR $_GET['pg'] == 'pic_report_view' ){echo 'class="active"';} ?> >
		<a href="?pg=pic_program_list"><em class="fa fa-file-text-o">&nbsp;</em>Program List</a>
	</li> -->
	<!-- <li>
		<a href="logout.php"><em class="fa fa-toggle-off">&nbsp;</em>Logout</a>
	</li> -->
</ul>
