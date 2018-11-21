<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL,
	as published by the Free Software Foundation, either version 3
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = 'SA_GLANALYTIC';
$path_to_root="../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
function count_account($role_id){

    $sql="SELECT COUNT(account_code) FROM 0_useraccounts WHERE account_code = ".db_escape($role_id)."";
    $result = db_query($sql, "could not process Requisition to Purchase Order");
    $row = db_fetch_row($result);

    global $path_to_root;

    label_cell(
        "<a target='_blank' "
        ."href='$path_to_root/admin/user_accounts.php?account_code=".$role_id."'"
        ." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >"
        . round2($row[0])
        ."</a>");

}


$js = "";
if (user_use_date_picker())
	$js = get_js_date_picker();
page(_($help_context = "Gl Account Inquiry"), false, false, "", $js);
$k = 0;
$pdeb = $pcre = $cdeb = $ccre = $tdeb = $tcre = $pbal = $cbal = $tbal = 0;
//----------------------------------------------------------------------------------------------------
// Ajax updates
//

if (get_post('Show'))
{
	$Ajax->activate('balance_tbl');
}
function edit_link($account)
{
    if (@$_GET['popup'])
        return '';
    global $trans_type;
    $modify = ($trans_type == ST_SALESORDER ? "ModifyOrderNumber" : "ModifyQuotationNumber");
    return pager_link( _("Edit"),
        "/gl/manage/gl_accounts.php?account_code=" . $account['account_code'], ICON_EDIT);
}
function gl_inquiry_controls()
{

 echo '<br>';
    start_form();
    start_table(TABLESTYLE_NOBORDER);


    gl_all_accounts_list_cells(_("Account:"), 'account_code', null, false, false, _("All Accounts"));
    gl_account_types_list_cells(_("GL Account Group:"), 'id', null, _("All Accounts"), true,false);
    gl_class_types_list_cells(_("Class Type:"), 'cid', null, _("All Accounts"), true,false);

////    submit_cells('Show', _("Search"),'',_('Select documents'), 'default');
//
////    $date = today();
////    if (!isset($_POST['TransToDate']))
////        $_POST['TransToDate'] = end_month($date);
////    if (!isset($_POST['TransFromDate']))
////        $_POST['TransFromDate'] = add_days(end_month($date), -user_transaction_days());
////    start_row();
////    date_cells(_("From:"), 'TransFromDate');
////    date_cells(_("To:"), 'TransToDate');
////    if ($dim >= 1)
////        dimensions_list_cells(_("Dimension")." 1:", 'Dimension', null, true, " ", false, 1);
////    if ($dim > 1)
////        dimensions_list_cells(_("Dimension")." 2:", 'Dimension2', null, true, " ", false, 2);
////    check_cells(_("No zero values"), 'NoZero', null);
////    check_cells(_("Only balances"), 'Balance', null);
////    check_cells(_("4 Columns Trial Balance"), 'trial_bal2', null);
    submit_cells('Show',_("Show"),'','', 'default');
//    end_row();

    end_table();

    echo '&nbsp;<center> <a href="/gl/manage/gl_accounts.php" target="_blank"><input type="button" value="+ADD GL ACCOUNTS"></a>
&nbsp;<a href="/gl/manage/gl_account_types.php" target="_blank"><input type="button" value="+ADD GL GROUPS"></a>
&nbsp;<a href="gl_account_classes.php" target="_blank"><input type="button" value="+ADD GL CLASSES"></a>
&nbsp;&nbsp;
<!--<a href="/modules/import_items/import_gl.php?action=import" target="_blank"><input type="button" value="IMPORT"></a>&nbsp;&nbsp;-->
<a href="/modules/import_items/import_gl.php?action=export" target="_blank"><input type="button" value="EXPORT"></a>
<a href="https://erp30.com/reporting/prn_redirect.php?Class=6&REP_ID=701" class="btn btn-info fa fa-print" role="button" target="_blank"> Print</a>
<center/>';
    end_form();
}
//----------------------------------------------------------------------------------------------------

