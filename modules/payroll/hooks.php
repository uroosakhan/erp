<?php
define ('SS_PAYROLL', 77<<8);

class payroll_app extends application
{
	function payroll_app()
	{
		$this->application("payroll", _($this->help_context = "Payroll"));

		$this->add_module(_("Transactions"));
		/*
            $this->add_rapp_function(0, _("Place Advertisement"),
              "payroll/manage/advertisment.php?", 'SS_PAYROLL');
          $this->add_rapp_function(0, _("Advertisement Inquiry"),
              "payroll/inquiry/advertisment_inquiry.php?", 'SS_PAYROLL', MENU_INQUIRY);
          $this->add_rapp_function(0, _("Add Applicant"),
              "payroll/manage/add_applicant.php?", 'SS_PAYROLL');
          $this->add_rapp_function(0, _("Applicant Inquiry"),
              "payroll/inquiry/applicant_inquiry.php?", 'SS_PAYROLL', MENU_INQUIRY);
          $this->add_rapp_function(0, _("Employee Evaluation"),
              "payroll/manage/employee_evaluation.php?", 'SS_PAYROLL');
          $this->add_rapp_function(1, _("Employee Documents"),
              "payroll/manage/emplyee_doc.php?", 'SS_PAYROLL');
      */
		$this->add_module(_("Reports"));
		//$this->add_rapp_function(1, _("Import CSV File"),
		//	"modules/payroll/import.php", 'SS_PAYROLL');

 

		

  $this->add_rapp_function(0, _("Difference Current Payroll"),
			"payroll/payroll_currpre_salary.php", 'SS_PAYROLL');


       $this->add_rapp_function(0, _("Payroll Entry"),
			"payroll/payroll.php", 'SS_PAYROLL');



		//$this->add_rapp_function(1, _("Daily Overtime"),
		//	"payroll/manage/overtime_perday.php", 'SS_PAYROLL');

		$this->add_rapp_function(1, _("Employee Advance"),
			"payroll/manage/advance.php?", 'SS_PAYROLL');




		$this->add_rapp_function(0, _("Leave Encashment"),
			"payroll/manage/leave_encashment.php?", 'SS_PAYROLL');

		$this->add_rapp_function(0, _("Gratuity"),
			"payroll/manage/gratuity.php?", 'SS_PAYROLL');

		$this->add_rapp_function(0, _("Bulk Wise Gratuity"),
			"payroll/manage/bulk_wise_gratuity_entry.php?", 'SS_PAYROLL');

		$this->add_rapp_function(0, _("Bulk Wise Leave Encashment"),
			"payroll/manage/bulk_leave_encashment_entry.php?", 'SS_PAYROLL');


		$this->add_rapp_function(0, _("Increment"),
			"payroll/manage/increment.php?", 'SS_PAYROLL');

		

		$this->add_module(_("Setup"));
		//$this->add_rapp_function(2, _("Employee Inquiry"),
		//	"payroll/inquiry/supplier_inquiry2.php?", 'SS_PAYROLL', MENU_INQUIRY);


		//$this->add_rapp_function(2, _("Employee Inquiry Log"),
		//	"payroll/inquiry/supplier_inquiry_log.php?", 'SS_PAYROLL', MENU_INQUIRY);
		//$this->add_rapp_function(2, _("Daily Attendance Inquiry"),
		//	"payroll/inquiry/attendance_per_day.php?", 'SS_PAYROLL', MENU_INQUIRY);

		//$this->add_rapp_function(2, _("Daily Overtime Inquiry"),
		//	"payroll/inquiry/overtime_inquery.php?", 'SS_PAYROLL', MENU_INQUIRY);

		$this->add_rapp_function(1, _("Advance Inquiry"),
			"payroll/inquiry/advance_inquiry.php?", 'SS_PAYROLL', MENU_INQUIRY);

		$this->add_rapp_function(1, _("Leave Encashment Inquiry"),
			"payroll/inquiry/leave_encashment_inquiry.php?", 'SS_PAYROLL', MENU_INQUIRY);

		$this->add_rapp_function(1, _("Gratuity Inquiry"),
			"payroll/inquiry/gratuity_inquiry.php?", 'SS_PAYROLL', MENU_INQUIRY);


		



		$this->add_rapp_function(1, _("Payroll Inquiry"),
			"payroll/inquiry/payroll_inquiry.php?", 'SS_PAYROLL', MENU_INQUIRY);
		
		$this->add_rapp_function(1, _("Increament Inquiry"),
			"payroll/inquiry/increament_inquiry.php?", 'SS_PAYROLL', MENU_INQUIRY);

		$this->add_rapp_function(1, _("Print &Reports"),
			"reporting/reports_main.php?Class=7", 'SS_PAYROLL', MENU_REPORT);

		//$this->add_module(_("Payroll Setup"));
		//$this->add_lapp_function(3, _("Add &Employees"),
		//	"payroll/manage/suppliers2.php?", 'SS_PAYROLL', MENU_ENTRY);
		//$this->add_lapp_function(3, _("Departments"),
		//	"payroll/manage/dept.php?", 'SS_PAYROLL', MENU_ENTRY);


		//$this->add_lapp_function(3, _("Employee Location"),
		//	"payroll/manage/employee_location.php", 'SS_PAYROLL', MENU_ENTRY);

		//$this->add_lapp_function(3, _("Divison"),
		//	"payroll/manage/division.php?", 'SS_PAYROLL', MENU_ENTRY);

		//$this->add_lapp_function(3, _("Project"),
		//	"payroll/manage/project.php?", 'SS_PAYROLL', MENU_ENTRY);

		//$this->add_lapp_function(3, _("Designation"),
		//	"payroll/manage/desg.php?", 'SS_PAYROLL', MENU_ENTRY);
		//$this->add_lapp_function(3, _("Gender"),
		//	"payroll/manage/gen.php?", 'SS_PAYROLL', MENU_ENTRY);
		//$this->add_lapp_function(3, _("Grade"),
		//	"payroll/manage/grade.php?", 'SS_PAYROLL', MENU_ENTRY);
		//$this->add_lapp_function(3, _("Title"),
		//	"payroll/manage/title.php?", 'SS_PAYROLL', MENU_ENTRY);
		//$this->add_lapp_function(3, _("Add Company Bank &Accounts"),
		//	"gl/manage/bank_accounts.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
		//$this->add_lapp_function(3, _("Allowances"),
		//	"payroll/manage/allowance.php?", 'SS_PAYROLL', MENU_ENTRY);
		//$this->add_lapp_function(3, _("Deductions"),
		//	"payroll/manage/deduction.php?", 'SS_PAYROLL', MENU_ENTRY);
		

		$this->add_rapp_function(2, _("GL Setup"),
			"payroll/manage/gl_setup.php?", 'SS_PAYROLL', MENU_MAINTENANCE);

		


		
		

		

		//$this->add_rapp_function(3, _("Location"),
		//	"payroll/manage/location.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
		//$this->add_rapp_function(3, _("Gazetted Holidays"),
		//	"payroll/manage/gazetted_holiday.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
		//$this->add_rapp_function(3, _("Grade Leave Setup"),
		//	"payroll/manage/grade_leave.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
		//$this->add_rapp_function(3, _("Salary Break Up Formula"),
		//	"payroll/manage/salary_breakup_formula.php?", 'SS_PAYROLL', MENU_ENTRY);
		//$this->add_rapp_function(3, _("Document Types"),
		//	"payroll/manage/document_type.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
		

$this->add_lapp_function(2, _("Current Tax Slab"),
			"payroll/manage/taxrate.php?", 'SS_PAYROLL', MENU_ENTRY);



		//$this->add_rapp_function(3, _("Category"),
		//	"payroll/manage/category.php?", 'SS_PAYROLL', MENU_ENTRY);

		//$this->add_rapp_function(3, _("Final Result"),
		//	"payroll/manage/final_result.php?", 'SS_PAYROLL', MENU_ENTRY);
		/*
               $this->add_module(_("Recruitment Setup"));
               $this->add_rapp_function(4, _("Qualification"),
                   "payroll/manage/qualification.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
               $this->add_rapp_function(4, _("Training"),
                   "payroll/manage/training.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
               $this->add_rapp_function(4, _("Nationality"),
                   "payroll/manage/nationality.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
               $this->add_rapp_function(4, _("Job Type"),
                   "payroll/manage/job_type.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
               $this->add_rapp_function(4, _("Age"),
                   "payroll/manage/age.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
               $this->add_lapp_function(4, _("Salary Range"),
                   "payroll/manage/salary_range.php?", 'SS_PAYROLL', MENU_ENTRY);
               $this->add_lapp_function(4, _("Travel"),
                   "payroll/manage/travel.php?", 'SS_PAYROLL', MENU_ENTRY);
               $this->add_lapp_function(4, _("Experience"),
                   "payroll/manage/experience.php?", 'SS_PAYROLL', MENU_ENTRY);

       */

		$this->add_extensions();
	}
}


class hooks_payroll extends hooks {
	var $module_name = 'Payroll'; 

	/*
		Install additonal menu options provided by module
	*/

	function install_tabs($app)
	{
        $app->add_application(new payroll_app());
	}

/*	function install_options($app) {
		global $path_to_root;

		switch($app->id) {
			case 'GL':
				$app->add_rapp_function(2, _('Import 2'), 
					$path_to_root.'/modules/payroll/import.php', 'SS_PAYROLL');
		}
	}
*/
	function install_access()
	{
		$security_sections[SS_PAYROLL] =	_("Payroll");

		$security_areas['SS_PAYROLL'] = array(SS_PAYROLL|77, _("Payroll"));

		return array($security_areas, $security_sections);
	}
}
?>