

<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 

if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page(_($help_context = "Over Time Per Day Inquiry"), isset($_GET['employee_id']), false, "", $js);
}

if (isset($_GET['employee_id'])){
	$_POST['employee_id'] = $_GET['employee_id'];
}
if (isset($_GET['FromDate'])){
	$_POST['TransAfterDate'] = $_GET['FromDate'];
}
if (isset($_GET['ToDate'])){
	$_POST['TransToDate'] = $_GET['ToDate'];
}
if (isset($_GET['id'])){
	$_POST['id'] = $_GET['id'];
}
if (isset($_GET['emp_dept'])){
	$_POST['emp_dept'] = $_GET['emp_dept'];
}

?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Shadow and Glow Transitions</title>

        <style>

            [class^="hvr-"] {
                background:#3c8dbc;
                color: #FFFFFF;
                cursor: pointer;
                margin: 0;
                padding:10px;
                text-decoration: none;

            }


            /* SHADOW/GLOW TRANSITIONS */
            /* Glow */
            .hvr-glow {

                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-glow:hover, .hvr-glow:focus, .hvr-glow:active {
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.6);
            }

            /* Shadow */
            .hvr-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-shadow:hover, .hvr-shadow:focus, .hvr-shadow:active {
                box-shadow: 0 10px 10px -10px rgba(0, 0, 0, 0.5);
            }

            /* Grow Shadow */
            .hvr-grow-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow, transform;
                transition-property: box-shadow, transform;
            }
            .hvr-grow-shadow:hover, .hvr-grow-shadow:focus, .hvr-grow-shadow:active {
                box-shadow: 0 10px 10px -10px rgba(0, 0, 0, 0.5);
                -webkit-transform: scale(1.1);
                transform: scale(1.1);
            }

            /* Box Shadow Outset */
            .hvr-box-shadow-outset {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-box-shadow-outset:hover, .hvr-box-shadow-outset:focus, .hvr-box-shadow-outset:active {    color: #000203;
                box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.6);
            }

            /* Box Shadow Inset */
            .hvr-box-shadow-inset {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
                box-shadow: inset 0 0 0 rgba(0, 0, 0, 0.6), 0 0 1px rgba(0, 0, 0, 0);
                /* Hack to improve aliasing on mobile/tablet devices */
            }
            .hvr-box-shadow-inset:hover, .hvr-box-shadow-inset:focus, .hvr-box-shadow-inset:active {    color: #000203;
                box-shadow: inset 2px 2px 2px rgba(0, 0, 0, 0.6), 0 0 1px rgba(0, 0, 0, 0);
                /* Hack to improve aliasing on mobile/tablet devices */
            }


            /* Float Shadow */
            .hvr-float-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                position: relative;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: transform;
                transition-property: transform;
            }
            .hvr-float-shadow:before {
                pointer-events: none;
                position: absolute;
                z-index: -1;
                content: '';
                top: 100%;
                left: 5%;
                height: 10px;
                width: 90%;
                opacity: 0;
                background: -webkit-radial-gradient(center, ellipse, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0) 80%);
                /* W3C */
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: transform, opacity;
                transition-property: transform, opacity;
            }

            .hvr-float-shadow:hover, .hvr-float-shadow:focus, .hvr-float-shadow:active {   background:#006699;   color: #000203;
                -webkit-transform: translateY(-5px);
                transform: translateY(-5px);
                /* move the element up by 5px */
            }
            .hvr-float-shadow:hover:before, .hvr-float-shadow:focus:before, .hvr-float-shadow:active:before {
                opacity: 1;
                -webkit-transform: translateY(5px);
                transform: translateY(5px);
                /* move the element down by 5px (it will stay in place because it's attached to the element that also moves up 5px) */
            }

            /* Shadow Radial */
            .hvr-shadow-radial {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                position: relative;
            }
            .hvr-shadow-radial:before, .hvr-shadow-radial:after {
                pointer-events: none;
                position: absolute;
                content: '';
                left: 0;
                width: 100%;
                box-sizing: border-box;
                background-repeat: no-repeat;
                height: 5px;
                opacity: 0;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: opacity;
                transition-property: opacity;
            }
            .hvr-shadow-radial:before {
                bottom: 100%;
                background: -webkit-radial-gradient(50% 150%, ellipse, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at 50% 150%, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
            }
            .hvr-shadow-radial:after {
                top: 100%;
                background: -webkit-radial-gradient(50% -50%, ellipse, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at 50% -50%, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
            }
            .hvr-shadow-radial:hover:before, .hvr-shadow-radial:focus:before, .hvr-shadow-radial:active:before, .hvr-shadow-radial:hover:after, .hvr-shadow-radial:focus:after, .hvr-shadow-radial:active:after {
                opacity: 1;
            }

        </style>
    </head>

    <body>



<center>
        <td><a class="hvr-float-shadow" href="http://erp30.com/portal.php"><i class="fa fa-dashboard " style="margin-right: 5px; font-size: large;">  </i> Fetch Data/Change Admin Password</a></td>
</center>



    </body>
    </html>

<?php
//------------------------------------------------------------------------------------------------
if (!@$_GET['popup'])
	start_form();



start_table(TABLESTYLE_NOBORDER);
start_row();
search_portal_list_cells(_("Search"), 'searching', null, "", "", '','',true);

ref_cells(_("Company Code/Name #:"), 'code', '',null, '', true);
ref_cells(_("User"), 'user', '',null, '', true);
check_cells(_("Show All:"), 'show_all');

submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');
submit_center_last('delete', _("Delete"), '', '', true);
end_row();
end_table();
set_global_supplier($_POST['employee_id']);


div_start('totals_tbl');

div_end();

if(get_post('RefreshInquiry'))
{
	$Ajax->activate('totals_tbl');
}

//------------------------------------------------------------------------------------------------
if (isset($_POST['delete'])) 
{		
	$sql="DELETE FROM ".TB_PREF."company_users ";
	db_query($sql,"The query could not be deleted");
}
function edit_company_link($row) 
{
	if (@$_GET['popup'])
		//return '';
		
	$modify = 'emp_dept';
  return pager_link( _("Edit"),
    "/payroll/inquiry/portal_detail_inquiry.php?name=".$row['name'].'&comp=1', ICON_EDIT);
}
function edit_link($row) 
{
	if (@$_GET['popup'])
		//return '';
		
	$modify = 'emp_dept';
  return pager_link( _("Edit"),
    "/admin/portal_user.php?comp_db=".$row['dbname'].'&comp=1', ICON_EDIT);
}
function edit_link_payroll($row) 
{
	if (@$_GET['popup'])
		//return '';
		
	$modify = 'emp_dept';
  return pager_link( _("Edit"),
    "/admin/portal_user.php?comp_db=".$row['dbname'].'&comp=2', ICON_EDIT);
}
function edit_link_hr($row) 
{
	if (@$_GET['popup'])
		//return '';
		
	$modify = 'emp_dept';
  return pager_link( _("Edit"),
    "/admin/portal_user.php?comp_db=".$row['dbname'].'&comp=3', ICON_EDIT);
}
function edit_link_manufacturing($row) 
{
	if (@$_GET['popup'])
		//return '';
		
	$modify = 'emp_dept';
  return pager_link( _("Edit"),
    "/admin/portal_user.php?emp_dept=".$row['dbname'].'&date='.$row['date'], ICON_EDIT);
}

//------------------------------------------------------------------------------------------------
function get_sql_for_employee_inquiry($comp_code, $user)
{
    $comp_code=$_POST['code'];
$user=$_POST['user'];
$searching=$_POST['searching'];
$show_all=$_POST['show_all'];

// 	$sql = "SELECT name,c_name,user_id,last_visit_date,c_reports,IF( `c_status` =0, 'Active', 'Suspend' ) AS c_status,dbname FROM ( SELECT name,c_name,user_id,last_visit_date,c_reports,IF( `c_status` =0, 'Active', 'Suspend' ) AS c_status,dbname FROM 0_company_users WHERE id!=0 GROUP BY c_name ) AS TEMP  ";
$sql =" SELECT name,c_name,user_id,last_visit_date,c_reports,IF( `c_status` =0, 'Active', 'Suspend' ) AS c_status,dbname FROM `0_company_users` WHERE id!=0 ";
	if ($searching==1 && $comp_code!='') {
		$sql .= " AND ".TB_PREF."company_users.name  LIKE " . db_escape("%" . $comp_code . "%");
	}

if ($searching==2 && $comp_code!='') {
		$sql .= " AND ".TB_PREF."company_users.c_name  LIKE " . db_escape("%" . $comp_code . "%");
	}

if ($searching==3 && $comp_code!='') {
		$sql .= " AND ".TB_PREF."company_users.c_reports  LIKE " . db_escape("%" . $comp_code . "%");
	}
	
// 	 if ($comp_code != '') {
// 			$sql .= " AND ".TB_PREF."company_users.name  LIKE " . db_escape("%" . $comp_code . "%");
//  		}
 		 if ($user != '') {
			$sql .= " AND ".TB_PREF."company_users.user_id  LIKE " . db_escape("%" . $user . "%");
 		}
// 	if ($comp_code != ALL_TEXT)
// 	{
//   		$sql .= " AND ".TB_PREF."company_users.c_code = ".db_escape($comp_code);
// 	}
//display_error($show_all);
// if($show_all=='' )
// {
//     	$sql .= "  ORDER BY last_visit_date DESC ";
   
// }
// else
// {
// 	$sql .= " ORDER BY last_visit_date ASC ";
// }
$sql .= " GROUP BY `name` ORDER BY `last_visit_date` DESC  ";
   	return $sql;
}


$sql = get_sql_for_employee_inquiry($_POST['comp_code'],$_POST['emp_dept'],$_POST['emp_grade'],$_POST['emp_desig'],date2sql($_POST['datefrom']),date2sql($_POST['dateto']));
 $cols = array(
			_("C Code"),
			_("C Name"),
			_("User"),
			_("Login Date")=> array('name'=>'last_visit_date', 'type'=>'last_visit_date', 'ord'=>'desc'),
			_("Reports") ,
			_(" Status")
			
			
			//_("present"),
    );
	array_append($cols, array(
	    _("Company Detail")=>array('insert'=>true, 'fun'=>'edit_company_link'),
		_("Active/Inactive")=>array('insert'=>true, 'fun'=>'edit_link'),
		_("Payroll")=>array('insert'=>true, 'fun'=>'edit_link_payroll'),
	_("HR")=>	array('insert'=>true, 'fun'=>'edit_link_hr')
		));

if ($_POST['employee_id'] != ALL_TEXT)
{
	$cols[_("Supplier")] = 'skip';
	$cols[_("Currency")] = 'skip';
}


//------------------------------------------------------------------------------------------------

/*show a table of the transactions returned by the sql */
$table =& new_db_pager('trans_tbl', $sql, $cols);
//$table->set_marker('check_overdue', _(""));

$table->width = "85%";


	
display_db_pager($table);

if (!@$_GET['popup'])
{
	end_form();
	end_page(@$_GET['popup'], false, false);
}
?>
