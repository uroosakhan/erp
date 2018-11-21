<?php
/**********************************************
Author: Joe Hunt
Author: Tom Moulton - added Export of many types and import of the same
Name: Import of CSV formatted items
Free software under GNU GPL
***********************************************/
$page_security = 'SA_CSVIMPORT';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_codes_db.inc");
include_once($path_to_root . "/dimensions/includes/dimensions_db.inc");

function get_add_workcenter($name) {
    $name = db_escape($name);
    $sql = "SELECT id FROM ".TB_PREF."workcentres WHERE UPPER( name ) = UPPER( $name )";
    $result = db_query($sql, "Can not search workcentres table");
    $row = db_fetch_row($result);
    if (!$row[0]) {
	$sql = "INSERT INTO ".TB_PREF."workcentres (name, description) VALUES ( $name, $name)";
	$result = db_query($sql, "Could not add workcenter");
	$id = db_insert_id();
        display_notification("Added $name as id $id");
    } else $id = $row[0];
    return $id;
}
function add_account_type_new($id, $name, $class_id, $parent)
{
    $sql = "INSERT INTO ".TB_PREF."chart_types (id, name, class_id, parent)
		VALUES (".db_escape($id).", ".db_escape($name).", ".db_escape($class_id).", ".db_escape($parent).")";

    return db_query($sql);
}

