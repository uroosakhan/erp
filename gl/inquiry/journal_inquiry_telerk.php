<?php

$page_security = 'SA_GLANALYTIC';
$path_to_root="../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Journal Inquiry"), false, false, "", $js);

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Search'))
{
	$Ajax->activate('journal_tbl');
}
//--------------------------------------------------------------------------------------
if (!isset($_POST['filterType']))
	$_POST['filterType'] = -1;



?>
<!----------------grid------------------>
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title>GRID PHP</title>
	<link rel="stylesheet" href="http://cdn.kendostatic.com/2012.2.710/styles/kendo.common.min.css" />
	<!--	<link rel="stylesheet" href="http://cdn.kendostatic.com/2012.2.710/styles/kendo.blueopal.min.css" />-->
	<script type="text/javascript" src="http://cdn.kendostatic.com/2012.2.710/js/jquery.min.js"></script>
	<script type="text/javascript" src="http://cdn.kendostatic.com/2012.2.710/js/kendo.all.min.js"></script>
	<link rel="stylesheet" href="http://kendo.cdn.telerik.com/2015.2.805/styles/kendo.common-material.min.css" />
	<link rel="stylesheet" href="http://kendo.cdn.telerik.com/2015.2.805/styles/kendo.material.min.css" />

	<!--	<script src="http://cdn.kendostatic.com/2015.1.429/js/jszip.min.js"></script>-->

	<script type="text/javascript">
        //function test(e){

        //	return '<a class="k-button" href="task_gridassigned.php" id="toolbar-add_user" >Assigned</a>';
        //};



        $(function() {


            $("#grid").kendoGrid({

                dataSource: {
                    transport: {
                        read: "fetch.php",

//                        update: {
//                            url:"data/update.php", type:"POST" ,
//
//
//
//                        },
//                        create: {url:"data/create.php",type:"POST",
//
//                        },
//                        destroy: {url:"data/destroy.php",type:"POST"},



                    },

                    batch: true,


//						pageSize: 20,
//                autoSync: true,
                    schema: {
                        model: {
                            //user_id: "user_id",
                            fields: {


                                tran_date: { type: "date"   },
                                type: { type: "string" },
                                type_no: { type: "string" },
                                reference: { type: "string"} ,
                                amount: { type: "number" },
                                memo_: { type: "string" },
                                user_id: { type: "string" },

                            }
                        }
                    },
                    pageSize: 30,


//
                    group:
                        [
                            {field: "tran_date"},
                            {field: "type"},

                        ]
                },

                //--
//			
                    //--
//				selectable: "multiple",
                    pageable: {
                        refresh: true,
//					pageSizes: true,
                        pageSizes: [30, 50, 100, "all"],
//                        buttonCount: 5,
                    },
                    groupable: true,
                    reorderable: true,
                    editable:  "inline",//				editable:  "incell",
				resizable: true,
					scrollable: false,
//                    height: 600,
                    filterable: true,
                    sortable: true,
//				pageable: true,
                    columnMenu: true,


//                toolbar: ["create",{template: '<a class="k-button" href="task_gridassigned.php"  >Assigned<span class="badge">7</span></a>'}
//
//                    ,{template: '<a class="k-button" href="task_griddone.php" >Done</a>'}
//                    ,{template: '<a class="k-button" href="task_gridonhold.php" >On Hold</a>'}
//                    ,{template: '<a class="k-button" href="task_gridall.php" >All</a>'}
//                    ,{template: '<a class="k-button" href="task_gridinprocess.php" >ToDay</a>'}
//
//
//
//                ],

                columns: [

                  // edit button comments  {command: [{text:" ", name:"edit"}, {text:" ",name:"destroy"}], title: " ", width: "100px" },


                    {
                        template:'<a  href="../view/gl_trans_view.php?type_id=#=type#&trans_no=#=type_no#" >#=type_no#</a>',
                        field: "type_no",
                        title: "#",					width: "50px",

                    },
                    {editor: dateTimeEditor,
                        field: "tran_date",					width: "80px",
                        format:"{0:dd-MM-yyyy}",
                        title: "Date",      // editor: debtornoDropDownEditor,
                       // template: "#=tran_date#",
                    },
//
                    {
                        field: "name",
                        width: "50px",
                        title: "Name",
                       // editor: categoryDropDownEditor,
                        //template: "#=systype_name#",

                    },
//
                    {
                        field: "type_no",					width: "50px",
                        title: "Trans #",
                        //editor: assignbyDropDownEditor,
                       // template: "#=assign_by#",
                    },

                    {
                        field: "reference",
                        title: "Referenc",					width: "50px",

                    },
                    {
                        field: "amount",
                        title: "Amount",					width: "50px",

                    },


                    {
                        field: "memo_",
                        title: "Memo",
                       // editor: statusDropDownEditor,

                      //  template: "#=status#",
                        width: "50px",

                    },

                    {
                        field: "user_id",
                        title: "User",
                        // editor: progressDropDownEditor,
                        // template: "#=progress#",
                        width: "100px",

                    },
                ]

            });



        });

        function dateTimeEditor(container, options) {
            console.log("options", options);

            $('<input  data-text-field="' + options.field + '" data-value-field="' + options.field + '" data-bind="value:' + options.field + '" data-format="' + options.format + '"/>')
                .appendTo(container)
                .kendoDatePicker({});

        }
	</script>
