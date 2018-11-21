<?php

/*
	Security sections groups various areas on both functionality and privilege levels.
	Often analytic inquires are available only for management, and configuration
	for administration or management staff. This is why we have those three
	section type inside near every FA module.

	Section codes 0-99 are reserved for core FA functionalities.
	Every security section can contain up to 256 different areas.
	External modules can extend security roles system by adding rows to 
	$security_sections and $security_areas using section codes >=100.
	Security areas and sections created by extension modules/plugins
	have dynamically assigned 3-byte integer codes. The highest byte is zero
	for sections/areas defined in this file, and extid+1 for those defined 
	by extensions 
*/
define('SS_SADMIN',	1<<8);	// site admin
define('SS_SETUP',	2<<8);	// company level setup
define('SS_SPEC',	3<<8);	// special administration

define('SS_SALES_C',11<<8); // configuration
define('SS_SALES',	12<<8);	// transactions
define('SS_SALES_A',13<<8); // analytic functions/reports/inquires

define('SS_PURCH_C',21<<8);
define('SS_PURCH',	22<<8);
define('SS_PURCH_A',23<<8);

define('SS_ITEMS_C',31<<8);
define('SS_ITEMS',	32<<8);
define('SS_ITEMS_A',33<<8);
define('SS_ITEMS_B',34<<8);

define('SS_ASSETS_C',36<<8);
define('SS_ASSETS',	37<<8);
define('SS_ASSETS_A',38<<8);

define('SS_MANUF_C',41<<8);
define('SS_MANUF',	42<<8);
define('SS_MANUF_A',43<<8);

define('SS_DIM_C',	51<<8);
define('SS_DIM',	52<<8);
define('SS_DIM_A',	53<<8);

define('SS_GL_C',	61<<8);
define('SS_GL',		62<<8);
define('SS_GL_A',	63<<8);
define('SS_CI_A',	64<<8);
define('SS_ACC_TRAN', 65<<8);
define('SS_APPROVAL', 66<<8);
define('SS_DASHBOARD', 67<<8);
define('SS_APPROVAL_IP', 68<<8);
define('SS_APPROVAL2', 69<<8);
define('SS_GRAPH', 70<<8);

define('SS_HUMAIN_C',101<<8);
define('SS_HUMAIN',	102<<8);
define('SS_HUMAIN_A',103<<8);

define('SS_PAYROLL_M_C',111<<8);
define('SS_PAYROLL_M',	112<<8);
define('SS_PAYROLL_M_A',113<<8);
define('SS_CRM_A',114<<8);
define('SS_CRM_B',115<<8);
define('SS_CRM_C',116<<8);


$security_sections = array(

 	SS_SADMIN => _("System administration"),
 	SS_SETUP => _("Company setup"),
 	SS_SPEC => _("Special maintenance"),
 	SS_SALES_C => _("Sales configuration"),
 	SS_SALES => _("Sales transactions"),
 	SS_SALES_A => _("Sales related reports"),
 	SS_PURCH_C => _("Purchase configuration"),
 	SS_PURCH => _("Purchase transactions"),
 	SS_PURCH_A => _("Purchase analytics"),
 	SS_ITEMS_C => _("Inventory configuration"),
 	SS_ITEMS => _("Inventory operations"),
 	SS_ITEMS_A => _("Inventory analytics"),
 	SS_ITEMS_B => _("Inventory Reports"), 	
	SS_ASSETS_C => _("Fixed Assets configuration"),
	SS_ASSETS => _("Fixed Assets operations"),
	SS_ASSETS_A => _("Fixed Assets analytics"),
 	SS_MANUF_C => _("Manufacturing configuration"),
 	SS_MANUF => _("Manufacturing transactions"),
 	SS_MANUF_A => _("Manufacturing analytics"),
 	SS_DIM_C => _("Dimensions configuration"),
 	SS_DIM => _("Dimensions"),
 	SS_GL_C => _("Banking & GL configuration"),
 	SS_GL => _("Banking & GL transactions"),
 	SS_GL_A => _("Banking & GL analytics"),
 	SS_CI_A => _("Complete Invoice"),
 	SS_ACC_TRAN => _("Account Transfer"),
 	SS_APPROVAL => _("Voucher Approval"),
 	SS_GRAPH => _("Graph Approval"),
 	SS_APPROVAL_IP => _("Static Ip Bypass"),
 	SS_DASHBOARD =>	_("Dashboard View"),
 	SS_APPROVAL2 => _("Approval System"),

    SS_HUMAIN_C => _("Human configuration"),
    SS_HUMAIN => _("Human operations"),
    SS_HUMAIN_A => _("Human analytics"),

    SS_PAYROLL_M_C => _("Payroll configuration"),
    SS_PAYROLL_M => _("Payroll operations"),
    SS_PAYROLL_M_A => _("Payroll analytics"),
     SS_CRM_A => _("CRM  Analytics"),
    SS_CRM_B => _("CRM  Inquiry"),
    SS_CRM_C => _("CRM  Setup")


);