function display_trial_balance($type, $typename, $account_code, $id, $cid)
{
	global $path_to_root, $SysPrefs,
		 $k, $pdeb, $pcre, $cdeb, $ccre, $tdeb, $tcre, $pbal, $cbal, $tbal;
	$printtitle = 0; //Flag for printing type name
	$k = 0;
	//Get Accounts directly under this group/type

        $accounts = get_gl_accounts_inq(null, null, $type, false, $account_code);
	$begin = get_fiscalyear_begin_for_date($_POST['TransFromDate']);
	if (date1_greater_date2($begin, $_POST['TransFromDate']))
		$begin = $_POST['TransFromDate'];
	$begin = add_days($begin, -1);
	$Apdeb=$pdeb;
	$Apcre=$pcre;
	$Acdeb=$cdeb;
	$Accre=$ccre;
	$Atdeb=$tdeb;
	$Atcre=$tcre;
	$Apbal=$pbal;
	$Acbal=$cbal;
	$Atbal=$tbal;

	while ($account = db_fetch($accounts))
	{
		//Print Type Title if it has atleast one non-zero account
		if (!$printtitle)
		{
			start_row("class='inquirybg' style='font-weight:bold'");
			label_cell(_("Group")." - ".$type ." - ".$typename, "colspan=8");
			end_row();
			$printtitle = 1;

		}

		// FA doesn't really clear the closed year, therefore the brought forward balance includes all the transactions from the past, even though the balance is null.
		// If we want to remove the balanced part for the past years, this option removes the common part from from the prev and tot figures.

		alt_table_row_color($k);
		$url = "<a href='$path_to_root/gl/manage/gl_accounts.php?selected_account=" . $account["account_code"] .  "'><i class='fa fa-pencil' ></i></a>";

		label_cell($url);
        label_cell($account["account_code"]);
		label_cell($account["account_name"]);
		///For Users
        global $SysPrefs;
        if ($SysPrefs->enable_user_gl_restrict() == 1) {
            echo count_account($account[ "account_code" ]);
        }

		end_row();
	}

	//Get Account groups/types under this group/type
    $group_id=get_account_class_id($type );

    if($group_id!='')
	$result = get_account_types_inq(false, false, $type, $group_id);

    else
        $result = get_account_types_inq(false, false, $type, $id);


	while ($accounttype=db_fetch($result))
	{
		//Print Type Title if has sub types and not previously printed
		if (!$printtitle)
		{
			start_row("class='inquirybg' style='font-weight:bold'");
			label_cell(_("Group")." - ".$type ." - ".$typename, "colspan=8");
			end_row();
			$printtitle = 1;


		}

//		display_trial_balance($accounttype["id"], $accounttype["name"].' ('.$typename.')', $account_code, $id, $cid);
	}





	end_row();


}



//----------------------------------------------------------------------------------------------------
gl_inquiry_controls();

div_start('balance_tbl');


start_table(TABLESTYLE);

if($_POST['trial_bal2']==0) {
    global $SysPrefs;
    if ($SysPrefs->enable_user_gl_restrict() == 1) {
        $tableheader = "<tr>
	<td rowspan=2 class='tableheader'>" . _("Account") . "</td>

	<td rowspan=2 class='tableheader'>" . _("Account Name") . "</td>
	
	<td rowspan=2 class='tableheader'>" . _("Users") . "</td>
		<td rowspan=2 class='tableheader'>" . _(" ") . "</td>

		
	</tr><tr>
	</tr>";
    }
    else{
        $tableheader = "<tr>
	<td rowspan=2 class='tableheader'>" . _("Account") . "</td>
	<td rowspan=2 class='tableheader'>" . _("Account Name") . "</td>
	<td rowspan=2 class='tableheader'>" . _(" ") . "</td>
		
	</tr><tr>
	</tr>";

    }
}

else
{
	$tableheader = "<tr>
	<td rowspan=2 class='tableheader'>" . _("Account") . "</td>
	<td rowspan=2 class='tableheader'>" . _("Account Name") . "</td>
		<td rowspan=2 class='tableheader'>" . _("Users") . "</td>
				<td rowspan=2 class='tableheader'>" . _(" ") . "</td>
	</tr>";
}
	echo $tableheader;

//display_trial_balance();
$class_id=get_account_class_id($_POST['id']);
if($class_id!='')
$classresult = get_account_classes_inq(false, -1, $class_id);
else
    $classresult = get_account_classes_inq(false, -1, $_POST['cid']);


while ($class = db_fetch($classresult))
{

	start_row("class='inquirybg' style='font-weight:bold'");
	label_cell(_("Class")." - ".$class['cid'] ." - ".$class['class_name'], "colspan=8");

	end_row();
	//Get Account groups/types under this group/type with no parents
    $group_id=get_account_class_id($_POST['cid']);
    if($group_id!='')
        $typeresult = get_account_types_inq(false, $class['cid'], false, $group_id);
        else
	$typeresult = get_account_types_inq(false, $class['cid'], false, $_POST['id']);

	while ($accounttype=db_fetch($typeresult))
	{

		display_trial_balance($accounttype["id"], $accounttype["name"], $_POST['account_code'], $_POST['id'], $_POST['cid']);

	}
}




end_row();
end_table(1);
div_end();

//----------------------------------------------------------------------------------------------------
end_page();
