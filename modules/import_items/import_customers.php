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
function add_credit_status_new($description, $disallow_invoicing)
{
	$sql = "INSERT INTO ".TB_PREF."credit_status (reason_description, dissallow_invoices) 
		VALUES (".db_escape($description).",".db_escape($disallow_invoicing).")";

	db_query($sql, "could not add credit status");
}
function add_shipper_new($shipper_name, $contact, $phone, $phone2, $address)
{
	$sql = "INSERT INTO ".TB_PREF."shippers (shipper_name, contact, phone, phone2, address)
		VALUES (" . db_escape($shipper_name) . ", " .
		db_escape($contact). ", " .
		db_escape($phone). ", " .
		db_escape($phone2). ", " .
		db_escape($address) . ")";

	db_query($sql,"The Shipping Company could not be added");
}
function add_item_location_new($loc_code, $location_name, $delivery_address, $phone, $phone2, $fax, $email, $contact, $fixed_asset = 0)
{
	$sql = "INSERT INTO ".TB_PREF."locations (loc_code, location_name, delivery_address, phone, phone2, fax, email, contact, fixed_asset)
		VALUES (".db_escape($loc_code).", ".db_escape($location_name).", ".db_escape($delivery_address).", "
		.db_escape($phone).", ".db_escape($phone2).", ".db_escape($fax).", ".db_escape($email).", "
		.db_escape($contact).", ".db_escape($fixed_asset).")";

	db_query($sql,"a location could not be added");

	/* Also need to add loc_stock records for all existing items */
	$sql = "INSERT INTO ".TB_PREF."loc_stock (loc_code, stock_id, reorder_level)
		SELECT ".db_escape($loc_code).", ".TB_PREF."stock_master.stock_id, 0 FROM ".TB_PREF."stock_master";

	db_query($sql,"a location could not be added");
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
function add_sales_group_new($description)
{
	$sql = "INSERT INTO ".TB_PREF."groups (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The sales group could not be added");
}
function add_customer_($CustName, $cust_ref, $address, $tax_id, $curr_code,
	$dimension_id, $dimension2_id, $credit_status, $payment_terms, $discount, $pymt_discount, 
	$credit_limit, $sales_type, $notes, $ntn_no, $credit_allowed, $debtor_no, $DOB)
{
    $dob = sql2date($DOB);

	$sql = "INSERT INTO ".TB_PREF."debtors_master (debtor_no, name, debtor_ref, address, tax_id,
		dimension_id, dimension2_id, curr_code, credit_status, payment_terms, discount, 
		pymt_discount,credit_limit, sales_type, notes,ntn_no,service_text,credit_allowed, DOB) VALUES ("
		.db_escape($debtor_no).","
		.db_escape($CustName) .", " .db_escape($cust_ref) .", "
		.db_escape($address) . ", " . db_escape($tax_id) . ","
		.db_escape($dimension_id) . ", " 
		.db_escape($dimension2_id) . ", ".db_escape($curr_code) . ", 
		" . db_escape($credit_status) . ", ".db_escape($payment_terms) . ",
		" . db_escape($discount) . ", 
		" . db_escape($pymt_discount) . ", " . db_escape($credit_limit) 
		 .", ".db_escape($sales_type).", " . db_escape($notes) . ",
		 ".db_escape($ntn_no) . ",'-',".db_escape($credit_allowed) . ",
		 " . db_escape($dob) . ")";
	db_query($sql,"The customer could not be added");
}
function add_branch_($customer_id, $br_name, $br_ref, $br_address, $salesman, $area, 
	$tax_group_id, $sales_account, $sales_discount_account, $receivables_account, 
	$payment_discount_account, $default_location, $br_post_address, $group_no,
	$default_ship_via, $notes, $bank_account, $branch_no)
{
	$sql = "INSERT INTO ".TB_PREF."cust_branch (branch_code, debtor_no, br_name, branch_ref, br_address,
		salesman, area, tax_group_id, sales_account, receivables_account, payment_discount_account, 
		sales_discount_account, default_location,
		br_post_address, group_no, default_ship_via, notes, bank_account)
		VALUES (".db_escape($branch_no). ",".db_escape($customer_id). ",".db_escape($br_name) . ", "
			.db_escape($br_ref) . ", "
			.db_escape($br_address) . ", ".db_escape($salesman) . ", "
			.db_escape($area) . ","
			.db_escape($tax_group_id) . ", "
			.db_escape($sales_account) . ", "
			.db_escape($receivables_account) . ", "
			.db_escape($payment_discount_account) . ", "
			.db_escape($sales_discount_account) . ", "
			.db_escape($default_location) . ", "
			.db_escape($br_post_address) . ","
			.db_escape($group_no) . ", "
			.db_escape($default_ship_via). ", "
			.db_escape($notes). ", "
			.db_escape($bank_account, true).")";
	db_query($sql,"The branch record could not be added");
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
		$fname = "customers.csv";

		$sql = "SELECT 'CUSTOMER' AS 
           	type, c.debtor_no, b.branch_code, c.debtor_ref, b.branch_ref, c.address, c.tax_id,
           	c.ntn_no, cur.curr_abrev, pt.terms, st.sales_type, cs.reason_description AS credit_status,
           	s.salesman_name, loc.location_name, ship.shipper_name, a.description AS area,
           	t.name AS tax_group , g.description AS group_no, c.notes, p.phone, p.phone2, p.fax,
           	p.email, c.DOB, c.name
            FROM 0_debtors_master AS c
            LEFT JOIN 0_cust_branch b ON c.`debtor_no` = b.`debtor_no` 
            LEFT JOIN 0_currencies cur ON c.`curr_code` = cur.`curr_abrev` 
            LEFT JOIN 0_payment_terms pt ON c.`payment_terms` = pt.`terms_indicator` 
            LEFT JOIN 0_sales_types st ON c.`sales_type` = st.`id` 
            LEFT JOIN 0_credit_status cs ON c.`credit_status` = cs.`id` 
            LEFT JOIN 0_salesman s ON b.`salesman` = s.`salesman_code` 
            LEFT JOIN 0_locations loc ON b.`default_location` = loc.`loc_code` 
            LEFT JOIN 0_shippers ship ON b.`default_ship_via` = ship.`shipper_id` 
            LEFT JOIN 0_areas a ON b.`area` = a.`area_code` 
            LEFT JOIN 0_tax_groups t ON b.`tax_group_id` = t.`id` 
            LEFT JOIN 0_groups g ON b.`group_no` = g.`id` 
            LEFT JOIN 0_crm_contacts con ON b.`branch_code` = con.`entity_id`
            LEFT JOIN 0_crm_persons p ON con.`person_id` = p.`id`
            GROUP BY c.debtor_ref";


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

		$lines = $i = $j = $k = $b = $u = $p = $pr = $dm_n = $sp = $cust = 0;

		while ($data = fgetcsv($fp, 4096, $sep)) {
			if ($lines++ == 0) continue;
//var_dump($data);
			list($type, $debtor_ref, $branch_ref, $address, $tax_id, $ntn_no, $curr_abrev, $terms,
                $sales_type, $credit_status , $salesman_name, $location_name , $shipper_name,
                $area_name, $taxgroup_name, $group_name, $notes, $phone, $phone2, $fax, $email,
                $DOB, $name) = $data;


			if ($type == 'CUSTOMER')
			{
				//salesman
				$sql = "SELECT salesman_code, salesman_name FROM ".TB_PREF."salesman 
				WHERE salesman_name='$salesman_name'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_salesman($salesman_name, $salesman_phone, $salesman_fax,
						$salesman_email, $provision, $break_pt, $provision2);
					$salesman_id = db_insert_id();
				} else $salesman_id = $row[0];

				//currency
				$sql = "SELECT  curr_abrev FROM ".TB_PREF."currencies 
				WHERE curr_abrev='$curr_abrev'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_currency($curr_abrev, $curr_abrev, $curr_abrev, $curr_abrev,
						$curr_abrev, '');
					$currency_id = $curr_abrev;
				} else $currency_id = $curr_abrev;

				//location
				$sql = "SELECT loc_code, location_name FROM ".TB_PREF."locations 
				WHERE location_name='$location_name'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_item_location_new($location_name, $location_name, $delivery_address,
						$phone, $phone2, $fax, $email, $contact, $fixed_asset);
					$location_id = $location_name;
				} else $location_id = $location_name;

				//salesarea
				$sql = "SELECT 	area_code, description FROM ".TB_PREF."areas 
				WHERE description='$area_name'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_sales_area($area_name);
					$area_id = db_insert_id();
				} else $area_id = $row[0];

				//shipping_via
				$sql = "SELECT 	shipper_id, shipper_name FROM ".TB_PREF."shippers 
				WHERE shipper_name='$shipper_name'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_shipper_new($shipper_name, $contact, $phone, $phone2, $address);
					$ship_via_id = db_insert_id();
				} else $ship_via_id = $row[0];

				//salestype
				$sql = "SELECT 	id, sales_type FROM ".TB_PREF."sales_types 
				WHERE sales_type='$sales_type'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_sales_type($sales_type, $tax_included, $factor);
					$salestype_id = db_insert_id();
				} else $salestype_id = $row[0];

				//paymenterms
				$sql = "SELECT 	terms_indicator, terms FROM ".TB_PREF."payment_terms 
				WHERE terms='$terms'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_payment_terms($payment_terms_id, $terms, $dayNumber);
					$payment_terms_id = db_insert_id();
				} else
					$payment_terms_id = $row[0];

				//creditstatus
				$sql = "SELECT 	id, reason_description FROM ".TB_PREF."credit_status 
				WHERE reason_description='$credit_status'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_credit_status_new($credit_status, $disallow_invoicing);
					$credit_status_id = db_insert_id();
				} else $credit_status_id = $row[0];

				//taxgroup
				$sql = "SELECT 	id, name FROM ".TB_PREF."tax_groups 
				WHERE name='$taxgroup_name'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_tax_group_new($taxgroup_name, $taxes, $tax_shippings);
					$tax_groups_id = db_insert_id();
				} else $tax_groups_id = $row[0];

				//groupno
				$sql = "SELECT 	id, description FROM ".TB_PREF."groups
				WHERE description='$group_name'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_sales_group_new($group_name);
					$groups_id = db_insert_id();
				} else $groups_id = $row[0];