/*
	This table stores security areas available in FA. 
	Key is area identifier used to check user rights, values are
	codes stored for each role in security_roles table and description used
	in roles editor.

	Set of allowed access areas codes is retrieved during user login from
	security_roles table, and cached in user profile.

	Special value 'SA_OPEN' is used for publicly available pages like login/logout.
*/
$security_areas =array(
//
//	Site administration
//
	'SA_CREATECOMPANY' =>array(SS_SADMIN|1, _("Install/update companies")),
	'SA_CREATELANGUAGE' => array(SS_SADMIN|2, _("Install/update languages")),
	'SA_CREATEMODULES' => array(SS_SADMIN|3, _("Install/upgrade modules")),
	'SA_SOFTWAREUPGRADE' => array(SS_SADMIN|4, _("Software upgrades")),
//
//	Company setup
//
	'SA_SETUPCOMPANY' => array(SS_SETUP|1, _("Company parameters")),
	'SA_SECROLES' => array(SS_SETUP|2, _("Access levels edition")),
	'SA_USERS' => array(SS_SETUP|3, _("Users setup")),
	'SA_POSSETUP' => array(SS_SETUP|4, _("Point of sales definitions")),
	'SA_PRINTERS' => array(SS_SETUP|5, _("Printers configuration")),
	'SA_PRINTPROFILE' => array(SS_SETUP|6, _("Print profiles")),
	'SA_PAYTERMS' => array(SS_SETUP|7, _("Payment terms")),
	'SA_SHIPPING' => array(SS_SETUP|8, _("Shipping ways")),
	'SA_CRSTATUS' => array(SS_SETUP|9, _("Credit status definitions changes")),
	'SA_INVENTORYLOCATION' => array(SS_SETUP|10, _("Inventory locations changes")),
	'SA_INVENTORYMOVETYPE'  => array(SS_SETUP|11, _("Inventory movement types")),
	'SA_WORKCENTRES' => array(SS_SETUP|12, _("Manufacture work centres")),
	'SA_FORMSETUP' => array(SS_SETUP|13, _("Forms setup")),
	'SA_CRMCATEGORY' => array(SS_SETUP|14, _("Contact categories")),
//
// Special and common functions
//
	'SA_VOIDTRANSACTION' => array(SS_SPEC|1, _("Voiding transactions")),
	'SA_BACKUP' => array(SS_SPEC|2, _("Database backup/restore")),
	'SA_VIEWPRINTTRANSACTION' => array(SS_SPEC|3, _("Common view/print transactions interface")),
	'SA_ATTACHDOCUMENT' => array(SS_SPEC|4, _("Attaching documents")),
	'SA_SETUPDISPLAY' => array(SS_SPEC|5, _("Display preferences")),
	'SA_CHGPASSWD' => array(SS_SPEC|6, _("Password changes")),
	'SA_EDITOTHERSTRANS' => array(SS_SPEC|7, _("Edit other users transactions")),
//
// Sales related functionality
//
	'SA_SALESTYPES' => array(SS_SALES_C|1, _("Sales types")),
	'SA_SALESPRICE' => array(SS_SALES_C|2, _("Sales prices edition")),
	'SA_SALESMAN' => array(SS_SALES_C|3, _("Sales staff maintenance")),
	'SA_SALESAREA' => array(SS_SALES_C|4, _("Sales areas maintenance")),
	'SA_SALESGROUP' => array(SS_SALES_C|5, _("Sales groups changes")),
	'SA_STEMPLATE' => array(SS_SALES_C|6, _("Sales templates")),
	'SA_SRECURRENT' => array(SS_SALES_C|7, _("Recurrent invoices definitions")),
    'SA_PRICES'   => array(SS_SALES_C|8, _("Restrict price editing one Sales Form")),
	'SA_DISCOUNT' => array(SS_SALES_C|9, _("Restrict discount editing on Sales Form")),
    'SA_SALESORDER_PDF' => array(SS_SALES_C|10, _("Restrict Price & Amount from SO PDF")),


	'SA_SALESTRANSVIEW' => array(SS_SALES|1,  _("Sales transactions view")),
	'SA_CUSTOMER' => array(SS_SALES|2,  _("Sales customer and branches changes")),
	'SA_SALESQUOTE' => array(SS_SALES|10, _("Sales quotations")),
	'SA_SALESORDER' => array(SS_SALES|3, _("Sales orders edition")),
	'SA_SALESDELIVERY' => array(SS_SALES|4, _("Sales deliveries edition")),
	'SA_SALESINVOICE' => array(SS_SALES|5, _("Sales invoices edition")),
	'SA_SALESCREDITINV' => array(SS_SALES|6, _("Sales credit notes against invoice")),
	'SA_SALESCREDIT' => array(SS_SALES|7, _("Sales freehand credit notes")),
	'SA_SALESPAYMNT' => array(SS_SALES|8, _("Customer payments entry")),
	'SA_SALESALLOC' => array(SS_SALES|9, _("Customer payments allocation")),

	'SA_SALESANALYTIC' => array(SS_SALES_A|1, _("Sales analytical reports")),
	'SA_SALESBULKREP' => array(SS_SALES_A|2, _("Sales document bulk reports")),
	'SA_PRICEREP' => array(SS_SALES_A|3, _("Sales prices listing")),
	'SA_SALESMANREP' => array(SS_SALES_A|4, _("Sales staff listing")),
	'SA_CUSTBULKREP' => array(SS_SALES_A|5, _("Customer bulk listing")),
	
	'SA_CUSTSTATREP' => array(SS_SALES_A|6, _("Customer status report")),
	'SA_CUSTPAYMREP' => array(SS_SALES_A|7, _("Customer payments report")),
	'SA_SUMMMREP' => array(SS_SALES_A|8, _("Sales Summary report")),
    
//
// Purchase related functions
//
	'SA_PURCHASEPRICING' => array(SS_PURCH_C|1, _("Purchase price changes")),

	'SA_SUPPTRANSVIEW' => array(SS_PURCH|1, _("Supplier transactions view")),
	'SA_SUPPLIER' => array(SS_PURCH|2, _("Suppliers changes")),
	'SA_PURCHASEORDER' => array(SS_PURCH|3, _("Purchase order entry")),
	'SA_GRN' => array(SS_PURCH|4, _("Purchase receive")),
	'SA_DIRECT_GRN' => array(SS_PURCH|10, _("Direct GRN")),
	'SA_SUPPLIERINVOICE' => array(SS_PURCH|5, _("Supplier invoices")),
	'SA_GRNDELETE' => array(SS_PURCH|9, _("Deleting GRN items during invoice entry")),
	'SA_SUPPLIERCREDIT' => array(SS_PURCH|6, _("Supplier credit notes")),
	'SA_SUPPLIERPAYMNT' => array(SS_PURCH|7, _("Supplier payments")),
	'SA_SUPPLIERALLOC' => array(SS_PURCH|8, _("Supplier payments allocations")),

	'SA_SUPPLIERANALYTIC' => array(SS_PURCH_A|1, _("Supplier analytical reports")),
	'SA_SUPPBULKREP' => array(SS_PURCH_A|2, _("Supplier document bulk reports")),
	'SA_SUPPPAYMREP' => array(SS_PURCH_A|3, _("Supplier payments report")),
	'SA_SUPPTAX' => array(SS_PURCH_A|4, _("Purchase Tax report")),
	'SA_SUPPPRICES' => array(SS_PURCH_A|5, _("Restrict Prices on Purchase Document")),
	
	
	
//
// Inventory 
//
	'SA_ITEM' => array(SS_ITEMS_C|1, _("Stock items add/edit")),
	'SA_SALESKIT' => array(SS_ITEMS_C|2, _("Sales kits")),
	'SA_ITEMCATEGORY' => array(SS_ITEMS_C|3, _("Item categories")),
	'SA_UOM' => array(SS_ITEMS_C|4, _("Units of measure")),

	'SA_ITEMSSTATVIEW' => array(SS_ITEMS|1, _("Stock status view")),
	'SA_ITEMSTRANSVIEW' => array(SS_ITEMS|2, _("Stock transactions view")),
	'SA_FORITEMCODE' => array(SS_ITEMS|3, _("Foreign item codes entry")),
	'SA_LOCATIONTRANSFER' => array(SS_ITEMS|4, _("Inventory location transfers")),
	'SA_INVENTORYADJUSTMENT' => array(SS_ITEMS|5, _("Inventory adjustments")),

	'SA_REORDER' => array(SS_ITEMS_A|1, _("Reorder levels")),
'SA_ITEMSPLANNING' => array(SS_ITEMS_A|2, _("Items analytical reports and inquiries")),

	'SA_ITEMSPRICES' => array(SS_ITEMS_A|4, _("Restrict prices on inventory reports")),
	
//inventory reports
	'SA_ITEMSVALREP' => array(SS_ITEMS_B|1, _("Inventory valuation report")),
    'SA_ITEMSPLANNINGREPORT' => array(SS_ITEMS_A|2, _("Inventory planning report")),	

	'SA_MONTHLYITEMISEDSALE' => array(SS_ITEMS_B|3, _("Monhtly itemised sales report")),

	'SA_ITEMS_STOCK' => array(SS_ITEMS_B|4, _("Stock check sheet report")),

	'SA_SALESREP' => array(SS_ITEMS_B|5, _("Inventory sales report")),
	'SA_SALESREP_ITEM' => array(SS_ITEMS_B|6, _("Inventory sales report item wise & summary report ")),
	'SA_DAILY_SALES' => array(SS_ITEMS_B|7, _("Daily sales report")),
	'SA_GRNVALREP' => array(SS_ITEMS_B|8, _("GRN valuation report")),
	'SA_ITEMSPURREP' => array(SS_ITEMS_B|9, _("Inventory purchasing report")),
	'SA_ITEMSMOVREP' => array(SS_ITEMS_B|10, _("Inventory Movement reports")),
	'SA_STOCKREP' => array(SS_ITEMS_B|11, _("Inventory stock report")),
	'SA_COSTREP' => array(SS_ITEMS_B|12, _("Costed inventory movement report")),
	'SA_ITEMSALESSUMREP' => array(SS_ITEMS_B|13, _("Item sales summary report")),
	'SA_DATED_STOCK' => array(SS_ITEMS_B|14, _("Dated stock sheet")),
	'SA_SALES_SUMM_REP' => array(SS_ITEMS_B|15, _("Sales summary report")),
	'SA_INVENTORY_REP' => array(SS_ITEMS_B|16, _("Inventory history")),
		'SA_SUPPLY_REP' => array(SS_ITEMS_B|17, _("Supply register")),
	'SA_BARREP' => array(SS_ITEMS_B|18, _("Multi barcode printing new report")),
	'SA_LOCREORDER' => array(SS_ITEMS_B|19, _("Location wise Re-Order report")),
	'SA_LOCTRANSFER' => array(SS_ITEMS_B|20, _("Location transfer reports")),	
//
// Fixed Assets
//
	'SA_ASSET' => array(SS_ASSETS_C|1, _("Fixed Asset items add/edit")),
	'SA_ASSETCATEGORY' => array(SS_ASSETS_C|2, _("Fixed Asset categories")),
	'SA_ASSETCLASS' => array(SS_ASSETS_C|4, _("Fixed Asset classes")),

	'SA_ASSETSTRANSVIEW' => array(SS_ASSETS|1, _("Fixed Asset transactions view")),
	'SA_ASSETTRANSFER' => array(SS_ASSETS|2, _("Fixed Asset location transfers")),
	'SA_ASSETDISPOSAL' => array(SS_ASSETS|3, _("Fixed Asset disposals")),
    'SA_DEPRECIATION' => array(SS_ASSETS|4, _("Depreciation")),
	'SA_SALES_FIXEDASSETS' => array(SS_ASSETS|3, _("Fixed Asset disposals")),
	'SA_ASSETSANALYTIC' => array(SS_ASSETS_A|1, _("Fixed Asset analytical reports and inquiries")),

//
// Manufacturing module 
//
	'SA_BOM' => array(SS_MANUF_C|1, _("Bill of Materials")),

	'SA_MANUFTRANSVIEW' => array(SS_MANUF|1, _("Manufacturing operations view")),
	'SA_WORKORDERENTRY' => array(SS_MANUF|2, _("Work order entry")),
	'SA_MANUFISSUE' => array(SS_MANUF|3, _("Material issues entry")),
	'SA_MANUFRECEIVE' => array(SS_MANUF|4, _("Final product receive")),
	'SA_MANUFRELEASE' => array(SS_MANUF|5, _("Work order releases")),
    'SA_WORKORDERENTRYREQ' => array(SS_MANUF|6, _("Work order requisition entry")),
	'SA_WORKORDERANALYTIC' => array(SS_MANUF_A|1, _("Work order analytical reports and inquiries")),
	'SA_WORKORDERCOST' => array(SS_MANUF_A|2, _("Manufacturing cost inquiry")),
	'SA_MANUFBULKREP' => array(SS_MANUF_A|3, _("Work order bulk reports")),
	'SA_BOMREP' => array(SS_MANUF_A|4, _("Bill of materials reports")),
//
// Dimensions
//
	'SA_DIMTAGS' => array(SS_DIM_C|1, _("Dimension tags")),

	'SA_DIMTRANSVIEW' => array(SS_DIM|1, _("Dimension view")),

	'SA_DIMENSION' => array(SS_DIM|2, _("Dimension entry")),

	'SA_DIMENSIONREP' => array(SS_DIM|3, _("Dimension reports")),
//
// Banking and General Ledger
//
	'SA_ITEMTAXTYPE' => array(SS_GL_C|1, _("Item tax type definitions")),
	'SA_GLACCOUNT' => array(SS_GL_C|2, _("GL accounts edition")),
	'SA_GLACCOUNTGROUP' => array(SS_GL_C|3, _("GL account groups")),
	'SA_GLACCOUNTCLASS' => array(SS_GL_C|4, _("GL account classes")),
	'SA_QUICKENTRY' => array(SS_GL_C|5, _("Quick GL entry definitions")),
	'SA_CURRENCY' => array(SS_GL_C|6, _("Currencies")),
	'SA_BANKACCOUNT' => array(SS_GL_C|7, _("Bank accounts")),
	'SA_TAXRATES' => array(SS_GL_C|8, _("Tax rates")),
	'SA_TAXGROUPS' => array(SS_GL_C|12, _("Tax groups")),
	'SA_FISCALYEARS' => array(SS_GL_C|9, _("Fiscal years maintenance")),
	'SA_GLSETUP' => array(SS_GL_C|10, _("Company GL setup")),
	'SA_GLACCOUNTTAGS' => array(SS_GL_C|11, _("GL Account tags")),
	'SA_GLCLOSE' => array(SS_GL_C|14, _("Closing GL transactions")),
	'SA_GLREOPEN' => array(SS_GL_C|15, _("Reopening GL transactions")), // see below
	'SA_MULTIFISCALYEARS' => array(SS_GL_C|13, _("Allow entry on non closed Fiscal years")),

	'SA_BANKTRANSVIEW' => array(SS_GL|1, _("Bank transactions view")),
	'SA_GLTRANSVIEW' => array(SS_GL|2, _("GL postings view")),
	'SA_EXCHANGERATE' => array(SS_GL|3, _("Exchange rate table changes")),
	'SA_PAYMENT' => array(SS_GL|4, _("Bank payments")),
	'SA_DEPOSIT' => array(SS_GL|5, _("Bank deposits")),
	'SA_BANKTRANSFER' => array(SS_GL|6, _("Bank account transfers")),
	'SA_RECONCILE' => array(SS_GL|7, _("Bank reconciliation")),
	'SA_JOURNALENTRY' => array(SS_GL|8, _("Manual journal entries")),
	'SA_BANKJOURNAL' => array(SS_GL|11, _("Journal entries to bank related accounts")),
	'SA_BUDGETENTRY' => array(SS_GL|9, _("Budget edition")),
	'SA_STANDARDCOST' => array(SS_GL|10, _("Item standard costs")),
	'SA_ACCRUALS' => array(SS_GL|12, _("Revenue / Cost Accruals")),

	'SA_GLANALYTIC' => array(SS_GL_A|1, _("GL analytical reports and inquiries")),
	'SA_TAXREP' => array(SS_GL_A|2, _("Tax reports and inquiries")),
	'SA_BANKREP' => array(SS_GL_A|3, _("Bank reports and inquiries")),
	'SA_GLREP' => array(SS_GL_A|4, _("GL reports and inquiries")),
	'SA_COMPLETEINVOICE' => array(SS_CI_A|16, _("Manual Complete Invoice")),
	'SA_ACCOUNTTRANSFER' => array(SS_ACC_TRAN|17, _("Account Transfer")),
	'SA_VOUCHERAPPROVAL' => array(SS_APPROVAL|18, _("Voucher Approval")),
	'SA_IPAPPROVAL' => array(SS_APPROVAL_IP|19, _("Static Ip Bypass")),
	
	'SS_DASHBOARD_VIEW' => array(SS_DASHBOARD|1, _("Dashboard View")),

	'SA_INCOME' => array(SS_GRAPH|1, _("Sales and Recovery Graphs")),
    'SA_INCOME2' => array(SS_GRAPH|2, _("Purchase and PaymentGraphs")),
    
	'SS_RECIEPT_ORDER'=> array(SS_DASHBOARD|2, _("Outstanding receipt in next 30 days")),
    'SS_PAYMENTS'     => array(SS_DASHBOARD|3, _("Outstanding payments in next 30 days")),
    'SS_AUDIT_TRAIL_VIEW'     => array(SS_DASHBOARD|4, _("Audit Trail View(Bell Icon)")),

    'SA_DISPATCHAPPROVAL' => array(SS_APPROVAL2|1, _("Sales Order Approval")),
    'SA_DISPATCHAPPROVAL2' => array(SS_APPROVAL2|2, _("Purchase Order Approval")),
    'SA_DISPATCHAPPROVAL3' => array(SS_APPROVAL2|3, _("Delivery Note Approval")),
    'SA_DISPATCHAPPROVAL4' => array(SS_APPROVAL2|4, _("GRN Approval")),
    'SA_DISPATCHAPPROVAL5' => array(SS_APPROVAL2|5, _("Restrict Requisition Approval")),
    'SA_DISPATCHAPPROVAL6' => array(SS_APPROVAL2|6, _("Restrict Customer Credit Limit on Sales Order")),
    'SA_DISPATCHAPPROVAL7' => array(SS_APPROVAL2|7, _("Location Transfer Approval")),

    'SA_HUMAIN_EMP_SETUP' => array(SS_HUMAIN_C|1, _("Human Add Employee")),
    'SA_HUMAIN_MAN' => array(SS_HUMAIN_C|2, _("Human Man Month")),
    'SA_HUMAIN_IMP_ATTEN' => array(SS_HUMAIN_C|3, _("Human Import Attendance")),
    'SA_HUMAIN_DAILY_ATTEN' => array(SS_HUMAIN_C|4, _("Human Daily Attendance")),
    'SA_HUMAIN_MONTHLY_ATTEN' => array(SS_HUMAIN_C|5, _("Human Monthly Attendance")),
    'SA_HUMAIN_EMP_LEAVE' => array(SS_HUMAIN_C|6, _("Human Employee Leave")),
    //Operation
    'SA_HUMAIN_EMP_D' => array(SS_HUMAIN|1, _("Human Employee Dashboard")),
    'SA_HUMAIN_LEAVE_D' => array(SS_HUMAIN|2, _("Human Employee Leave Dashboard")),
    'SA_HUMAIN_ATTEN_D' => array(SS_HUMAIN|3, _("Human Employee Attendance Dashboard")),
    //setup
    'SA_HUMAIN_DEPT' => array(SS_HUMAIN_A|1, _("Human Department")),
    'SA_HUMAIN_DESIG' => array(SS_HUMAIN_A|2, _("Human Designation")),
    'SA_HUMAIN_GRADE' => array(SS_HUMAIN_A|3, _("Human Grade")),
    'SA_HUMAIN_ALLOWANCE' => array(SS_HUMAIN_A|4, _("Human Allowance")),
    'SA_HUMAIN_DEDUCTION' => array(SS_HUMAIN_A|5, _("Human Deduction")),
    'SA_HUMAIN_ATTEND_POLICY' => array(SS_HUMAIN_A|6, _("Human Attendance Policy")),
    'SA_HUMAIN_L_TYPE' => array(SS_HUMAIN_A|7, _("Human Leave Type")),
    'SA_HUMAIN_G_HOLI' => array(SS_HUMAIN_A|8, _("Human Gazzet Holiday")),
    'SA_HUMAIN_PAY_MODE' => array(SS_HUMAIN_A|9, _("Human Payment Mode")),
    'SA_HUMAIN_DOC_TYPE' => array(SS_HUMAIN_A|10, _("Human Document Type")),


    //Configration
    'SA_PAYROLL_PAYROLL' => array(SS_PAYROLL_M_C|4, _("Payroll Entery")),
    'SA_PAYROLL_ADVANCE' => array(SS_PAYROLL_M_C|1, _("Advance Entery")),
    'SA_PAYROLL_ENCASMENT' => array(SS_PAYROLL_M_C|2, _("Leave Encashment Entery")),
    'SA_PAYROLL_GRATUITY' => array(SS_PAYROLL_M_C|3, _("Gratuity Entery")),
    'SA_PAYROLL_INCREMENT' => array(SS_PAYROLL_M_C|5, _("Increment Entery")),
    //Operation
    'SA_PAYROLL_ADVANCE_I' => array(SS_PAYROLL_M|1, _("Advance Inquery")),
    'SA_PAYROLL_ENCASMENT_I' => array(SS_PAYROLL_M|2, _("Leave Encashment Inquery")),
    'SA_PAYROLL_GRATUITY_I' => array(SS_PAYROLL_M|3, _("Gratuity Inquery")),
    'SA_PAYROLL_PAYROLL_I' => array(SS_PAYROLL_M|4, _("Payroll Inquery")),
    'SA_PAYROLL_INCREMENT_I' => array(SS_PAYROLL_M|5, _("Payroll Report")),
//REPORt
    'SA_PAYROLL_REPORT' => array(SS_PAYROLL_M_A|1, _("Increment Inquery")),
//Setup
    'SA_PAYROLL_TAX_SETUP' => array(SS_PAYROLL_M_A|1, _("Tax Slab")),
    'SA_PAYROLL_GL_SETUP' => array(SS_PAYROLL_M_A|2, _("Gl Setup")),


    'SS_CRM_TASK' => array(SS_CRM_A|1, _("Task,Event,Call")),
    'SS_CRM_CALL' => array(SS_CRM_A|2, _("Call Log")),
    'SS_CRM_BASE' => array(SS_CRM_A|3, _("Knowledge Base Attachments")),
	'SS_CRM_QUERY' => array(SS_CRM_A|4, _("Query")),


    'SS_CRM_TASK_I' => array(SS_CRM_B|1, _("Task Inquiry")),
    'SS_CRM_TASK_G' => array(SS_CRM_B|2, _("Task Grid")),
    'SS_CRM_BASE_I' => array(SS_CRM_B|3, _("Knowledge Base Inquiry")),
	'SS_CRM_QUERY_I' => array(SS_CRM_B|4, _("Query Inquiry")),
    'SS_CRM_QUERY_C' => array(SS_CRM_B|5, _("Calendar")),

//    'SS_CRM_C_C' => array(SS_CRM_C|1, _("Add Customer")),
    'SS_CRM_C_K_CAT' => array(SS_CRM_C|2, _("Knowledge Base Category")),
    'SS_CRM_C_TASK' => array(SS_CRM_C|3, _("Task Status")),
	'SS_CRM_C_DURATION' => array(SS_CRM_C|4, _("Duration")),
    'SS_CRM_C_TYPE' => array(SS_CRM_C|5, _("Call Type")),
    'SS_CRM_C_STATUS' => array(SS_CRM_C|6, _("Query Status")),
    'SS_CRM_C_QUERY' => array(SS_CRM_C|7, _("Query Source")),
    'SS_CRM_C_S' => array(SS_CRM_C|8, _("Settings")),


);