function add_gl_account_new($account_code, $account_name, $account_type, $account_code2)
{
    $sql = "INSERT INTO ".TB_PREF."chart_master (account_code, account_code2, account_name, account_type)
		VALUES (".db_escape($account_code).", ".db_escape($account_code2).", "
        .db_escape($account_name).", ".db_escape($account_type).")";

    return db_query($sql);
}

function download_file($filename, $saveasname='')
{
    if (empty($filename) || !file_exists($filename))
    {
        return false;
    }
    if ($saveasname == '') $saveasname = basename($filename);
    header('Content-type: application/vnd.ms-excel');
    header('Content-Length: '.filesize($filename));
    header('Content-Disposition: attachment; filename="'.$saveasname.'"');
    readfile($filename);

    return true;
}

// change this from file to mysql $result
function download_csv($filename, $saveasname='')
{
    if (empty($filename) || !file_exists($filename))
    {
        return false;
    }
    if ($saveasname == '') $saveasname = basename($filename);
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="'.$saveasname.'"');
// print all results, converting data as needed
    return true;
}

$action = 'import';
if (isset($_GET['action'])) $action = $_GET['action'];
if (isset($_POST['action'])) $action = $_POST['action'];

if (isset($_POST['export'])) {
    $etype = 0;
    if (isset($_POST['export_type'])) $etype = $_POST['export_type'];
    $sales_type_id = 0;
    if (isset($_POST['sales_type_id'])) $sales_type_id = $_POST['sales_type_id'];
    $currency = "USD";
    if (isset($_POST['currency'])) $currency = $_POST['currency'];

	if ($etype == 9) {
		$fname = "gl_account.csv";

		$sql = "SELECT  'GL_ACCOUNT' AS 
            type , ct.id, ct.name, cc.class_name, ct.parent, cm.account_code, cm.account_name 
            
FROM 0_chart_types AS ct
LEFT JOIN 0_chart_master cm ON ct.`id` = cm.`account_type` 
LEFT JOIN 0_chart_class cc ON cc.`cid` = ct.`class_id`";
		}
	//------------------------------------
    $result = db_query($sql, "Could not select csv data");
    if (db_num_rows($result) > 0) {
        // header('Content-type: application/vnd.ms-excel');
        header('Content-type: text/x-csv');
        header('Content-Disposition: attachment; filename='.$fname);
        $i = 0;
        while ($csv = db_fetch_assoc($result)) {
            $hdr = '';
            $str = '';
            while (list($k, $d) = each($csv)) {
                if ($i == 0) $hdr .= $k . ",";
                $str .= htmlspecialchars_decode($d) . ",";
            }
            if ($i == 0) echo $hdr . "\n";
            echo $str."\n";
            $i++;
        }
        exit;
    } else display_notification("No Results to download.");
}

page("Import of CSV formatted Items");
if (isset($_POST['import'])) {
	if (isset($_FILES['imp']) && $_FILES['imp']['name'] != '') {
		$filename = $_FILES['imp']['tmp_name'];
		$sep = $_POST['sep'];

		$fp = @fopen($filename, "r");
		if (!$fp)
			die("can not open file $filename");
		$lines = $i = $j = $k = $b = $u = $p = $pr = $dm_n = $gl = 0;

		while ($data = fgetcsv($fp, 4096, $sep)) {

			if ($lines++ == 0) continue;

                list($type, $id, $name, $class_name, $parent, $account_code, $account_name) = $data;

                if ($type == 'GL_ACCOUNT') {
//-----------------------------------------------------------------------------------------------------//
                    $sql = "SELECT cid , class_name FROM " . TB_PREF . "chart_class 
				WHERE class_name='$class_name'";
                    $result = db_query($sql, "could not get customer items");
                    $row = db_fetch_row($result);
                    if (!$row) {
                        add_account_class($id, $class_name, $ctype);
                        $class_id = db_insert_id();
                    } else $class_id = $row[0];

//------------------------------------------------------------------------------------------------------//
                    $sql = "SELECT id, name FROM " . TB_PREF . "chart_types 
				WHERE id='$id'";
                    $result = db_query($sql, "could not get customer items");
                    $row = db_fetch_row($result);
                    if (!$row) {
                        add_account_type_new($id, $name, $class_id, $parent);
                        $accounts_id = db_insert_id();
                    } else $accounts_id = $row[0];

//------------------------------------------------------------------------------------------------------//
                    $sql = "SELECT account_code, account_name FROM " . TB_PREF . "chart_master
				 WHERE account_name='$account_name'";
                    $result = db_query($sql, "could not get supplier items");
                    $row = db_fetch_row($result);
                    if (!$row) {
                        add_gl_account_new($account_code, $account_name, $id, $account_code2);
                    }
//-----------------------------------------------------------------------------------------------------//
             else
				$gl_acc = $row[0];
                $gl++;
			}

        }


		@fclose($fp);
		if ($gl > 0) display_notification("$gl GL_Account Data added or updated");

	} else display_error("No CSV file selected");
}

if ($action == 'import') echo 'Import';
else hyperlink_params($_SERVER['PHP_SELF'], _("Import"), "action=import", false);
echo '&nbsp;|&nbsp;';
if ($action == 'export') echo 'Export';
else hyperlink_params($_SERVER['PHP_SELF'], _("Export"), "action=export", false);
echo "<br><br>";

if ($action == 'import') {
    start_form(true);

    start_table(TABLESTYLE2, "width=40%");

    table_section_title("Default GL Accounts");

//    $company_record = get_company_prefs();
//
//    if (!isset($_POST['inventory_account']) || $_POST['inventory_account'] == "")
//   	$_POST['inventory_account'] = $company_record["default_inventory_act"];
//
//    if (!isset($_POST['cogs_account']) || $_POST['cogs_account'] == "")
//   	$_POST['cogs_account'] = $company_record["default_cogs_act"];
//
//    if (!isset($_POST['sales_account']) || $_POST['sales_account'] == "")
//	$_POST['sales_account'] = $company_record["default_inv_sales_act"];
//
//    if (!isset($_POST['adjustment_account']) || $_POST['adjustment_account'] == "")
//	$_POST['adjustment_account'] = $company_record["default_adj_act"];
//
//    if (!isset($_POST['wip_account']) || $_POST['wip_account'] == "")
//	$_POST['wip_account'] = $company_record["default_wip_act"];
    if (!isset($_POST['sep']))
	$_POST['sep'] = ",";

	gl_all_accounts_list_row(_("Sales Account:"), 'sales_account', $_POST['sales_account'], false, false, true);
	gl_all_accounts_list_row(_("Sales Discount Account:"), 'sales_discount_account', $_POST['sales_discount_account']);
	gl_all_accounts_list_row(_("Accounts Receivable Account:"), 'receivables_account', $_POST['receivables_account'], true);
	gl_all_accounts_list_row(_("Prompt Payment Discount Account:"), 'payment_discount_account', $_POST['payment_discount_account']);

    table_section_title("Separator, Location, Tax and Sales Type");
	text_row("Field separator:", 'sep', $_POST['sep'], 2, 1);
    
    label_row("CSV Import File:", "<input type='file' id='imp' name='imp'>");

    end_table(1);

    submit_center('import', "Import CSV File");

    end_form();
}
if ($action == 'export') {
    start_form(true);

    start_table(TABLESTYLE2, "width=40%");

    $company_record = get_company_prefs();
    $currency = $company_record["curr_default"];
    hidden('currency', $currency);

    table_section_title("Export Selection");
?>
<tr>
<td>Export Type:</td>
<td><select  name='export_type' class='combo' title='' >
<option value='9'>GL_Account</option>
</select>
</td>
</tr>
<?php

    end_table(1);

    hidden('action', 'export');
    submit_center('export', "Export CSV File");

    end_form();
}

    end_page();
