<?php
/**********************************************
Author: Joe Hunt
Author: Tom Moulton - added Export of many types and import of the same
Name: Import of CSV formatted items
Free software under GNU GPL
 ***********************************************/
$page_security = 'SA_CUSTOMER';
$path_to_root="../..";

include_once($path_to_root . "/POS/includes/cart_class.inc");

include($path_to_root . "/includes/session.inc");

add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

//include_once($path_to_root . "/POS/includes/sales_db.inc");
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


//	if ($etype == 6) 
	{
		$fname = "OBCustSample.csv";
		$sql = "SELECT master.debtor_no, branch.branch_code, master.name, branch.br_name, DATE_FORMAT(CURDATE(),'%d-%m-%Y') as date, 'OB' as code, '1' as qty, '' as amount, '' as optional_reference, '' as optional_memo
				FROM ".TB_PREF."debtors_master master 
				INNER JOIN ".TB_PREF."cust_branch branch ON master.debtor_no = branch.debtor_no 
				ORDER BY master.debtor_no";
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
        $OutCustomers = array();
        unset($OutCustomers);
        $ref = '';
		while($data = fgetcsv($fp, 4096, $sep))
		{


		    // payment terms not fetch, work on it
			if ($lines++ == 0) continue;
			list($customer, $date, $time, $stock_id, $stock_code, $description, $qty, $price, $item_discount, $total_discount, $gst, $reference, $comments, $location, $pos_counter, $pos_shift, $dimension_id, $salesperson, $cash_account) = $data;

            $sql = "SELECT COUNT(*) as TotalRecord FROM 0_stock_master WHERE stock_id = ".db_escape($stock_id);
            $query = db_query($sql, "Error");
            $fetch = db_fetch($query);
            if($fetch['TotalRecord'] == 0)
            {
                $OutCustomers[] = $stock_id;
//                continue;
            }

			//			if($qty < 0)
//			    continue;
            if($stock_id == '#SALE-TAX2')
                $StockAllow = 1;
            elseif($stock_id == '#SALE-TIPRND')
                $StockAllow = 2;
            elseif($stock_id == '#$$-1PKR')
                $StockAllow = 3;
            elseif($stock_id == '#$$-2MC')
                $StockAllow = 5;
            elseif($stock_id == '#DISC-TTL')
                $StockAllow = 6;
            elseif($stock_id == '#$$-2VISA')
                $StockAllow = 7;
            else
                $StockAllow = 4;
            if($ref != $reference)
                $trans_no = get_next_trans_no(ST_SALESORDER);
//            $ref = $reference;
			$customer_id = get_debtorno_from_customer_table_pos($customer);
            $checked = check_duplication($trans_no, $stock_id);
            $Item = get_stock_master_info_pos($stock_id);
			/*if($customer_id['customer_id'] == 0 || $checked > 0 || !$Item['stock_id']) {

			    if($customer_id['customer_id'] == 0) {
                    $cust_name = $customer.'-'.$name;
                    $OutCustomers[] = $cust_name;
                }
                if($Item['stock_id'] == '')
                    $OutCustomers[] = $stock_id;
//              1 = return sales invoice error.
//              2 = return sales orders error.
                if($checked == 1)
                    $ShowAfterUpload = 'Trans#'.$trans_no.' Already exist in sales orders';
                elseif($checked == 2)
                    $ShowAfterUpload = 'Trans#'.$trans_no.' Already exist in debtor trans';
                $OutCustomers[] = $ShowAfterUpload;
                if($OutCustomers)
                    foreach ($OutCustomers as $outCustomer => $out)
                        display_error("There is no data. Please add and reload CSV file. ".$out);
                break;
            }*/
			$order_no = add_sales_order_import_pos($customer_id['customer_id'], $customer_id['branch_id'],
            $date, $stock_id, $qty, $Item['description'], $price, $customer_id, $_POST['payment_terms'], $_POST['sales_type_id'],
            $Item, $comments, $trans_no, $location, $dimension_id, $description, $StockAllow, $time, $pos_counter, $pos_shift, $salesperson, $item_discount);
            $sql1 = "SELECT SUM(1 * (1 - `discount_percent`) * `unit_price` * `quantity`) as Total FROM ".TB_PREF."sales_order_details WHERE order_no=$order_no ";
            $result1 = db_query($sql1,"item could not be retrieved");
            $t_price = db_fetch_row($result1);
            $sql1 = "SELECT (ov_amount+ov_gst) FROM ".TB_PREF."debtor_trans WHERE trans_no=$order_no";
            $sql1 .= " AND type = 10";
            $result1 = db_query($sql1,"item could not be retrieved");
            $gst_price = db_fetch_row($result1);
			$delivery_no = write_sales_delivery_import_pos($customer_id['customer_id'], $customer_id['branch_id'],
            $date, $stock_id, $qty, $Item['description'], $price, $order_no, $_POST['payment_terms'], $_POST['sales_type_id'],
            $Item, $customer_id['customer_id'], $comments,$trans_no, $location, $dimension_id, $t_price[0], $StockAllow, $item_discount);
			$invoice_no = write_sales_invoice_import_pos($customer_id['customer_id'], $customer_id['branch_id'],
            $date, $stock_id, $qty, $Item['description'], $price, $delivery_no, $order_no, $_POST['payment_terms'], $_POST['sales_type_id'], $Item, $customer_id, $reference,
            $comments, $trans_no, $location, $dimension_id, $cash_account, $t_price[0], $StockAllow, $item_discount);
            $branch_data = get_branch_accounts($customer_id['branch_id']);
            $TotalAmount = $qty*$price;
            $stock_gl_code = get_stock_gl_code($stock_id);
            $sales_account = ($branch_data['sales_account'] != "" ? $branch_data['sales_account'] : $stock_gl_code['sales_account']);
            $dim = $dimension_id;
            $dim2 = 0;
//            $sql1 = " UPDATE ".TB_PREF."gl_trans SET amount = '$t_price[0]'
//                    WHERE type_no = $order_no
//                    AND type = ".db_escape(ST_SALESINVOICE)."
//                    AND account = ".db_escape($branch_data["receivables_account"]);
////                db_query($sql1, "order Details Cannot be Added");
//            if($StockAllow == 1) {
//                $sql1 = " UPDATE ".TB_PREF."gl_trans SET amount = '$TotalAmount'
//                    WHERE type_no = $order_no
//                    AND type = ".db_escape(ST_SALESINVOICE)."
//                    AND account = ".db_escape(201004);
////                db_query($sql1, "order Details Cannot be Added");
//            }
//            $sql8 = " UPDATE ".TB_PREF."gl_trans SET amount = '".-($t_price[0])."'
//                    WHERE type_no = $order_no
//                    AND type = ".db_escape(ST_SALESINVOICE)."
//                    AND account = ".db_escape($sales_account);
////                db_query($sql8, "order Details Cannot be Added");
//            if($StockAllow == 1){
//                $sql8 = " UPDATE ".TB_PREF."gl_trans SET amount = amount + '$TotalAmount'
//                    WHERE type_no = $order_no
//                    AND type = ".db_escape(ST_SALESINVOICE)."
//                    AND account = ".db_escape($sales_account);
////                db_query($sql8, "order Details Cannot be Added");
//            }
            $sql1 = " UPDATE ".TB_PREF."gl_trans SET amount = '$gst_price[0]'
                    WHERE type_no = $order_no
                    AND type = ".db_escape(ST_CUSTPAYMENT)."
                    AND account = ".db_escape($cash_account);
                db_query($sql1, "order Details Cannot be Added");
            $debtors_account = $branch_data["receivables_account"];
            $sql7 = " UPDATE ".TB_PREF."gl_trans SET amount = '".-($gst_price[0])."'
                    WHERE type_no = $order_no
                    AND type = ".db_escape(ST_CUSTPAYMENT)."
                    AND account = ".db_escape($debtors_account);
                db_query($sql7, "order Details Cannot be Added");
            $sql2 = " UPDATE ".TB_PREF."debtor_trans SET ov_amount = '$t_price[0]' WHERE trans_no = $order_no ";
                db_query($sql2, "order Details Cannot be Added");
            $sql3 = " UPDATE ".TB_PREF."debtor_trans SET alloc = '$t_price[0]' WHERE trans_no = $order_no AND type = '12'";
                db_query($sql3, "order Details Cannot be Added");
            $sql4 = " UPDATE ".TB_PREF."debtor_trans SET alloc = '$t_price[0]' WHERE trans_no = $order_no AND type = '10'";
                db_query($sql4, "order Details Cannot be Added");
            $sql5 = " UPDATE ".TB_PREF."bank_trans SET amount = '$t_price[0]' WHERE trans_no = $order_no";
                db_query($sql5, "order Details Cannot be Added");
            $sql6 = " UPDATE ".TB_PREF."cust_allocations SET amt = '$t_price[0]' WHERE trans_no_to = $order_no AND trans_type_to = 10";
                db_query($sql6, "order Details Cannot be Added");
//            if($StockAllow == 4)
            {
                $sql2 = "SELECT (ov_amount+ov_gst) as Total, ov_amount as Price, ov_gst as GSTPrice FROM 0_debtor_trans WHERE type = 10 AND trans_no = ".db_escape($trans_no);
                $query = db_query($sql2, "Error");
                $fetch = db_fetch($query);
                $sql1 = " UPDATE ".TB_PREF."debtor_trans 
                    SET alloc = ".db_escape($fetch['Total'])."
                    WHERE trans_no = $trans_no
                    AND type = ".db_escape(ST_SALESINVOICE);
                db_query($sql1, "order Details Cannot be Added");
//                $sql2 = "SELECT (ov_amount+ov_gst) as Total FROM 0_debtor_trans WHERE type = 10 AND trans_no = ".db_escape($trans_no);
//                $query = db_query($sql2, "Error");
//                $fetch = db_fetch($query);
                $sql1 = " UPDATE ".TB_PREF."debtor_trans 
                    SET alloc = ".db_escape($fetch['Total']).",
                    ov_amount = ".db_escape($fetch['Total'])."
                    WHERE trans_no = $trans_no
                    AND type = ".db_escape(ST_CUSTPAYMENT);
                db_query($sql1, "order Details Cannot be Added");
                $sql1 = " UPDATE ".TB_PREF."cust_allocations 
                    SET amt = ".db_escape($fetch['Total'])."
                    WHERE trans_no_to = $trans_no
                    AND trans_type_to = ".db_escape(ST_SALESINVOICE);
                db_query($sql1, "order Details Cannot be Added");
                $sql1 = " UPDATE ".TB_PREF."bank_trans 
                    SET amount = ".db_escape($fetch['Total'])."
                    WHERE trans_no = $trans_no
                    AND type = ".db_escape(ST_CUSTPAYMENT);
                db_query($sql1, "order Details Cannot be Added");
                $sql1 = " UPDATE ".TB_PREF."trans_tax_details 
                    SET net_amount = ".db_escape($fetch['Price'])."
                    WHERE trans_no = $trans_no
                    AND trans_type IN (10, 13)";
                db_query($sql1, "order Details Cannot be Added");
                if($fetch['GSTPrice'] != 0) {
                    $sql1 = " UPDATE ".TB_PREF."trans_tax_details 
                    SET amount = ".db_escape($fetch['GSTPrice']).",
                    tax_type_id = 1,
                    included_in_price = 0
                    WHERE trans_no = $trans_no AND trans_type IN (10, 13)";
                    db_query($sql1, "order Details Cannot be Added");
                }
            }
            $sql = "SELECT (quantity*unit_price) as NetAmount FROM ".TB_PREF."debtor_trans_details WHERE quantity > 0
                    AND debtor_trans_type = 10 
                    AND debtor_trans_no = ".db_escape($invoice_no);
            $query = db_query($sql, "Error");
            if($StockAllow == 4) {
//                $amount = $qty*$price;
//                if($qty > 0)
//                display_error($TotalAmount);
//                die;
                $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date, $sales_account, $dim, $dim2,
                    -$TotalAmount, $customer_id['customer_id'], "The sales price GL posting could not be inserted");
            }
            if($StockAllow == 4) {
//                $amount = $qty*$price;
//                if($qty > 0)
                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date, $branch_data["receivables_account"], $dim, $dim2,
                        $TotalAmount, $customer_id['customer_id'], "The sales price GL posting could not be inserted");
            }