if (!@$SysPrefs->allow_gl_reopen)
	unset($security_areas['SA_GLREOPEN']);
/*
	This function should be called whenever we want to extend core access level system
	with new security areas and/or sections i.e.: 
	. on any page with non-standard security areas
	. in security roles editor
	The call should be placed between session.inc inclusion and page() call.
	Up to 155 security sections and 155 security areas for any extension can be installed.
*/
function add_access_extensions()
{
	global $security_areas, $security_sections, $installed_extensions;

	foreach($installed_extensions as $extid => $ext) {
		$accext = hook_invoke($ext['package'], 'install_access', $dummy);
		if ($accext == null) continue;

		$scode = 100;
		$acode = 100;
		$extsections = $accext[1];
		$extareas = $accext[0];
		$extcode = $extid<<16;
		
		$trans = array();
		foreach($extsections as $code =>$name) {
			$trans[$code] = $scode<<8;
			// reassign section codes
			$security_sections[$trans[$code]|$extcode] = $name;
			$scode++;
		}
		foreach($extareas as $code => $area) {
			$section = $area[0]&0xff00;
			// extension modules:
			// if area belongs to nonstandard section
			// use translated section codes and
			// preserve lower part of area code
			if (isset($trans[$section])) {
				$section = $trans[$section];
			} 
				// otherwise assign next available
				// area code >99
			$area[0] = $extcode | $section | ($acode++);
			$security_areas[$code] = $area;
		}
	}
}

function check_edit_access($name)
{
	global $input_security;

	$access = @$input_security[$name];

	if (!$access)
		$access = @$input_security['']; // default access level

	if (!$access)
		return true; // if constraint is not defined edit access is allowed

	return  user_check_access($access);
}
/*
	Returns POST value or null if edit access to $name control is forbidden.
*/
function access_post($name, $dflt=null)
{
	if (!check_edit_access($name))
		return $dflt;
	else
		return get_post($name, $dflt);
}

/*
	Returns numeric input value or null if edit access to $name control is forbidden.
*/
function access_num($name, $dflt=null)
{
	if (!check_edit_access($name))
		return $dflt;
	else
		return input_num($name, $dflt);
}
