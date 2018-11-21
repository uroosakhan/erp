<?php

class payroll_setup_app extends application
{
	function __construct()
	{
		//$this->application("payroll_setup", _($this->help_context = "Human Resources"));
        parent::__construct("payroll_setup", _($this->help_context = "Human Resources"));

//		$this->add_module(_("Transactions"));
//		$this->add_lapp_function(0, _("&Quotations"),
//			"sales/sales_order_entry.php?NewQuotation=Yes", 'SA_SALESQUOTE', MENU_TRANSACTION);
//		$this->add_lapp_function(0, _("Sales &Orders (S.O)"),
//			"sales/sales_order_entry.php?NewOrder=Yes", 'SA_SALESORDER', MENU_TRANSACTION);
//		//$this->add_lapp_function(0, _("&Dispatch Note),
//		//"sales/sales_order_entry.php?NewDelivery=0", 'SA_SALESDELIVERY', MENU_TRANSACTION);
//
//		$this->add_lapp_function(0, _("&Delivery Note (D.N)"),
//			"sales/inquiry/sales_orders_view.php?OutstandingOnly=1", 'SA_SALESDELIVERY', MENU_TRANSACTION);
//
//		$this->add_lapp_function(0, _("&Credit Invoice"),
//			"sales/inquiry/sales_deliveries_view.php?OutstandingOnly=1", 'SA_SALESINVOICE', MENU_TRANSACTION);
//
//		$this->add_lapp_function(0, "","");
//
//
//
//		//$this->add_rapp_function(0, _("&Template Delivery"),
//		//"sales/inquiry/sales_orders_view.php?DeliveryTemplates=Yes", 'SA_SALESDELIVERY', MENU_TRANSACTION);
//		//$this->add_rapp_function(0, _("&Template Invoice"),
//		//"sales/inquiry/sales_orders_view.php?InvoiceTemplates=Yes", 'SA_SALESINVOICE', MENU_TRANSACTION);
//		//$this->add_rapp_function(0, _("&Create and Print Recurrent Invoices"),
//		//"sales/create_recurrent_invoices.php?", 'SA_SALESINVOICE', MENU_TRANSACTION);
//		$this->add_lapp_function(0, _("Cash &Invoice"),
//			"sales/sales_order_entry.php?NewInvoice=0", 'SA_SALESINVOICE', MENU_TRANSACTION);
//
//		$this->add_rapp_function(0, "","");
//		$this->add_lapp_function(0, _("Receive &Payments"),
//			"sales/customer_payments.php?", 'SA_SALESPAYMNT', MENU_TRANSACTION);
//		$this->add_rapp_function(0, _("&Sale Return"),
//			"sales/credit_note_entry.php?NewCredit=Yes", 'SA_SALESCREDIT', MENU_TRANSACTION);
//		//$this->add_rapp_function(0, _("&Allocate Customer Payments or Credit Notes"),
//		//	"sales/allocations/customer_allocation_main.php?", 'SA_SALESALLOC', MENU_TRANSACTION);

//		$this->add_module(_("Reports"));
//		$this->add_lapp_function(1, _("Quotation &Dashboard"),
//			"sales/inquiry/sales_orders_view.php?type=32", 'SA_SALESTRANSVIEW', MENU_INQUIRY);
//		$this->add_lapp_function(1, _("Sales &Order Dashboard"),
//			"sales/inquiry/sales_orders_view.php?type=30", 'SA_SALESTRANSVIEW', MENU_INQUIRY);
//		$this->add_lapp_function(1, _("Customer &Transaction Dashboard"),
//			"sales/inquiry/customer_inquiry.php?", 'SA_SALESTRANSVIEW', MENU_INQUIRY);
//		$this->add_lapp_function(1, "","");
//		//$this->add_lapp_function(1, _("Customer Allocation Dashboard"),
//		//"sales/inquiry/customer_allocation_inquiry.php?", 'SA_SALESALLOC', MENU_INQUIRY);
//
//		$this->add_rapp_function(1, _("Print &Reports"),
//			"reporting/reports_main.php?Class=0", 'SA_SALESTRANSVIEW', MENU_REPORT);

//		$this->add_module(_("Setup"));
//		$this->add_lapp_function(2, _("Add &Customers"),
//			"sales/manage/customers.php?", 'SA_CUSTOMER', MENU_ENTRY);
//		$this->add_lapp_function(2, _("Add &Branches"),
//			"sales/manage/customer_branches.php?", 'SA_CUSTOMER', MENU_ENTRY);
//		//$this->add_lapp_function(2, _("Sales &Groups"),
//		//"sales/manage/sales_groups.php?", 'SA_SALESGROUP', MENU_MAINTENANCE);
//		//$this->add_lapp_function(2, _("Recurrent &Invoices"),
//		//	"sales/manage/recurrent_invoices.php?", 'SA_SRECURRENT', MENU_MAINTENANCE);
//		$this->add_rapp_function(2, _("Sales T&ypes"),
//			"sales/manage/sales_types.php?", 'SA_SALESTYPES', MENU_MAINTENANCE);
//		$this->add_rapp_function(2, _("Sales &Persons"),
//			"sales/manage/sales_people.php?", 'SA_SALESMAN', MENU_MAINTENANCE);
//		$this->add_rapp_function(2, _("Salary Mode"),
//			"sales/manage/salary.php?", 'SA_SALESAREA', MENU_MAINTENANCE);


		$this->add_module(_("HR Transaction"));
		$this->add_lapp_function(0, _("Add &Employees"),
			"payroll/manage/suppliers2.php?", 'SA_HUMAIN_EMP_SETUP', MENU_ENTRY);
$this->add_rapp_function(0, _("Monthly Man Month"),
			"payroll/manage/man_month_view_new.php", 'SA_HUMAIN_MAN');
$this->add_rapp_function(0, _("Import Attendanec CSV"),
			"modules/import_items/import_emp_attendance.php?", 'SA_HUMAIN_IMP_ATTEN', MENU_MAINTENANCE);
$this->add_rapp_function(0, _("Daily Attendance"),
			"payroll/manage/daily_attendance.php", 'SA_HUMAIN_DAILY_ATTEN');

$this->add_rapp_function(0, _("Monthly Attendance"),
			"payroll/manage/attendance.php", 'SA_HUMAIN_MONTHLY_ATTEN');

		$this->add_rapp_function(0, _("Employee Leave"),
			"payroll/manage/emp_info.php?", 'SA_HUMAIN_EMP_LEAVE');


$this->add_module(_("HR Report"));
		$this->add_lapp_function(1, _("&Employees Dashboard"),
			"payroll/inquiry/supplier_inquiry2.php?", 'SA_HUMAIN_EMP_D', MENU_ENTRY);
$this->add_lapp_function(1, _("&Employees Dashboard Log"),
			"payroll/inquiry/supplier_inquiry_log.php?", 'SA_HUMAIN_EMP_D', MENU_ENTRY);

$this->add_rapp_function(1, _("Leave Inquiry"),
			"payroll/inquiry/leave_inquery.php?", 'SA_HUMAIN_LEAVE_D', MENU_INQUIRY);
$this->add_rapp_function(1, _("Attendance Inquiry"),
			"payroll/inquiry/attendance_inquiry.php?", 'SA_HUMAIN_ATTEN_D', MENU_INQUIRY);



$this->add_rapp_function(1, _("Division Wise Inquiry"),
			"payroll/manage/category2.php?", 'SA_HUMAIN_ATTEN_D', MENU_INQUIRY);

$this->add_rapp_function(1, _("Print &Reports"),
			"reporting/reports_main.php?Class=7", 'SA_PAYROLL_SETUP', MENU_REPORT);

   $this->add_rapp_function(1, _("Employee dashbord demo"),
            "payroll/manage/dashboardemp.php?", 'SA_HUMAIN_ATTEN_D', MENU_INQUIRY);

		$this->add_module(_("HR Setup"));
		$this->add_lapp_function(2, _("Departments"),
			"payroll/manage/dept.php?", 'SA_HUMAIN_DEPT', MENU_ENTRY);
		$this->add_lapp_function(2, _("Designation"),
			"payroll/manage/desg.php?", 'SA_HUMAIN_DESIG', MENU_ENTRY);
		//$this->add_lapp_function(3, _("Gender"),
		//	"payroll/manage/gen.php?", 'SS_PAYROLL', MENU_ENTRY);
		$this->add_lapp_function(2, _("Grade"),
			"payroll/manage/grade.php?", 'SA_HUMAIN_GRADE', MENU_ENTRY);

		$this->add_lapp_function(2, _("Allowances"),
			"payroll/manage/allowance.php?", 'SA_HUMAIN_ALLOWANCE', MENU_ENTRY);
		$this->add_lapp_function(2, _("Deductions"),
			"payroll/manage/deduction.php?", 'SA_HUMAIN_DEDUCTION', MENU_ENTRY);
$this->add_lapp_function(2, _("Attendance Policy"),
			"payroll/manage/attendance_policy.php?", 'SA_HUMAIN_ATTEND_POLICY', MENU_ENTRY);
$this->add_lapp_function(2, _("Leave Type"),
			"payroll/manage/leave_types.php?", 'SA_HUMAIN_L_TYPE', MENU_ENTRY);
		$this->add_lapp_function(2, _("Gazetted Holiday"),
			"payroll/manage/gazetted_holiday.php?", 'SA_HUMAIN_G_HOLI', MENU_ENTRY);
$this->add_lapp_function(2, _("Payment Mode"),
		"payroll/manage/payment_mode.php?", 'SA_HUMAIN_PAY_MODE', MENU_ENTRY);
$this->add_rapp_function(2, _("Document Type"),
			"payroll/manage/document_type.php?", 'SA_HUMAIN_DOC_TYPE', MENU_ENTRY);
        //$this->add_lapp_function(3, _("Title"),
        //	"payroll/manage/title.php?", 'SS_PAYROLL', MENU_ENTRY);
        //$this->add_lapp_function(3, _("Add Company Bank &Accounts"),
        //	"gl/manage/bank_accounts.php?", 'SS_PAYROLL', MENU_MAINTENANCE);
        //$this->add_rapp_function(1, _("GL Setup"),
        //	"payroll/manage/gl_setup.php?", 'SS_PAYROLL', MENU_MAINTENANCE);

        //$this->add_rapp_function(2, _("Credit &Status Setup"),
        //	"sales/manage/credit_status.php?", 'SA_CRSTATUS', MENU_MAINTENANCE);


        //$this->add_lapp_function(1, _("Employee Location"),
        //	"payroll/manage/employee_location.php?", 'SS_PAYROLL', MENU_ENTRY);


        //$this->add_lapp_function(1, _("Divison"),
        //	"payroll/manage/division.php?", 'SS_PAYROLL', MENU_ENTRY);

        //$this->add_lapp_function(1, _("Project"),
        //	"payroll/manage/project.php?", 'SS_PAYROLL', MENU_ENTRY);

		$this->add_extensions();
	}
}


?>