//-----------------------------------------------------------------------------------------------------//
				$sql = "SELECT debtor_no, debtor_ref FROM ".TB_PREF."debtors_master 
				WHERE debtor_ref='$debtor_ref'";
				$result = db_query($sql, "could not get customer items");
				$row = db_fetch_row($result);
				if (!$row) {
					add_customer_($name, $branch_ref, $address, $tax_id, $currency_id,
						$dimension_id, $dimension2_id, $credit_status_id, $payment_terms_id,
						$discount, $pymt_discount, $credit_limit, $salestype_id, $notes,
						$ntn_no, $credit_allowed, $debtor_no, $DOB );
					$customer_id = db_insert_id();
				} else $customer_id = $row[0];
//-----------------------------------------------------------------------------------------------------//
					if (isset($SysPrefs->auto_create_branch) && $SysPrefs->auto_create_branch == 1)
					{
						$sql = "SELECT branch_code, branch_ref FROM ".TB_PREF."cust_branch
						WHERE branch_ref='$branch_name'";
						$result = db_query($sql, "could not get customer items");
						$row = db_fetch_row($result);
						if (!$row) {
							add_branch_($customer_id, $name, $branch_ref, $address,
								$salesman_id, $area_id, $tax_groups_id,$_POST['sales_account'],
								$_POST['sales_discount_account'], $_POST['receivables_account'],
								$_POST['payment_discount_account'], $location_id, $br_post_address,
								$groups_id, $ship_via_id, $notes, $bank_account, $branch_no);
							$selected_branch = db_insert_id();
						}else $selected_branch = $row[0];
//-----------------------------------------------------------------------------------------------------//
						$sql = "SELECT id, ref FROM ".TB_PREF."crm_persons
						WHERE ref='$branch_ref'";
						$result = db_query($sql, "could not get customer items");
						$row = db_fetch_row($result);
						if (!$row) {
							add_crm_person_new($branch_ref, $branch_ref, $name2, $address, $phone,
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

				else $cus = $row[0];
				$cust++;
			}

		}
		@fclose($fp);
		if ($cust > 0) display_notification("$cust Customer Data added or updated");

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

	gl_all_accounts_list_row(_("Sales Account:"), 'sales_account', $_POST['sales_account'], false, false, true);
	gl_all_accounts_list_row(_("Sales Discount Account:"), 'sales_discount_account', $_POST['sales_discount_account']);
	gl_all_accounts_list_row(_("Accounts Receivable Account:"), 'receivables_account', $_POST['receivables_account'], true);
	gl_all_accounts_list_row(_("Prompt Payment Discount Account:"), 'payment_discount_account', $_POST['payment_discount_account']);

    table_section_title("Separator, Location, Tax and Sales Type");
	text_row("Field separator:", 'sep', $_POST['sep'], 2, 1);

//	sales_persons_list_row( _("Sales Person:"), 'salesman', $_POST['salesman']);
//	locations_list_row(_("Default Inventory Location:"), 'location', $_POST['location']);
//	sales_areas_list_row( _("Sales Area:"), 'area', $_POST['area']);
//	sales_types_list_row(_("Sales Type/Price List:"), 'sales_type', $_POST['sales_type']);
//	currencies_list_row(_("Customer's Currency:"), 'curr_code', $_POST['curr_code']);
//	shippers_list_row(_("Default Shipping Company:"), 'ship_via', $_POST['ship_via']);
//	payment_terms_list_row(_("Payment Terms:"), 'payment_terms', $_POST['payment_terms']);
//	credit_status_list_row(_("Credit Status:"), 'credit_status', $_POST['credit_status']);
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
<option value='9'>Customers</option>
</select>
</td>
</tr>
<?php

//    sales_types_list_row("Sales Type (for Price Lists):", 'sales_type_id', null);

    end_table(1);

    hidden('action', 'export');
    submit_center('export', "Export CSV File");

    end_form();
}

    end_page();