</head>
<body>

<div id="example">

	<script>
        $(document).ready(function () {
            $("#primaryTextButton").kendoButton();


        });
	</script>

	<style>

		#primaryTextButton  {
			line-height: 40px;

		}
		#primaryTextButton .k-button {
			margin: 0px;


		}
		.k-primary {
			min-width: 150px;
			margin-right: -10px;
			/*border-radius: 2px 2px 2px 2px;*/
		}
		.k-primary a{text-decoration: none; color: white;}
		.k-primary	a:hover{text-decoration: none; color: white;}
	</style>
<!--	<button id="primaryTextButton" class="k-primary"><a href="../task.php?type=task">ADD TASK</a> </button>-->
<!---->
<!--	<button id="primaryTextButton" class="k-primary"><a href="../query.php?type=task">ADD QUERY</a> </button>-->
<!---->
<!--	<button id="primaryTextButton" class="k-primary"><a href="../task.php?type=call">ADD CALL</a> </button>-->
<!---->
<!--	<button id="primaryTextButton" class="k-primary"><a href="call_log.php">CALL LOG</a> </button>-->
<!--	<button id="primaryTextButton" class="k-primary"><a href="demo.php?type=task">ADD KB</a> </button>-->
<!---->
<!--	<button id="primaryTextButton" class="k-primary"><a href="calender.php?"> CALENDAR</a></button>-->
<!---->
<!--	<button id="primaryTextButton" class="k-primary"><a href="../task.php?type=task">BACK</a> </button>-->

	<style>

		#grid td{
			white-space: normal;
			padding: 10;
		}
	</style>


	<div id="grid" ></div>

</div>

