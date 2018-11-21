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

function check_stock_id($stock_id) {
    $sql = "SELECT * FROM ".TB_PREF."stock_master where stock_id = $stock_id";
    $result = db_query($sql, "Can not look up stock_id");
    $row = db_fetch_row($result);
    if (!$row[0]) return 0;
    return 1;
}

function get_supplier_id($supplier) {
    $sql = "SELECT supplier_id FROM ".TB_PREF."suppliers where supp_name = $supplier";
    $result = db_query($sql, "Can not look up supplier");
    $row = db_fetch_row($result);
    if (!$row[0]) return 0;
    return $row[0];
}

function get_dimension_by_name($name) {
    if ($name = '') return 0;

    $sql = "SELECT * FROM ".TB_PREF."dimensions WHERE name=$name";
    $result = db_query($sql, "Could not find dimension");
    if ($db_num_rows($result) == 0) return -1;
    $row = db_fetch_row($result);
    if (!$row[0]) return -1;
    return $row[0];
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
function add_crm_person_new($ref, $name, $name2, $address, $phone, $phone2, $fax, $email, $lang, $notes,
						$cat_ids=null, $entity=null)
{
	$sql = "INSERT INTO ".TB_PREF."crm_persons (ref, name, name2, address,
		phone, phone2, fax, email, lang, notes)
		VALUES ("
		.db_escape($ref) . ", "
		.db_escape($name) . ", "
		.db_escape($name2) . ", "
		.db_escape($address) . ", "
		.db_escape($phone) . ", "
		.db_escape($phone2) . ", "
		.db_escape($fax) . ", "
		.db_escape($email) . ", "
		.db_escape($lang) . ", "
		.db_escape($notes)
		.")";

	begin_transaction();

	$ret = db_query($sql, "Can't insert crm person");
	$id = db_insert_id();
	if ($ret && $cat_ids) {
		if(!update_person_contacts($id, $cat_ids, $entity))
			return null;
	}
	commit_transaction();
	return $id;
}

function add_crm_contact_new($type, $action, $entity_id, $person_id)
{

	$sql = "INSERT INTO ".TB_PREF."crm_contacts (person_id, type, action, entity_id) VALUES ("
		.db_escape($person_id) . ","
		.db_escape($type) . ","
		.db_escape($action) . ","
		.db_escape($entity_id) . ")";
	return db_query($sql, "Can't insert crm contact");
}
function get_person_crm_max($customer_id)
{
	$sql = "SELECT MAX(id) FROM ".TB_PREF."crm_persons ";
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function add_tax_group_new($name, $taxes, $tax_shippings)
{
    begin_transaction();

    $sql = "INSERT INTO ".TB_PREF."tax_groups (name) VALUES (".db_escape($name).")";
    db_query($sql, "could not add tax group");

    $id = db_insert_id();

    add_tax_group_items($id, $taxes, $tax_shippings);

    commit_transaction();
}

function add_supplier_($supp_name, $supp_ref, $address, $supp_address, $gst_no,$ntn_no,
	$website, $supp_account_no, $bank_account, $credit_limit, $dimension_id, $dimension2_id, 
	$curr_code, $payment_terms, $payable_account, $purchase_account, $payment_discount_account, 
	$notes, $tax_group_id, $tax_included, $supplier_id)
{
	$sql = "INSERT INTO ".TB_PREF."suppliers(supplier_id, supp_name, supp_ref, address, supp_address, gst_no,ntn_no, website,
		supp_account_no, bank_account, credit_limit, dimension_id, dimension2_id, curr_code,
		payment_terms, payable_account, purchase_account, payment_discount_account, notes, 
		tax_group_id, tax_included, service_text)
		VALUES (".db_escape($supplier_id). ", "
		.db_escape($supp_name). ", "
		.db_escape($supp_ref). ", "
		.db_escape($address) . ", "
		.db_escape($supp_address) . ", "
		.db_escape($gst_no). ", "
		.db_escape($ntn_no). ", "
		.db_escape($website). ", "
		.db_escape($supp_account_no). ", "
		.db_escape($bank_account). ", "
		.$credit_limit. ", "
		.db_escape($dimension_id). ", "
		.db_escape($dimension2_id). ", "
		.db_escape($curr_code). ", "
		.db_escape($payment_terms). ", "
		.db_escape($payable_account). ", "
		.db_escape($purchase_account). ", "
		.db_escape($payment_discount_account). ", "
		.db_escape($notes). ", "
		.db_escape($tax_group_id). ", "
		.db_escape($tax_included). ", "
		.db_escape($service_text). ")";

	db_query($sql,"The supplier could not be added");
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

	//marina work start--------------------
	if ($etype == 8) {
		$fname = "suppliers.csv";
		$sql = "SELECT  'SUPPLIER' AS 
              type , s.supplier_id, s.supp_name, s.supp_ref, s.address, s.supp_address, s.ntn_no, s.gst_no, 
              cur.curr_abrev, pt.terms, t.name AS tax_group, p.phone, p.phone2, p.fax, p.email
              
FROM 0_suppliers AS s
LEFT JOIN 0_currencies cur ON s.`curr_code` = cur.`curr_abrev` 
LEFT JOIN 0_payment_terms pt ON s.`payment_terms` = pt.`terms_indicator` 
LEFT JOIN 0_tax_groups t ON s.`tax_group_id` = t.`id`
LEFT JOIN 0_crm_contacts con ON s.`supplier_id` = con.`entity_id`
LEFT JOIN 0_crm_persons p ON con.`person_id` = p.`id`

GROUP BY s.supplier_id";
	}

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

		$lines = $i = $j = $k = $b = $u = $p = $pr = $dm_n = $sp  = 0;

		while ($data = fgetcsv($fp, 4096, $sep)) {
			if ($lines++ == 0) continue;


				list($type, $supplier_id, $supp_ref, $supp_ref, $address, $supp_address, $ntn_no,
					$gst_no, $curr_abrev, $terms, $taxgroup_name, $phone,
                    $phone2, $fax, $email) = $data;

			//marina----for supplier

			if ($type == 'SUPPLIER') {

                //currency
                $sql = "SELECT currency, curr_abrev FROM " . TB_PREF . "currencies 
				WHERE curr_abrev='$curr_abrev'";
                $result = db_query($sql, "could not get customer items");
                $row = db_fetch_row($result);
                if (!$row) {
                    add_currency($curr_abrev, $curr_abrev, $curr_abrev, $curr_abrev, $curr_abrev, '');
                    $currency_id = $curr_abrev;
                } else $currency_id = $curr_abrev;

                //paymenterms
                $sql = "SELECT 	terms_indicator, terms FROM " . TB_PREF . "payment_terms 
				WHERE terms='$terms'";
                $result = db_query($sql, "could not get customer items");
                $row = db_fetch_row($result);
                if (!$row) {
                    add_payment_terms($payment_terms_id, $terms, $dayNumber);
                    $payment_terms_id = db_insert_id();
                } else
                    $payment_terms_id = $row[0];

                //taxgroup
                $sql = "SELECT 	id, name FROM " . TB_PREF . "tax_groups 
				WHERE name='$taxgroup_name'";
                $result = db_query($sql, "could not get customer items");
                $row = db_fetch_row($result);
                if (!$row) {
                    add_tax_group_new($taxgroup_name, $taxes, $tax_shippings);
                    $tax_groups_id = db_insert_id();
                } else $tax_groups_id = $row[0];

//----------------------------------------------------------------------------------------//

                $sql = "SELECT supplier_id, supp_ref FROM " . TB_PREF . "suppliers
				 WHERE supp_ref='$supp_ref'";
                $result = db_query($sql, "could not get supplier items");
                $row = db_fetch_row($result);
                if (!$row) {
                    add_supplier_($supp_ref, $supp_ref, $address, $supp_address,$gst_no, $ntn_no,
                        $website, $supp_account_no, $bank_account, 0, $dimension_id,
                        $dimension2_id, $currency_id, $payment_terms_id, $payable_account, $purchase_account,
                        $payment_discount_account, $notes, $tax_groups_id, $tax_included, $supplier_id,  $supplier_id);
                    $selected_branch = db_insert_id();
                } else $selected_branch = $row[0];

//-----------------------------------------------------------------------------------------------------//

                $sql = "SELECT id, ref FROM ".TB_PREF."crm_persons
						WHERE ref='$supp_ref'";
                $result = db_query($sql, "could not get customer items");
                $row = db_fetch_row($result);
                if (!$row) {
                    add_crm_person_new($supp_ref, $supp_ref, $name2, $address, $phone,
                        $phone2, $fax, $email, $lang, $notes,
                        $cat_ids=null, $entity=null);

                    $per_max = get_person_crm_max();
                    if($per_max==0)
                        $pers_id_ = 1;
                    else
                        $pers_id_ = $per_max;
                }else $pers_id_ = $row[0];

//-----------------------------------------------------------------------------------------------------//
                $sql = "SELECT id FROM ".TB_PREF."crm_contacts
						WHERE id='$pers_id_'";
                $result = db_query($sql, "could not get customer items");
                $row = db_fetch_row($result);
                if (!$row) {
                    add_crm_contact_new('cust_branch','general',$selected_branch, $pers_id_);
                }else $pers_id_ = $row[0];
            }

				else $supp = $row[0];
				$sp++;

		}
		@fclose($fp);

		if ($sp > 0) display_notification("$sp Supplier Data added or updated");

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

    $company_record = get_company_prefs();

    if (!isset($_POST['inventory_account']) || $_POST['inventory_account'] == "")
   	$_POST['inventory_account'] = $company_record["default_inventory_act"];

    if (!isset($_POST['cogs_account']) || $_POST['cogs_account'] == "")
   	$_POST['cogs_account'] = $company_record["default_cogs_act"];

    if (!isset($_POST['sales_account']) || $_POST['sales_account'] == "")
	$_POST['sales_account'] = $company_record["default_inv_sales_act"];

    if (!isset($_POST['adjustment_account']) || $_POST['adjustment_account'] == "")
	$_POST['adjustment_account'] = $company_record["default_adj_act"];

    if (!isset($_POST['wip_account']) || $_POST['wip_account'] == "")
	$_POST['wip_account'] = $company_record["default_wip_act"];
    if (!isset($_POST['sep']))
	$_POST['sep'] = ",";

    gl_all_accounts_list_row("Sales Account:", 'sales_account', $_POST['sales_account']);
    gl_all_accounts_list_row("Inventory Account:", 'inventory_account', $_POST['inventory_account']);
    gl_all_accounts_list_row("C.O.G.S. Account:", 'cogs_account', $_POST['cogs_account']);
    gl_all_accounts_list_row("Inventory Adjustments Account:", 'adjustment_account', $_POST['adjustment_account']);
    gl_all_accounts_list_row("Item Assembly Costs Account:", 'wip_account', $_POST['wip_account']);

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

<option value='8'>Suppliers</option>

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
