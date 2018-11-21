<?php
/**********************************************
Author: Joe Hunt
Author: Tom Moulton - added Export of many types and import of the same
Name: Import of CSV formatted items
Free software under GNU GPL
 ***********************************************/
$page_security = 'SA_SUPPLIER';
$path_to_root="../..";

include_once($path_to_root . "/purchasing/includes/po_class.inc");

include($path_to_root . "/includes/session.inc");

add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");
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

    /*	if ($etype == 1) {
            $fname = "items.csv";
            $sql = "SELECT 'ITEM' as type, sm.stock_id as item_code, sm.stock_id, sm.description, sc.description as category, sm.units, '' as dummy, sm.mb_flag, '' as price, d.name as dimension FROM ".TB_PREF."stock_master sm left join ".TB_PREF."stock_category sc on sm.category_id = sc.category_id left join ".TB_PREF."dimensions d on sm.dimension_id = d.id left join ".TB_PREF."prices as p on sm.stock_id = p.stock_id WHERE sm.inactive = 0";
        }
        if ($etype == 2) {
            $fname = "prices_$sales_type_id.csv";
            $sql = "SELECT 'PRICE' as type, sm.stock_id, '' as dummy1, '' as dummy2, '' as dummy3, '' as dummy4, '' as dummy5, '' as dummy6, p.curr_abrev as currency, p.price FROM ".TB_PREF."stock_master sm left join ".TB_PREF."prices p on sm.stock_id = p.stock_id where sm.inactive = 0 AND p.sales_type_id = $sales_type_id";
        }
        if ($etype == 3) {
            $fname = "supp_prices.csv";
            $sql = "SELECT 'BUY' as type, pd.stock_id, '' as dummy, pd.supplier_description, s.supp_name as supplier, pd.suppliers_uom, pd.conversion_factor, '' as dummy1, '" . $currency ."', pd.price from ".TB_PREF."purch_data pd left join ".TB_PREF."suppliers s on pd.supplier_id = s.supplier_id where 1";
        }
        if ($etype == 4) {
            $fname = "uom.csv";
            $sql = "SELECT 'UOM' as type, abbr, name, '' as dummy1, '' as dummy2, '' as dummy3, decimals, '' as dummy4, '' as dummy5, '' as dummy6 FROM ".TB_PREF."item_units WHERE `inactive` = 0";
        }
        if ($etype == 5) {
            $fname = "kits.csv";
            $sql = "SELECT 'KIT' as type, i.item_code, i.stock_id, i.description, sc.description as category, '' as dummy, i.quantity, '' as dummy1, '' as dummy2, '' as dummy3 FROM ".TB_PREF."item_codes i left join ".TB_PREF."stock_category sc on i.category_id = sc.category_id WHERE i.item_code <> i.stock_id and i.is_foreign = 0 and i.inactive = 0";
        }*/
//	if ($etype == 6)
    {
        $fname = "OBSuppSample.csv";
        $sql = "SELECT supplier_id, supp_name, DATE_FORMAT(CURDATE(),'%d-%m-%Y') as date, 'OB' as code, '1' as qty, '' as amount  
				FROM ".TB_PREF."suppliers  
				ORDER BY supplier_id";
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
        $OutSuppliers = array();
        unset($OutSuppliers);
        while($data = fgetcsv($fp, 4096, $sep))
        {
            if ($lines++ == 0) continue;

            list($supplier_id, $supplier_name, $date, $stock_id, $qty, $price) = $data;

            $supplier_id = get_id_from_supplier_table($supplier_id);

            if($supplier_id['supplier_id'] == 0){
                
               	$supp_name = $supplier_id.'-'.$supplier_name;
                $OutSuppliers[] = $supp_name;
                    continue;
            }
            $Item = get_stock_master_info_purchase($stock_id);
//          Purchase Order
            $order_no = add_OB_import_po($supplier_id, $date, $Item,
            $qty, $price, $_POST['location'], $_POST['payment_terms'],
            $_POST['dimension_id'], $_POST['dimension2_id']);
//          Good Receive Note
            $grn_no = add_OB_import_grn($order_no, $supplier_id, $date, $Item,
                $qty, $price, $_POST['location'], $_POST['payment_terms'],
                $_POST['dimension_id'], $_POST['dimension2_id']);
//          Supplier Invoice
            $inv_no = add_import_OB_supp_invoice($grn_no, $order_no, $supplier_id, $date, $Item,
                $qty, $price, $_POST['location'], $_POST['payment_terms'],
                $_POST['dimension_id'], $_POST['dimension2_id']);
//          $invoice_no = add_supplier_OB_invoices($supplier_id['supplier_id'], $Item, $qty, $date, 3012,0,0, $price, 0, '');

            if(!$inv_no)
                display_error('Could not be insert. '.$inv_no.'---'.$order_no);
            else
                display_notification("Records Successfully. ".$inv_no.'---'.$order_no);
        }
        		
        if($OutSuppliers)
            foreach ($OutSuppliers as $outsupplier => $out)
                display_notification("These Suppliers have no data. Please add and reload CSV file.".$out);
		else
            display_notification("These Suppliers have been uploaded");
	
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

    table_section_title("Import CSV Supplier Opening");

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
	locations_list_row("To Location:", 'location', null);
//	item_tax_types_list_row("Item Tax Type:", 'tax_type_id', null);
//    sales_types_list_row("Sales Type:", 'sales_type_id', null);
    payment_term_list_cells("Payment Terms:", 'payment_terms', null);
    dimensions_list_row("Dimension 1:", 'dimension_id', null, true, " ", false, 1);
    dimensions_list_row("Dimension 2:", 'dimension2_id', null, true, " ", false, 2);

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