//            if($StockAllow == 1) {
//                if($qty > 0)
//                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date, '201004', $dim, $dim2,
//                        -$TotalAmount, $customer_id, "The sales price GL posting could not be inserted");
//            }
//            if($StockAllow == 4) {
//                if($qty > 0)
//                    $total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date, $branch_data["receivables_account"], 0, 0,
//                        ($t_price[0]),
//                        $customer_id, "The total debtor GL posting could not be inserted");
//            }
            $ref = $reference;
			if(!$order_no)
				display_error('Could not be insert. '.$lines.'--'.$order_no);
			else
				display_notification("Records Successfully. ".$lines.'--'.$order_no);
		}
        if($OutCustomers)
            foreach ($OutCustomers as $outCustomer => $out)
                display_error("There is no data. Please add and reload CSV file. ".$out);
		else
            display_notification("Data have been uploaded");
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
//	submit_center('export', "Download Sample File");
	echo "<br>";
	start_table(TABLESTYLE2, "width=40%");

	table_section_title("Import CSV Customer Invoices");

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
//	locations_list_row("To Location:", 'location', null);
//	item_tax_types_list_row("Item Tax Type:", 'tax_type_id', null);

//	sales_types_list_row("Sales Type:", 'sales_type_id', null);
//	payment_term_list_cells("Payment Terms:", 'payment_terms', null);
    hidden('sales_type_id', 1);
    hidden('payment_terms', 4);
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
