<?php
/**********************************************
Author: Joe Hunt
Author: Tom Moulton - added Export of many types and import of the same
Name: Import of CSV formatted items
Free software under GNU GPL
 ***********************************************/
$page_security = 'SA_CUSTOMER';
$path_to_root="../..";

include_once($path_to_root . "/sales/includes/cart_class.inc");

include($path_to_root . "/includes/session.inc");

add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/sales/includes/sales_db.inc");
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

$action = 'import';
if (isset($_GET['action'])) $action = $_GET['action'];
if (isset($_POST['action'])) $action = $_POST['action'];

if (isset($_POST['export']))
{
	$etype = 0;
	if (isset($_POST['export_type'])) $etype = $_POST['export_type'];
	$sales_type_id = 0;
	if (isset($_POST['sales_type_id'])) $sales_type_id = $_POST['sales_type_id'];
	$currency = "USD";
	if (isset($_POST['currency'])) $currency = $_POST['currency'];

		$fname = "OBInventorySample.csv";
		$sql = "SELECT master.stock_id, master.description, master.units, master.units, '1' as qty, '' as amount  
				FROM ".TB_PREF."stock_master master
				WHERE master.mb_flag != 'D'
				ORDER BY master.stock_id";
	$result = db_query($sql, "Could not select csv data");
	if (db_num_rows($result) > 0) {
		// header('Content-type: application/vnd.ms-excel');
		header('Content-type: text/x-csv');
		header('Content-Disposition: attachment; filename='.$fname);
		$i = 0;
		while ($csv = db_fetch_assoc($result)) {
			$hdr = '';
			$str = '';
			$csv = str_replace(',', '', $csv);

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

page("Import of CSV OB Files");

if (isset($_POST['import'])) {

	if (isset($_FILES['imp']) && $_FILES['imp']['name'] != '') {
		$filename = $_FILES['imp']['tmp_name'];
		$sep = ',';
//		$sep = $_POST['sep'];

		$fp = @fopen($filename, "r");


		if (!$fp)
			die("can not open file $filename");

		$lines = $i = $j = $k = $b = $u = $p = $pr = $dm_n = 0;
		// type, item_code, stock_id, description, category, units, qty, mb_flag, currency, price
        $adj_id = get_next_trans_no(ST_INVADJUST);
        $date_ = $_POST['AdjDate'];
        $movement_types = $_POST['type'];
        $increase = $_POST['Increase'];
        $reference = $Refs->get_next(ST_INVADJUST); //$_POST['ref'];
        $loc_code = $_POST["location"];
        $memo_ = $_POST['memo_'];
        $OutStock = array();
        unset($OutStock);


        while ($data = fgetcsv($fp, 4096, $sep)) {
            if ($lines++ == 0) continue;
            list($id, $description, $units, $quantity, $price) = $data;
            $type = strtoupper($type);
            $mb_flag = strtoupper($mb_flag); 
            $item = get_item($id);
             if(strlen($item['stock_id']) == 0) {
			    $stock_name = $id.'-'.$description;
                $OutStock[] = $stock_name;
            }
            add_stock_import_adjustment_item($adj_id, $item, $loc_code, $date_, $movement_types,
                $reference, $quantity, $price, $memo_);

            display_notification("Line $lines: Added $adj_id");
        }
        add_comments(ST_INVADJUST, $adj_id, $date_, $memo_);
        $Refs->save(ST_INVADJUST, $adj_id, $reference);
        add_audit_trail(ST_INVADJUST, $adj_id, $date_);
        
        if($OutStock)
            foreach ($OutStock as $outstockr => $out)
                display_notification("These stocks have no data. Please add and reload CSV file.".$out);
		else
            display_notification("These stocks have been uploaded");
		@fclose($fp);

		if ($i+$j > 0) display_notification("$i item posts created, $j item posts updated.");
		if ($dim_n > 0) display_notification("$dim_n Item Dimensions added.");
		if ($k > 0) display_notification("$k sales kit components added or updated.");
		if ($b > 0) display_notification("$b BOM components added or updated.");
		if ($u > 0) display_notification("$u Units of Measure added or updated.");
		if ($p > 0) display_notification("$p Purchasing Data items added or updated.");
		if ($pr > 0) display_notification("$pr Prices items added or updated for " . $_POST['sales_type_id']);

	} else display_error("No CSV file selected");
}

//if ($action == 'import') echo 'Import';
//else hyperlink_params($_SERVER['PHP_SELF'], _("Import"), "action=import", false);
//echo '&nbsp;|&nbsp;';
//	if ($action == 'export') echo 'Export';
//
//	else hyperlink_params($_SERVER['PHP_SELF'], _("Export"), "action=export", false);
//hidden('action', 'export');
//submit_center('exports', "Export CSV File");

//echo "<br><br>";

if ($action == 'import') {
	start_form(true);
	submit_center('export', "Download Sample File");
	echo "<br>";
	start_table(TABLESTYLE2, "width=40%");

	table_section_title("Import CSV  Inventory Adjustment");

	$company_record = get_company_prefs();

	if (!isset($_POST['inventory_account']) || $_POST['inventory_account'] == "")
		$_POST['inventory_account'] = $company_record["default_inventory_act"];

	if (!isset($_POST['cogs_account']) || $_POST['cogs_account'] == "")
		$_POST['cogs_account'] = $company_record["default_cogs_act"];

	if (!isset($_POST['sales_account']) || $_POST['sales_account'] == "")
		$_POST['sales_account'] = $company_record["default_inv_sales_act"];

	if (!isset($_POST['adjustment_account']) || $_POST['adjustment_account'] == "")
		$_POST['adjustment_account'] = $company_record["default_adj_act"];

	if (!isset($_POST['assembly_account']) || $_POST['assembly_account'] == "")
		$_POST['assembly_account'] = $company_record["default_assembly_act"];
	if (!isset($_POST['sep']))
		$_POST['sep'] = ",";

//	gl_all_accounts_list_row("Sales Account:", 'sales_account', $_POST['sales_account']);
//	gl_all_accounts_list_row("Inventory Account:", 'inventory_account', $_POST['inventory_account']);
//	gl_all_accounts_list_row("C.O.G.S. Account:", 'cogs_account', $_POST['cogs_account']);
//	gl_all_accounts_list_row("Inventory Adjustments Account:", 'adjustment_account', $_POST['adjustment_account']);
//	gl_all_accounts_list_row("Item Assembly Costs Account:", 'assembly_account', $_POST['assembly_account']);

//	table_section_title("Separator, Location, Tax and Sales Type");
//	text_row("Field separator:", 'sep', $_POST['sep'], 2, 1);
	locations_list_row("Location:", 'location', null);
//	item_tax_types_list_row("Item Tax Type:", 'tax_type_id', null);
    date_row(_("Date:"), 'AdjDate', '', true, 0, 0, 0, "", true);
    
    movement_types_list_row(_("Detail:"), 'type', null);
    if (!isset($_POST['Increase'])) $_POST['Increase'] = 1;
    yesno_list_row(_("Type:"), 'Increase', $_POST['Increase'], _("Positive Adjustment"), _("Negative Adjustment"));
    text_row(_("Trans No"), "trans_no", get_next_trans_no(ST_INVADJUST));
    // ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_INVADJUST));
    ref_row(_("Reference:"), 'ref', '', null, false, ST_INVADJUST, array('date'=> @$_POST['AdjDate']));
    textarea_row(_("Memo"), 'memo_', null, 50, 3);
    //asad
    label_row("CSV Import File:", "<input type='file' id='imp' name='imp'>");

	end_table(1);

	submit_center('import', "Import CSV File");
	hidden('action', 'import');

	end_form();
}
if ($action == 'import')
{
	start_form(true);

//	start_table(TABLESTYLE2, "width=40%");

	$company_record = get_company_prefs();
	$currency = $company_record["curr_default"];
	hidden('currency', $currency);

//	table_section_title("Export Selection");
	?>
<!--	<tr>-->
<!--		<td>Export Type:</td>-->
<!--		<td><select  name='export_type' class='combo' title='' >-->
<!--				<option value='1'>Item</option>-->
<!--				<option value='2'>Price List</option>-->
<!--				<option value='3'>Purchase Price</option>-->
<!--				<option value='4'>Units of Measure</option>-->
<!--				<option value='5'>Kit</option>-->
<!--				<option value='6'>Bill of Materials</option>-->
<!--				<option value='7'>Foreign Item Codes</option>-->
<!--			</select>-->
<!--		</td>-->
<!--	</tr>-->
	<?php
//	sales_types_list_row("Sales Type (for Price Lists):", 'sales_type_id', null);

	end_table(1);



	end_form();
}

end_page();
?>