</body>
</html>
<?php
//start_form(); comment by ansar for grid
//
//start_table(TABLESTYLE_NOBORDER);
//start_row();
//
//ref_cells(_("Reference:"), 'Ref', '',null, _('Enter reference fragment or leave empty'));
//
//journal_types_list_cells(_("Type:"), "filterType");
//date_cells(_("From:"), 'FromDate', '', null, 0, -1, 0);
//date_cells(_("To:"), 'ToDate');
//
//check_cells( _("Show closed:"), 'AlsoClosed', null);
//
//submit_cells('Search', _("Search"), '', '', 'default');
//end_row();
//start_row();
//ref_cells(_("Memo:"), 'Memo', '',null, _('Enter memo fragment or leave empty'));
//end_row();
//end_table();
//
//function journal_pos($row)
//{
//	return $row['gl_seq'] ? $row['gl_seq'] : '-';
//}
//
//function systype_name($dummy, $type)
//{
//	global $systypes_array;
//
//	return $systypes_array[$type];
//}
//
//function view_link($row)
//{
//	return get_trans_view_str($row["type"], $row["type_no"]);
//}
//
//function gl_link($row)
//{
//	return get_gl_view_str($row["type"], $row["type_no"]);
//}
//
//$editors = array(
//	ST_JOURNAL => "/gl/gl_journal.php?ModifyGL=Yes&trans_no=%d&trans_type=%d",
//	ST_BANKPAYMENT => "/gl/gl_bank.php?ModifyPayment=Yes&trans_no=%d&trans_type=%d",
//	ST_BANKDEPOSIT => "/gl/gl_bank.php?ModifyDeposit=Yes&trans_no=%d&trans_type=%d",
//	ST_CPV => "/gl/gl_bankCV.php?ModifyPayment=Yes&trans_no=%d&trans_type=%d",
//	ST_CRV => "/gl/gl_bankCV.php?ModifyDeposit=Yes&trans_no=%d&trans_type=%d",
////	4=> Funds Transfer,
//   ST_SALESINVOICE => "/sales/customer_invoice.php?ModifyInvoice=%d",
////   11=>
//// free hand (debtors_trans.order_==0)
////	"/sales/credit_note_entry.php?ModifyCredit=%d"
//// credit invoice
////	"/sales/customer_credit_invoice.php?ModifyCredit=%d"
////	 12=> Customer Payment,
//   ST_CUSTDELIVERY => "/sales/customer_delivery.php?ModifyDelivery=%d",
////   16=> Location Transfer,
////   17=> Inventory Adjustment,
////   20=> Supplier Invoice,
////   21=> Supplier Credit Note,
////   22=> Supplier Payment,
////   25=> Purchase Order Delivery,
////   28=> Work Order Issue,
////   29=> Work Order Production",
////   35=> Cost Update,
//);
//
//function edit_link($row)
//{
//	global $editors;
//
//	$ok = true;
//	if ($row['type'] == ST_SALESINVOICE)
//	{
//		$myrow = get_customer_trans($row["type_no"], $row["type"]);
//		if ($myrow['alloc'] != 0 || get_voided_entry(ST_SALESINVOICE, $row["type_no"]) !== false)
//			$ok = false;
//	}
//	return isset($editors[$row["type"]]) && !is_closed_trans($row["type"], $row["type_no"]) && $ok ?
//		pager_link(_("Edit"),
//			sprintf($editors[$row["type"]], $row["type_no"], $row["type"]),
//			ICON_EDIT) : '';
//}
//
//$sql = get_sql_for_journal_inquiry(get_post('filterType', -1), get_post('FromDate'),
//	get_post('ToDate'), get_post('Ref'), get_post('Memo'), check_value('AlsoClosed'));
//
//$cols = array(
//	_("#") => array('fun'=>'journal_pos', 'align'=>'center'),
//	_("Date") =>array('name'=>'tran_date','type'=>'date','ord'=>'desc'),
//	_("Type") => array('fun'=>'systype_name'),
//	_("Trans #") => array('fun'=>'view_link'),
//	_("Reference"),
//	_("Amount") => array('type'=>'amount'),
//	_("Memo"),
//	_("Cheque No"),
//	_("User"),
//	_("View") => array('insert'=>true, 'fun'=>'gl_link'),
//	array('insert'=>true, 'fun'=>'edit_link')
//);
//
//if (!check_value('AlsoClosed')) {
//	$cols[_("#")] = 'skip';
//}
//
//$table =& new_db_pager('journal_tbl', $sql, $cols);
//
//$table->width = "80%";
//
//display_db_pager($table);

//end_form(); commment by ansar for grid
end_page();

?>
